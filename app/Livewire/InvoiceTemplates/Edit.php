<?php

namespace App\Livewire\InvoiceTemplates;

use App\Models\Client;
use App\Models\InvoiceTemplate;
use Livewire\Component;

class Edit extends Component
{
    public InvoiceTemplate $template;
    public $name;
    public $client_id;
    public $frequency;
    public $description;

    protected $rules = [
        'name' => 'required|string|max:255',
        'client_id' => 'required|exists:clients,id',
        'frequency' => 'required|in:weekly,monthly,quarterly,yearly',
        'description' => 'nullable|string',
    ];

    public function mount(InvoiceTemplate $template)
    {
        $this->template = $template;
        $this->name = $template->name;
        $this->client_id = $template->client_id;
        $this->frequency = $template->frequency;
        $this->description = $template->description;
    }

    public function save()
    {
        $this->validate();

        $this->template->update([
            'name' => $this->name,
            'client_id' => $this->client_id,
            'frequency' => $this->frequency,
            'description' => $this->description,
        ]);

        session()->flash('success', __('invoices.template_updated_successfully'));

        return redirect()->route('invoice-templates.index');
    }

    public function render()
    {
        return view('livewire.invoice-templates.edit', [
            'clients' => Client::where('user_id', auth()->id())->orderBy('name')->get(),
        ]);
    }
}