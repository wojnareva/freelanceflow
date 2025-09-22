<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AresService
{
    private const ARES_API_URL = 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty';
    
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
     * Fetch company data from ARES API.
     */
    private function fetchFromAres(string $ico): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get(self::ARES_API_URL . '/' . $ico);
                
            if (!$response->successful()) {
                Log::warning('ARES API request failed', [
                    'ico' => $ico,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }
            
            $data = $response->json();
            return $this->parseAresResponse($data);
            
        } catch (\Exception $e) {
            Log::error('ARES API Error', [
                'ico' => $ico,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }
    
    /**
     * Parse ARES API response into standardized format.
     */
    private function parseAresResponse(array $data): array
    {
        $company = $data['ekonomickySubjekt'] ?? null;
        
        if (!$company) {
            return [];
        }
        
        $address = $company['sidlo'] ?? [];
        $legal = $company['pravniForma'] ?? [];
        
        return [
            'ico' => $company['ico'] ?? '',
            'dic' => $company['dic'] ?? null,
            'company_name' => $this->getCompanyName($company),
            'legal_form' => $legal['nazev'] ?? '',
            'address' => $this->formatAddress($address),
            'street' => $this->getStreet($address),
            'street_number' => $this->getStreetNumber($address),
            'city' => $address['nazevObce'] ?? '',
            'postal_code' => $address['psc'] ?? '',
            'state' => 'Česká republika',
            'is_active' => $this->isCompanyActive($company),
            'business_activities' => $this->getBusinessActivities($company),
            'establishment_date' => $this->getEstablishmentDate($company),
            'court_registration' => $this->getCourtRegistration($company),
            'raw_data' => $company, // Store raw data for debugging
        ];
    }
    
    /**
     * Get company name from various possible fields.
     */
    private function getCompanyName(array $company): string
    {
        return $company['obchodniJmeno'] 
            ?? $company['nazev'] 
            ?? $company['jmeno'] 
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
            if (!empty($address['psc'])) {
                $city = $address['psc'] . ' ' . $city;
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
            $numbers[] = $address['cisloOrientacni'];
        }
        
        return implode('/', $numbers);
    }
    
    /**
     * Check if company is active.
     */
    private function isCompanyActive(array $company): bool
    {
        $status = $company['stavZanikuZivnosti'] ?? $company['stav'] ?? '';
        return $status !== 'ZANIKLÝ' && $status !== 'ZRUŠENÝ';
    }
    
    /**
     * Get business activities from company data.
     */
    private function getBusinessActivities(array $company): array
    {
        $activities = [];
        
        if (isset($company['seznamRegistraci'])) {
            foreach ($company['seznamRegistraci'] as $registration) {
                if (isset($registration['predmetyPodnikani'])) {
                    foreach ($registration['predmetyPodnikani'] as $activity) {
                        if (!empty($activity['nazev'])) {
                            $activities[] = $activity['nazev'];
                        }
                    }
                }
            }
        }
        
        return array_filter(array_unique($activities));
    }
    
    /**
     * Get establishment date.
     */
    private function getEstablishmentDate(array $company): ?string
    {
        return $company['datumVzniku'] ?? $company['datumZalozen'] ?? null;
    }
    
    /**
     * Get court registration information.
     */
    private function getCourtRegistration(array $company): ?string
    {
        if (isset($company['seznamRegistraci'])) {
            foreach ($company['seznamRegistraci'] as $registration) {
                if (!empty($registration['nazevRegistru'])) {
                    return $registration['nazevRegistru'];
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
        $checkDigit = $remainder < 2 ? $remainder : 11 - $remainder;
        
        return (int)$ico[7] === $checkDigit;
    }
    
    /**
     * Check if ARES API is available.
     */
    public function isApiAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get(self::ARES_API_URL);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get cache statistics for debugging.
     */
    public function getCacheStats(): array
    {
        // This is a simple implementation - in production you might want more detailed stats
        return [
            'cache_driver' => config('cache.default'),
            'api_url' => self::ARES_API_URL,
            'cache_ttl' => 86400, // 24 hours
        ];
    }
}