<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\WebhookService;

class InvoiceObserver
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        $this->webhookService->trigger('invoice.created', $invoice);
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        // Check if status changed to trigger appropriate webhook
        if ($invoice->wasChanged('status')) {
            $newStatus = $invoice->status;

            switch ($newStatus) {
                case 'sent':
                    $this->webhookService->trigger('invoice.sent', $invoice);
                    break;
                case 'paid':
                    $this->webhookService->trigger('invoice.paid', $invoice);
                    break;
                case 'overdue':
                    $this->webhookService->trigger('invoice.overdue', $invoice);
                    break;
            }
        }
    }
}
