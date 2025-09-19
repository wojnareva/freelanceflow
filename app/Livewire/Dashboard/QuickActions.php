<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Project;
use App\Models\Client;

class QuickActions extends Component
{
    public $recentProjects;
    public $recentClients;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->recentProjects = Project::with('client')
            ->where('status', 'active')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();

        $this->recentClients = Client::orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function startTimer($projectId)
    {
        // This will redirect to time tracking page with pre-selected project
        return redirect()->route('time-tracking', ['project' => $projectId]);
    }

    public function createInvoice($projectId = null)
    {
        // This will redirect to invoice creation page
        $query = $projectId ? ['project' => $projectId] : [];
        return redirect()->route('invoices.create', $query);
    }

    public function viewProject($projectId)
    {
        return redirect()->route('projects.show', $projectId);
    }

    public function viewClient($clientId)
    {
        return redirect()->route('clients.show', $clientId);
    }

    public function render()
    {
        return view('livewire.dashboard.quick-actions');
    }
}
