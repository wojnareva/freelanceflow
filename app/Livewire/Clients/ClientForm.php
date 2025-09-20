<?php

namespace App\Livewire\Clients;

use App\Models\Client;
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
            'user_id' => auth()->id(),
        ];

        if ($this->client) {
            $this->client->update($data);
            session()->flash('message', 'Client updated successfully.');
            $this->dispatch('clientUpdated');
        } else {
            Client::create($data);
            session()->flash('message', 'Client created successfully.');

            return redirect()->route('clients.index');
        }

        $this->reset(['name', 'email', 'phone', 'company', 'address', 'notes']);
    }

    public function render()
    {
        return view('livewire.clients.client-form');
    }
}
