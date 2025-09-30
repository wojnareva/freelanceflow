<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Services\PerformanceService;
use Livewire\Component;
use Livewire\WithPagination;

class ClientsList extends Component
{
    use WithPagination;

    public $search = '';

    public $showEditModal = false;

    public $editingClient = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function editClient($clientId)
    {
        $this->editingClient = Client::find($clientId);
        $this->showEditModal = true;
    }

    public function closeModal()
    {
        $this->showEditModal = false;
        $this->editingClient = null;
    }

    protected $listeners = ['clientUpdated' => 'handleClientUpdated', 'closeModal' => 'closeModal'];

    public function handleClientUpdated()
    {
        $this->closeModal();
        $this->resetPage();
    }

    public function deleteClient($clientId)
    {
        $client = Client::find($clientId);

        if ($client) {
            $client->delete();

            // Clear performance caches after client deletion
            $performanceService = app(PerformanceService::class);
            $performanceService->clearAllUserCaches(auth()->id());

            session()->flash('message', 'Client deleted successfully.');
        }
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('company', 'like', '%'.$this->search.'%');
            })
            ->withCount(['projects'])
            ->paginate(10);

        return view('livewire.clients.clients-list', compact('clients'));
    }
}
