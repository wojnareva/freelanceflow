<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class ClientDetail extends Component
{
    public Client $client;

    public $showEditModal = false;

    public function mount(Client $client)
    {
        $this->client = $client;
    }

    public function editClient()
    {
        $this->showEditModal = true;
    }

    public function closeModal()
    {
        $this->showEditModal = false;
    }

    protected $listeners = ['clientUpdated' => 'handleClientUpdated', 'closeModal' => 'closeModal'];

    public function handleClientUpdated()
    {
        $this->client->refresh();
        $this->closeModal();
    }

    public function render()
    {
        $projects = $this->client->projects()
            ->withCount(['tasks', 'timeEntries'])
            ->withSum('timeEntries', 'duration')
            ->latest()
            ->get();

        $totalRevenue = $this->client->invoices()
            ->where('status', 'paid')
            ->sum('total');

        $stats = [
            'total_projects' => $projects->count(),
            'total_hours' => $projects->sum('time_entries_sum_duration') / 3600,
            'total_revenue' => $totalRevenue,
            'active_projects' => $projects->where('status', 'active')->count(),
        ];

        return view('livewire.clients.client-detail', compact('projects', 'stats'));
    }
}
