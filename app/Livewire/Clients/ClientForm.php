<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Services\AresService;
use App\Rules\ValidIco;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ClientForm extends Component
{
    public $client;

    public $name = '';

    public $email = '';

    public $phone = '';

    public $company = '';

    public $address = '';

    public $notes = '';

    // Czech business fields
    public $ico = '';
    public $dic = '';
    
    // ARES integration state
    public $companyDataFound = false;
    public $aresLookupLoading = false;
    public $autoFillEnabled = true;

    public function mount($client = null)
    {
        if ($client) {
            $this->client = $client;
            $this->name = $client->name;
            $this->email = $client->email;
            $this->phone = $client->phone;
            $this->company = $client->company;
            $this->address = $client->address;
            $this->notes = $client->notes;
            $this->ico = $client->ico ?? '';
            $this->dic = $client->dic ?? '';
            
            // Set company data found if we have ARES data
            $this->companyDataFound = !empty($client->company_registry_data);
        }
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clients', 'email')->ignore($this->client?->id),
            ],
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'ico' => [
                'nullable',
                'string',
                'size:8',
                'regex:/^[0-9]{8}$/',
                new ValidIco(),
                Rule::unique('clients', 'ico')->ignore($this->client?->id),
            ],
            'dic' => 'nullable|string|max:15',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'address' => $this->address,
            'notes' => $this->notes,
            'ico' => $this->ico ?: null,
            'dic' => $this->dic ?: null,
            'user_id' => auth()->id(),
        ];

        if ($this->client) {
            $this->client->update($data);
            session()->flash('message', __('clients.client_updated'));
            $this->dispatch('clientUpdated');
        } else {
            Client::create($data);
            session()->flash('message', __('clients.client_created'));

            return redirect()->route('clients.index');
        }

        $this->reset(['name', 'email', 'phone', 'company', 'address', 'notes', 'ico', 'dic']);
        $this->companyDataFound = false;
    }
    
    /**
     * Watch for IČO changes and trigger automatic lookup.
     */
    public function updatedIco()
    {
        if (!$this->autoFillEnabled) {
            return;
        }
        
        // Clear previous company data
        $this->companyDataFound = false;
        
        // Only lookup if IČO is 8 digits
        if (strlen($this->ico) === 8 && preg_match('/^[0-9]{8}$/', $this->ico)) {
            $this->fetchCompanyData();
        }
    }
    
    /**
     * Manually fetch company data from ARES.
     */
    public function fetchCompanyData()
    {
        if (!$this->ico || strlen($this->ico) !== 8) {
            $this->addError('ico', __('clients.ico_format'));
            return;
        }
        
        $this->aresLookupLoading = true;
        $this->companyDataFound = false;
        
        try {
            $aresService = app(AresService::class);
            
            // Validate IČO first
            if (!$aresService->isValidIco($this->ico)) {
                $this->addError('ico', __('clients.ico_invalid'));
                return;
            }
            
            $companyData = $aresService->getCompanyData($this->ico);
            
            if ($companyData && !empty($companyData['company_name'])) {
                $this->fillCompanyData($companyData);
                $this->companyDataFound = true;
                
                session()->flash('ares_success', __('clients.company_data_loaded'));
            } else {
                $this->addError('ico', __('clients.ico_not_found'));
            }
            
        } catch (\Exception $e) {
            logger()->error('ARES lookup failed', [
                'ico' => $this->ico,
                'error' => $e->getMessage()
            ]);
            
            $this->addError('ico', __('clients.ares_api_error'));
        } finally {
            $this->aresLookupLoading = false;
        }
    }
    
    /**
     * Fill form with company data from ARES.
     */
    private function fillCompanyData(array $companyData)
    {
        // Only fill empty fields to avoid overwriting user data
        if (empty($this->company)) {
            $this->company = $companyData['company_name'] ?? '';
        }
        
        if (empty($this->address)) {
            $this->address = $companyData['address'] ?? '';
        }
        
        if (empty($this->dic) && !empty($companyData['dic'])) {
            $this->dic = $companyData['dic'];
        }
        
        // Store registry data for future reference
        if ($this->client) {
            $this->client->update([
                'company_registry_data' => $companyData,
                'registry_updated_at' => now(),
            ]);
        }
    }
    
    /**
     * Clear company data and reset ARES state.
     */
    public function clearCompanyData()
    {
        $this->companyDataFound = false;
        
        // Clear only ARES-filled data, not user-entered data
        $this->company = '';
        $this->address = '';
        $this->dic = '';
        
        if ($this->client) {
            $this->client->update([
                'company_registry_data' => null,
                'registry_updated_at' => null,
            ]);
        }
    }
    
    /**
     * Toggle auto-fill functionality.
     */
    public function toggleAutoFill()
    {
        $this->autoFillEnabled = !$this->autoFillEnabled;
    }

    public function render()
    {
        return view('livewire.clients.client-form');
    }
}
