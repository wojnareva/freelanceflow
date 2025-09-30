<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'url',
        'events',
        'secret',
        'active',
        'headers',
        'timeout',
        'retry_attempts',
        'last_triggered_at',
        'last_status',
        'last_error',
    ];

    protected $casts = [
        'events' => 'array',
        'headers' => 'array',
        'active' => 'boolean',
        'last_triggered_at' => 'datetime',
        'timeout' => 'integer',
        'retry_attempts' => 'integer',
    ];

    protected $hidden = [
        'secret',
    ];

    /**
     * Get the user that owns the webhook
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Available webhook events
     */
    public static function getAvailableEvents(): array
    {
        return [
            'invoice.created' => 'Invoice Created',
            'invoice.sent' => 'Invoice Sent',
            'invoice.paid' => 'Invoice Paid',
            'invoice.overdue' => 'Invoice Overdue',
            'payment.received' => 'Payment Received',
            'project.created' => 'Project Created',
            'project.completed' => 'Project Completed',
            'client.created' => 'Client Created',
            'time_entry.created' => 'Time Entry Created',
            'expense.created' => 'Expense Created',
        ];
    }

    /**
     * Check if webhook listens to a specific event
     */
    public function listensTo(string $event): bool
    {
        return in_array($event, $this->events ?? []);
    }

    /**
     * Update webhook status after trigger attempt
     */
    public function updateTriggerStatus(string $status, ?string $error = null): void
    {
        $this->update([
            'last_triggered_at' => now(),
            'last_status' => $status,
            'last_error' => $error,
        ]);
    }

    /**
     * Generate a unique secret for this webhook
     */
    public function generateSecret(): string
    {
        $secret = 'whsec_'.bin2hex(random_bytes(32));
        $this->update(['secret' => $secret]);

        return $secret;
    }

    /**
     * Verify webhook signature
     */
    public function verifySignature(string $payload, string $signature): bool
    {
        if (! $this->secret) {
            return true; // No secret configured, skip verification
        }

        $expectedSignature = 'sha256='.hash_hmac('sha256', $payload, $this->secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get webhook payload for an event
     */
    public function getEventPayload(string $event, Model $model): array
    {
        $basePayload = [
            'event' => $event,
            'timestamp' => now()->toISOString(),
            'webhook_id' => $this->id,
            'user_id' => $this->user_id,
        ];

        switch ($event) {
            case 'invoice.created':
            case 'invoice.sent':
            case 'invoice.paid':
            case 'invoice.overdue':
                return array_merge($basePayload, [
                    'data' => [
                        'invoice' => $model->toArray(),
                        'client' => $model->client?->toArray(),
                        'items' => $model->items?->toArray(),
                    ],
                ]);

            case 'payment.received':
                return array_merge($basePayload, [
                    'data' => [
                        'payment' => $model->toArray(),
                        'invoice' => $model->invoice?->toArray(),
                        'client' => $model->invoice?->client?->toArray(),
                    ],
                ]);

            case 'project.created':
            case 'project.completed':
                return array_merge($basePayload, [
                    'data' => [
                        'project' => $model->toArray(),
                        'client' => $model->client?->toArray(),
                    ],
                ]);

            case 'client.created':
                return array_merge($basePayload, [
                    'data' => [
                        'client' => $model->toArray(),
                    ],
                ]);

            case 'time_entry.created':
                return array_merge($basePayload, [
                    'data' => [
                        'time_entry' => $model->toArray(),
                        'project' => $model->project?->toArray(),
                        'task' => $model->task?->toArray(),
                    ],
                ]);

            case 'expense.created':
                return array_merge($basePayload, [
                    'data' => [
                        'expense' => $model->toArray(),
                        'project' => $model->project?->toArray(),
                    ],
                ]);

            default:
                return array_merge($basePayload, [
                    'data' => $model->toArray(),
                ]);
        }
    }
}
