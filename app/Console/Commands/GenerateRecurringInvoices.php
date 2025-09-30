<?php

namespace App\Console\Commands;

use App\Models\InvoiceTemplate;
use Illuminate\Console\Command;

class GenerateRecurringInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-recurring {--dry-run : Show what would be generated without actually creating invoices}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices from recurring invoice templates that are due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Checking for recurring invoice templates due for generation...');

        $dueTemplates = InvoiceTemplate::dueForGeneration()
            ->with(['client', 'project'])
            ->get();

        if ($dueTemplates->isEmpty()) {
            $this->info('No invoice templates are due for generation.');

            return Command::SUCCESS;
        }

        $this->info("Found {$dueTemplates->count()} template(s) due for generation:");

        $generatedCount = 0;
        $errors = [];

        foreach ($dueTemplates as $template) {
            $templateInfo = "- {$template->name} (Client: {$template->client->name})";

            if ($template->project) {
                $templateInfo .= " [Project: {$template->project->name}]";
            }

            $templateInfo .= " - Next due: {$template->next_generation_date->format('Y-m-d')}";

            $this->line($templateInfo);

            if (! $dryRun) {
                try {
                    $invoice = $template->generateInvoice();
                    $this->info("  ✓ Generated invoice: {$invoice->invoice_number}");
                    $generatedCount++;
                } catch (\Exception $e) {
                    $error = "  ✗ Failed to generate invoice: {$e->getMessage()}";
                    $this->error($error);
                    $errors[] = [
                        'template' => $template->name,
                        'error' => $e->getMessage(),
                    ];
                }
            } else {
                $this->info("  → Would generate invoice for {$template->total_with_tax} {$template->currency}");
            }
        }

        if ($dryRun) {
            $this->info("\nDry run completed. No invoices were actually generated.");
            $this->info('Run without --dry-run to generate invoices.');
        } else {
            $this->info("\nGeneration completed!");
            $this->info("Successfully generated: {$generatedCount} invoice(s)");

            if (! empty($errors)) {
                $this->error('Errors encountered: '.count($errors));
                foreach ($errors as $error) {
                    $this->error("- {$error['template']}: {$error['error']}");
                }
            }
        }

        return Command::SUCCESS;
    }
}
