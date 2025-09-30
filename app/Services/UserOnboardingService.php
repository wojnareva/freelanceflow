<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserOnboardingService
{
    /**
     * Create sample data for a new user to help them get started
     */
    public function createSampleData(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Create sample clients
            $clients = $this->createSampleClients($user);

            // Create sample projects
            $projects = $this->createSampleProjects($user, $clients);

            // Create sample time entries
            $this->createSampleTimeEntries($projects);

            // Create sample invoices
            $this->createSampleInvoices($user, $projects);

            // Mark user as onboarded
            $user->update([
                'onboarded_at' => now(),
                'sample_data_created' => true,
            ]);
        });
    }

    /**
     * Create sample clients for the user
     */
    private function createSampleClients(User $user): array
    {
        $sampleClients = [
            [
                'name' => 'TechCorp Solutions',
                'company' => 'TechCorp Solutions Inc.',
                'email' => 'hello@techcorp.com',
                'phone' => '+1-555-0123',
                'address' => '123 Tech Street, Silicon Valley, CA 94000',
                'website' => 'https://techcorp.com',
                'notes' => 'Fast-growing tech startup focused on AI solutions. Great client for long-term projects.',
            ],
            [
                'name' => 'Creative Design Studio',
                'company' => 'Creative Design Studio LLC',
                'email' => 'projects@creativestudio.design',
                'phone' => '+1-555-0456',
                'address' => '456 Design Avenue, New York, NY 10001',
                'website' => 'https://creativestudio.design',
                'notes' => 'Boutique design agency. Values quality over speed, good rates.',
            ],
            [
                'name' => 'Local Restaurant Group',
                'company' => 'Delicious Eats Restaurant Group',
                'email' => 'owner@deliciouseats.com',
                'phone' => '+1-555-0789',
                'address' => '789 Food Street, Portland, OR 97201',
                'notes' => 'Local restaurant chain expanding online presence. Budget-conscious but loyal.',
            ],
        ];

        $clients = [];
        foreach ($sampleClients as $clientData) {
            $clients[] = Client::create(array_merge($clientData, ['user_id' => $user->id]));
        }

        return $clients;
    }

    /**
     * Create sample projects for the user
     */
    private function createSampleProjects(User $user, array $clients): array
    {
        $sampleProjects = [
            [
                'client' => $clients[0], // TechCorp
                'name' => 'AI Dashboard Development',
                'description' => 'Build a modern dashboard for AI analytics with real-time data visualization and user management.',
                'status' => ProjectStatus::Active,
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(30),
                'budget' => 15000.00,
                'hourly_rate' => 85.00,
                'estimated_hours' => 180,
            ],
            [
                'client' => $clients[1], // Creative Studio
                'name' => 'Portfolio Website Redesign',
                'description' => 'Complete redesign of portfolio website with modern animations and improved user experience.',
                'status' => ProjectStatus::Active,
                'start_date' => Carbon::now()->subDays(8),
                'end_date' => Carbon::now()->addDays(22),
                'budget' => 8500.00,
                'hourly_rate' => 75.00,
                'estimated_hours' => 110,
            ],
            [
                'client' => $clients[2], // Restaurant
                'name' => 'Online Ordering System',
                'description' => 'Custom online ordering system with payment integration and delivery tracking.',
                'status' => ProjectStatus::OnHold,
                'start_date' => Carbon::now()->subDays(25),
                'end_date' => Carbon::now()->addDays(45),
                'budget' => 12000.00,
                'hourly_rate' => 65.00,
                'estimated_hours' => 185,
            ],
            [
                'client' => $clients[0], // TechCorp
                'name' => 'Mobile App MVP',
                'description' => 'Minimum viable product for iOS and Android mobile application.',
                'status' => ProjectStatus::Completed,
                'start_date' => Carbon::now()->subDays(90),
                'end_date' => Carbon::now()->subDays(10),
                'budget' => 25000.00,
                'hourly_rate' => 85.00,
                'estimated_hours' => 300,
            ],
        ];

        $projects = [];
        foreach ($sampleProjects as $projectData) {
            $client = $projectData['client'];
            unset($projectData['client']);

            $projects[] = Project::create(array_merge($projectData, [
                'user_id' => $user->id,
                'client_id' => $client->id,
            ]));
        }

        return $projects;
    }

    /**
     * Create sample time entries
     */
    private function createSampleTimeEntries(array $projects): void
    {
        $timeEntries = [
            // AI Dashboard project entries
            [
                'project' => $projects[0],
                'description' => 'Initial project setup and requirements analysis',
                'duration' => 240, // 4 hours
                'date' => Carbon::now()->subDays(15),
            ],
            [
                'project' => $projects[0],
                'description' => 'Database schema design and API planning',
                'duration' => 180, // 3 hours
                'date' => Carbon::now()->subDays(14),
            ],
            [
                'project' => $projects[0],
                'description' => 'Frontend dashboard mockups and wireframes',
                'duration' => 300, // 5 hours
                'date' => Carbon::now()->subDays(12),
            ],
            [
                'project' => $projects[0],
                'description' => 'API development and testing',
                'duration' => 420, // 7 hours
                'date' => Carbon::now()->subDays(10),
            ],

            // Portfolio Website project entries
            [
                'project' => $projects[1],
                'description' => 'Client consultation and design brief review',
                'duration' => 120, // 2 hours
                'date' => Carbon::now()->subDays(8),
            ],
            [
                'project' => $projects[1],
                'description' => 'Homepage design and layout development',
                'duration' => 360, // 6 hours
                'date' => Carbon::now()->subDays(6),
            ],
            [
                'project' => $projects[1],
                'description' => 'Animation implementation and responsive testing',
                'duration' => 480, // 8 hours
                'date' => Carbon::now()->subDays(4),
            ],

            // Completed project entries
            [
                'project' => $projects[3],
                'description' => 'Final testing and deployment',
                'duration' => 240, // 4 hours
                'date' => Carbon::now()->subDays(12),
            ],
        ];

        foreach ($timeEntries as $entryData) {
            $project = $entryData['project'];
            unset($entryData['project']);

            TimeEntry::create(array_merge($entryData, [
                'user_id' => $project->user_id,
                'project_id' => $project->id,
                'start_time' => $entryData['date']->setTime(9, 0),
                'end_time' => $entryData['date']->setTime(9, 0)->addMinutes($entryData['duration']),
            ]));
        }
    }

    /**
     * Create sample invoices
     */
    private function createSampleInvoices(User $user, array $projects): void
    {
        $sampleInvoices = [
            [
                'project' => $projects[3], // Completed project
                'status' => InvoiceStatus::Paid,
                'issue_date' => Carbon::now()->subDays(20),
                'due_date' => Carbon::now()->subDays(5),
                'amount' => 8500.00,
                'notes' => 'Final invoice for completed mobile app MVP project.',
            ],
            [
                'project' => $projects[0], // Active project
                'status' => InvoiceStatus::Sent,
                'issue_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->addDays(20),
                'amount' => 5100.00,
                'notes' => 'Progress invoice for AI Dashboard development - Phase 1 completed.',
            ],
        ];

        foreach ($sampleInvoices as $invoiceData) {
            $project = $invoiceData['project'];
            unset($invoiceData['project']);

            Invoice::create(array_merge($invoiceData, [
                'user_id' => $user->id,
                'client_id' => $project->client_id,
                'project_id' => $project->id,
                'invoice_number' => $this->generateInvoiceNumber($user),
                'currency' => 'USD',
            ]));
        }
    }

    /**
     * Generate a unique invoice number for the user
     */
    private function generateInvoiceNumber(User $user): string
    {
        $year = date('Y');
        $lastInvoice = Invoice::where('user_id', $user->id)
            ->where('invoice_number', 'like', "INV-{$year}-%")
            ->orderByDesc('invoice_number')
            ->first();

        $nextNumber = 1;
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = $lastNumber + 1;
        }

        return sprintf('INV-%s-%04d', $year, $nextNumber);
    }

    /**
     * Check if user should see onboarding
     */
    public function shouldShowOnboarding(User $user): bool
    {
        return ! $user->onboarded_at && ! $user->sample_data_created;
    }

    /**
     * Check if user has minimal data to get started
     */
    public function hasMinimalData(User $user): bool
    {
        return $user->clients()->count() > 0 || $user->projects()->count() > 0;
    }
}
