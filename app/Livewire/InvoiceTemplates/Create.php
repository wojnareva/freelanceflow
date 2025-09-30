<?php

namespace App\Livewire\InvoiceTemplates;

use App\Models\Client;
use App\Models\InvoiceTemplate;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $client_id = '';
    public $frequency = 'monthly';
    public $description = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'client_id' => 'required|exists:clients,id',
        'frequency' => 'required|in:weekly,monthly,quarterly,yearly',
        'description' => 'nullable|string',
    ];

    public function save()
    {
        $this->validate();

        InvoiceTemplate::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'client_id' => $this->client_id,
            'frequency' => $this->frequency,
            'description' => $this->description,
        ]);

        session()->flash('success', __('invoices.template_created_successfully'));

        return redirect()->route('invoice-templates.index');
    }

    public function render()
    {
        return view('livewire.invoice-templates.create', [
            'clients' => Client::where('user_id', auth()->id())->orderBy('name')->get(),
        ]);
    }
}