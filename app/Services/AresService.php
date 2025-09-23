<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use SoapClient;

class AresService
{
    private const WSDL_URL = 'https://wwwinfo.mfcr.cz/ares/ares_obsluzne_vyslecht.wsdl';
    
    /**
     * Primary JSON endpoints tried in order (tests fake ares.gov.cz/* so any path works)
     */
    private const JSON_ENDPOINTS = [
        // Official REST that returns { icoId, zaznamy: [ { ico, obchodniJmeno, sidlo { ... } } ] }
        'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty-res/%s',
    ];
    
    /**
     * Get company data from ARES by IČO.
     */
    public function getCompanyData(string $ico): ?array
    {
        // Cache na 24 hodin - data se mění pomalu
        return Cache::remember("ares_company_{$ico}", 86400, function () use ($ico) {
            return $this->fetchFromAres($ico);
        });
    }
    
    /**
     * Fetch company data from ARES SOAP API.
     */
    private function fetchFromAres(string $ico): ?array
    {
        // 1) Try modern JSON API first (works with tests that fake ares.gov.cz/*)
        foreach (self::JSON_ENDPOINTS as $pattern) {
            $url = sprintf($pattern, urlencode($ico));
            try {
                $response = Http::timeout(15)
                    ->acceptJson()
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Accept-Language' => 'cs',
                        // Some government endpoints are strict about User-Agent formatting – keep it simple.
                        'User-Agent' => 'FreelanceFlow/1.0'
                    ])
                    ->get($url);

                if ($response->ok()) {
                    $json = $response->json();
                    $data = $this->parseJsonResponse($json);
                    if (!empty($data) && !empty($data['company_name'])) {
                        Log::info('ARES JSON data fetched successfully', [
                            'ico' => $ico,
                            'company_name' => $data['company_name'],
                            'endpoint' => $url,
                        ]);
                        return $data;
                    } else {
                        Log::warning('ARES JSON parsed empty or missing company_name', [
                            'ico' => $ico,
                            'endpoint' => $url,
                            'sample_keys' => is_array($json) ? array_slice(array_keys($json), 0, 5) : gettype($json),
                        ]);
                    }
                } else {
                    Log::warning('ARES JSON non-ok response', [
                        'ico' => $ico,
                        'endpoint' => $url,
                        'status' => $response->status(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('ARES JSON endpoint failed', [
                    'ico' => $ico,
                    'endpoint' => $url,
                    'error' => $e->getMessage(),
                ]);
                // try next endpoint
            }
        }

        // 2) Fallback to legacy SOAP API (best effort)
        if (!class_exists(\SoapClient::class)) {
            Log::warning('SOAP extension not available, skipping SOAP fallback');
            return null;
        }
        try {
            $client = new SoapClient(self::WSDL_URL, [
                'trace' => true,
                'exceptions' => true,
                'connection_timeout' => 10,
                'cache_wsdl' => WSDL_CACHE_DISK,
            ]);

            $result = $client->VyhledatSubjekt([
                'ico' => $ico,
                'dotaz' => $ico,
                'maxPocetZaznamu' => 1,
            ]);

            if (!$result || !isset($result->SubjektySubjekt)) {
                Log::warning('ARES SOAP response empty', [
                    'ico' => $ico,
                    'lastRequest' => method_exists($client, '__getLastRequest') ? $client->__getLastRequest() : null,
                    'lastResponse' => method_exists($client, '__getLastResponse') ? $client->__getLastResponse() : null,
                ]);
                return null;
            }

            $subjekt = is_array($result->SubjektySubjekt)
                ? $result->SubjektySubjekt[0]
                : $result->SubjektySubjekt;

            $data = $this->parseSoapResponse($subjekt);

            Log::info('ARES SOAP data fetched successfully', [
                'ico' => $ico,
                'company_name' => $data['company_name'] ?? 'unknown',
            ]);

            return $data;
        } catch (\Throwable $e) {
            Log::error('ARES SOAP fallback failed', [
                'ico' => $ico,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
    /**
     * Parse ARES SOAP response into standardized format.
     */
    private function parseSoapResponse(object $subjekt): array
    {
        $company = (array) $subjekt;
        
        if (empty($company)) {
            return [];
        }
        
        $address = $company['Sidlo'] ?? [];
        $legalForm = $company['PravniForma'] ?? [];
        
        return [
            'ico' => $company['ICO'] ?? '',
            'dic' => $company['DIC'] ?? null,
            'company_name' => $this->getCompanyName($company),
            'legal_form' => $legalForm['nazev'] ?? '',
            'address' => $this->formatAddress($address),
            'street' => $this->getStreet($address),
            'street_number' => $this->getStreetNumber($address),
            'city' => $address['nazevObce'] ?? '',
            'postal_code' => $address['PSC'] ?? '',
            'state' => 'Česká republika',
            'is_active' => $this->isCompanyActive($company),
            'business_activities' => $this->getBusinessActivities($company),
            'establishment_date' => $this->getEstablishmentDate($company),
            'court_registration' => $this->getCourtRegistration($company),
            'raw_data' => $company, // Store raw data for debugging
        ];
    }

    /**
     * Parse JSON response from modern ARES endpoints to standardized format.
     */
    private function parseJsonResponse($json): array
    {
        if (empty($json)) {
            return [];
        }

        // New REST returns { icoId, zaznamy: [ { ... } ] }
        if (isset($json['zaznamy']) && is_array($json['zaznamy']) && count($json['zaznamy']) > 0) {
            $subject = $json['zaznamy'][0];
        }
        // Some older endpoints return { ekonomickySubjekt: {...} }
        if (isset($json['ekonomickySubjekt'])) {
            $subject = $json['ekonomickySubjekt'];
        } elseif (isset($json['ekonomickeSubjekty']) && is_array($json['ekonomickeSubjekty']) && count($json['ekonomickeSubjekty']) > 0) {
            $subject = $json['ekonomickeSubjekty'][0];
        } elseif (isset($json['ico']) || isset($json['obchodniJmeno'])) {
            // Or directly the subject object
            $subject = $json;
        } else {
            return [];
        }

        $subject = (array) $subject;
        $address = isset($subject['sidlo']) ? (array) $subject['sidlo'] : [];

        $companyName = $subject['obchodniJmeno']
            ?? $subject['nazev']
            ?? '';

        $postal = $address['psc'] ?? $address['PSC'] ?? '';

        return [
            'ico' => $subject['ico'] ?? '',
            'dic' => $subject['dic'] ?? ($subject['DIC'] ?? null),
            'company_name' => $companyName,
            'legal_form' => $subject['pravniForma'] ?? ($subject['pravniFormaRos'] ?? ''),
            'address' => $this->formatAddressJson($address),
            'street' => $address['nazevUlice'] ?? ($address['nazevCastiObce'] ?? ''),
            'street_number' => $this->getStreetNumberJson($address),
            'city' => $address['nazevObce'] ?? '',
            'postal_code' => $postal,
            'state' => 'Česká republika',
            'is_active' => true, // JSON endpoint typically returns active subjects; refine if field available
            'raw_data' => $subject,
        ];
    }
    
    /**
     * Get company name from various possible fields.
     */
    private function getCompanyName(array $company): string
    {
        return $company['ObchodniJmeno']
            ?? $company['obchodniJmeno']
            ?? $company['nazev']
            ?? $company['jmeno']
            ?? $company['nazevFirmy']
            ?? '';
    }
    
    /**
     * Format complete address string.
     */
    private function formatAddress(array $address): string
    {
        $parts = [];
        
        $street = $this->getStreet($address);
        $streetNumber = $this->getStreetNumber($address);
        
        if ($street) {
            $streetPart = $street;
            if ($streetNumber) {
                $streetPart .= ' ' . $streetNumber;
            }
            $parts[] = $streetPart;
        }
        
        if (!empty($address['nazevObce'])) {
            $city = $address['nazevObce'];
            if (!empty($address['PSC'])) {
                $city = $address['PSC'] . ' ' . $city;
            }
            $parts[] = $city;
        }
        
        return implode(', ', $parts);
    }
    
    /**
     * Get street name from address.
     */
    private function getStreet(array $address): string
    {
        return $address['nazevUlice'] ?? $address['nazevCastiObce'] ?? '';
    }
    
    /**
     * Get street number from address.
     */
    private function getStreetNumber(array $address): string
    {
        $numbers = [];
        
        if (!empty($address['cisloDomovni'])) {
            $numbers[] = $address['cisloDomovni'];
        }
        
        if (!empty($address['cisloOrientacni'])) {
            $numbers[] = '/' . $address['cisloOrientacni'];
        }
        
        return implode('', $numbers);
    }

    /**
     * Format complete address string for JSON schema.
     */
    private function formatAddressJson(array $address): string
    {
        $parts = [];
        $street = $address['nazevUlice'] ?? ($address['nazevCastiObce'] ?? '');
        $streetNumber = $this->getStreetNumberJson($address);
        if ($street) {
            $streetPart = $street;
            if ($streetNumber) {
                $streetPart .= ' ' . $streetNumber;
            }
            $parts[] = $streetPart;
        }
        if (!empty($address['nazevObce'])) {
            $city = $address['nazevObce'];
            if (!empty($address['psc'])) {
                $city = $address['psc'] . ' ' . $city;
            }
            $parts[] = $city;
        }
        return implode(', ', $parts);
    }

    /**
     * Get street number from JSON address.
     */
    private function getStreetNumberJson(array $address): string
    {
        $numbers = [];
        if (!empty($address['cisloDomovni'])) {
            $numbers[] = $address['cisloDomovni'];
        }
        if (!empty($address['cisloOrientacni'])) {
            $numbers[] = '/' . $address['cisloOrientacni'];
        }
        return implode('', $numbers);
    }
    
    /**
     * Check if company is active.
     */
    private function isCompanyActive(array $company): bool
    {
        $status = $company['Stav'] ?? ($company['stav'] ?? '');
        return $status !== 'ZANIKLÝ' && $status !== 'ZRUŠENÝ' && $status !== 'ZANIKÁ';
    }
    
    /**
     * Get business activities from company data.
     */
    private function getBusinessActivities(array $company): array
    {
        $activities = [];
        
        if (isset($company['SeznamRegistraci'])) {
            $registrace = is_array($company['SeznamRegistraci']->Rejstrik) 
                ? $company['SeznamRegistraci']->Rejstrik 
                : [$company['SeznamRegistraci']->Rejstrik];
            
            foreach ($registrace as $rejstrik) {
                if (isset($rejstrik->PredmetyPodnikani)) {
                    $predmety = is_array($rejstrik->PredmetyPodnikani->Predmet) 
                        ? $rejstrik->PredmetyPodnikani->Predmet 
                        : [$rejstrik->PredmetyPodnikani->Predmet];
                    
                    foreach ($predmety as $predmet) {
                        if (!empty($predmet->nazev)) {
                            $activities[] = (string) $predmet->nazev;
                        }
                    }
                }
            }
        }
        
        return array_unique(array_filter($activities));
    }
    
    /**
     * Get establishment date.
     */
    private function getEstablishmentDate(array $company): ?string
    {
        return $company['DatumVzniku'] ?? null;
    }
    
    /**
     * Get court registration information.
     */
    private function getCourtRegistration(array $company): ?string
    {
        if (isset($company['SeznamRegistraci'])) {
            $registrace = is_array($company['SeznamRegistraci']->Rejstrik) 
                ? $company['SeznamRegistraci']->Rejstrik 
                : [$company['SeznamRegistraci']->Rejstrik];
            
            foreach ($registrace as $rejstrik) {
                if (!empty($rejstrik->nazevRegistru)) {
                    return (string) $rejstrik->nazevRegistru;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Clear cached company data.
     */
    public function clearCompanyCache(string $ico): void
    {
        Cache::forget("ares_company_{$ico}");
    }
    
    /**
     * Validate IČO format.
     */
    public function isValidIcoFormat(string $ico): bool
    {
        return preg_match('/^[0-9]{8}$/', $ico);
    }
    
    /**
     * Validate IČO with control digit algorithm.
     */
    public function isValidIco(string $ico): bool
    {
        if (!$this->isValidIcoFormat($ico)) {
            return false;
        }
        
        // IČO kontrolní algoritmus
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        
        for ($i = 0; $i < 7; $i++) {
            $sum += (int)$ico[$i] * $weights[$i];
        }
        
        $remainder = $sum % 11;
        // If the remainder is 0 or 1, the check digit should be 0 according to Czech IČO specification
        $checkDigit = ($remainder === 0 || $remainder === 1)
            ? 0
            : 11 - $remainder;
        
        return (int)$ico[7] === $checkDigit;
    }
    
    /**
     * Check if ARES API is available.
     */
    public function isApiAvailable(): bool
    {
        // Try JSON endpoint first
        try {
            $url = sprintf(self::JSON_ENDPOINTS[0], '55555555'); // dummy IČO for availability check
            $response = Http::timeout(3)->get($url);
            if ($response->successful() || $response->status() === 404) {
                return true;
            }
        } catch (\Throwable $e) {
            // ignore and try SOAP
        }

        try {
            $client = new SoapClient(self::WSDL_URL, [
                'trace' => false,
                'exceptions' => false,
                'connection_timeout' => 5,
            ]);
            return $client ? true : false;
        } catch (\Throwable $e) {
            return false;
        }
    }
    
    /**
     * Get cache statistics for debugging.
     */
    public function getCacheStats(): array
    {
        return [
            'cache_driver' => config('cache.default'),
            'wsdl_url' => self::WSDL_URL,
            'json_endpoints' => self::JSON_ENDPOINTS,
            'cache_ttl' => 86400, // 24 hours
        ];
    }
}