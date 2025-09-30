<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoCzSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');

        // Create the main user (freelancer)
        $user = User::factory()->withLocale('cs_CZ')->create([
            'name' => 'Demo Uživatel CZ',
            'email' => 'demo-cz@freelanceflow.app',
            'email_verified_at' => now(),
        ]);

        // Create demo clients
        $clients = \App\Models\Client::factory()->withLocale('cs_CZ')->count(8)->create([
            'currency' => 'CZK',
            'hourly_rate' => $faker->numberBetween(800, 2500),
        ]);

        // Create projects for each client
        $projects = collect();
        $clients->each(function ($client) use (&$projects) {
            $clientProjects = \App\Models\Project::factory()->withLocale('cs_CZ')->count(rand(1, 3))->create([
                'client_id' => $client->id,
            ]);
            $projects = $projects->merge($clientProjects);
        });

        // Create tasks for projects
        $tasks = collect();
        $projects->each(function ($project) use (&$tasks) {
            $projectTasks = \App\Models\Task::factory()->withLocale('cs_CZ')->count(rand(3, 8))->create([
                'project_id' => $project->id,
            ]);
            $tasks = $tasks->merge($projectTasks);
        });

        // Create time entries
        $timeEntries = collect();
        $projects->each(function ($project) use (&$timeEntries, $user, $tasks, $faker) {
            $projectTasks = $tasks->where('project_id', $project->id);

            for ($i = 0; $i < rand(5, 15); $i++) {
                $task = $projectTasks->random();
                $timeEntry = \App\Models\TimeEntry::factory()->create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'task_id' => $task->id,
                    'hourly_rate' => $project->hourly_rate ?? $project->client->hourly_rate ?? $faker->numberBetween(800, 2500),
                ]);
                $timeEntries->push($timeEntry);
            }
        });

        // Update task actual hours based on time entries
        $tasks->each(function ($task) {
            $task->updateActualHours();
        });

        // Create invoices for some projects
        $invoices = collect();
        $projects->random(5)->each(function ($project) use (&$invoices) {
            $invoice = \App\Models\Invoice::factory()->create([
                'client_id' => $project->client_id,
                'project_id' => $project->id,
                'client_details' => [
                    'name' => $project->client->name,
                    'company' => $project->client->company,
                    'email' => $project->client->email,
                    'address' => $project->client->address,
                ],
            ]);
            $invoices->push($invoice);
        });

        // Create invoice items for each invoice
        $invoices->each(function ($invoice) {
            \App\Models\InvoiceItem::factory(rand(2, 5))->create([
                'invoice_id' => $invoice->id,
            ]);
        });

        // Create payments for some invoices
        $invoices->random(3)->each(function ($invoice) {
            \App\Models\Payment::factory()->create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total * (rand(50, 100) / 100), // Partial or full payment
            ]);
        });

        // Create some expenses
        $projects->random(4)->each(function ($project) {
            \App\Models\Expense::factory(rand(1, 3))->create([
                'project_id' => $project->id,
            ]);
        });

        // Create invoice templates (recurring invoices)
        $invoiceTemplates = collect();
        $clients->random(4)->each(function ($client) use (&$invoiceTemplates, $user, $projects) {
            $clientProjects = $projects->where('client_id', $client->id);
            $template = \App\Models\InvoiceTemplate::factory()->create([
                'user_id' => $user->id,
                'client_id' => $client->id,
                'project_id' => $clientProjects->isNotEmpty() ? $clientProjects->random()->id : null,
            ]);
            $invoiceTemplates->push($template);
        });

        $this->command->info('FreelanceFlow česká demo data byla úspěšně vytvořena! 🎉');
        $this->command->info('Přihlašte se pomocí: demo-cz@freelanceflow.app (heslo: password)');
        $this->command->info("Vytvořeno: {$clients->count()} klientů, {$projects->count()} projektů, {$tasks->count()} úkolů");
        $this->command->info("Vytvořeno: {$timeEntries->count()} časových záznamů, {$invoices->count()} faktur, {$invoiceTemplates->count()} šablon faktur");
    }
}
