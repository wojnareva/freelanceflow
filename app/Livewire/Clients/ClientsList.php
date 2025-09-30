<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Services\PerformanceService;
use Illuminate\Support\Facades\Auth;
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
            try {
                $client->delete();
            } catch (\Throwable $e) {
                // If FK constraint prevents deletion, show friendly message
                session()->flash('error', __('clients.delete_failed_due_to_relations'));

                return;
            }

            // Clear performance caches after client deletion
            $performanceService = app(PerformanceService::class);
            $performanceService->clearAllUserCaches(Auth::id());

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
