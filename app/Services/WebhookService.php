<?php

namespace App\Services;

use App\Models\Webhook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class WebhookService
{
    /**
     * Trigger webhooks for a specific event
     */
    public function trigger(string $event, Model $model, ?int $userId = null): void
    {
        // Get user ID from model or parameter
        $userId = $userId ?? $model->user_id ?? auth()->id();
        
        if (!$userId) {
            Log::warning('Cannot trigger webhook: no user ID available', [
                'event' => $event,
                'model' => get_class($model),
                'model_id' => $model->id,
            ]);
            return;
        }

        // Get active webhooks that listen to this event
        $webhooks = Webhook::where('user_id', $userId)
            ->where('active', true)
            ->get()
            ->filter(function (Webhook $webhook) use ($event) {
                return $webhook->listensTo($event);
            });

        if ($webhooks->isEmpty()) {
            return; // No webhooks to trigger
        }

        // Queue webhook deliveries
        foreach ($webhooks as $webhook) {
            $this->queueWebhookDelivery($webhook, $event, $model);
        }
    }

    /**
     * Queue webhook delivery for background processing
     */
    protected function queueWebhookDelivery(Webhook $webhook, string $event, Model $model): void
    {
        try {
            $payload = $webhook->getEventPayload($event, $model);
            
            // Use Laravel queues for reliable delivery
            Queue::push(function () use ($webhook, $payload) {
                $this->deliverWebhook($webhook, $payload);
            });

        } catch (\Exception $e) {
            Log::error('Failed to queue webhook delivery', [
                'webhook_id' => $webhook->id,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Deliver webhook payload to endpoint
     */
    public function deliverWebhook(Webhook $webhook, array $payload): bool
    {
        $attempt = 0;
        $maxAttempts = $webhook->retry_attempts;

        while ($attempt < $maxAttempts) {
            $attempt++;

            try {
                $response = $this->makeHttpRequest($webhook, $payload);

                if ($response->successful()) {
                    $webhook->updateTriggerStatus('success');
                    Log::info('Webhook delivered successfully', [
                        'webhook_id' => $webhook->id,
                        'url' => $webhook->url,
                        'attempt' => $attempt,
                        'status' => $response->status(),
                    ]);
                    return true;
                }

                // If we get a 4xx error, don't retry
                if ($response->clientError()) {
                    $error = "HTTP {$response->status()}: {$response->body()}";
                    $webhook->updateTriggerStatus('failed', $error);
                    Log::warning('Webhook delivery failed (client error, not retrying)', [
                        'webhook_id' => $webhook->id,
                        'url' => $webhook->url,
                        'status' => $response->status(),
                        'error' => $error,
                    ]);
                    return false;
                }

                // For 5xx errors, we'll retry
                $error = "HTTP {$response->status()}: {$response->body()}";
                
                if ($attempt >= $maxAttempts) {
                    $webhook->updateTriggerStatus('failed', $error);
                    Log::error('Webhook delivery failed after all retries', [
                        'webhook_id' => $webhook->id,
                        'url' => $webhook->url,
                        'attempts' => $attempt,
                        'error' => $error,
                    ]);
                    return false;
                }

                // Wait before retry (exponential backoff)
                $backoffSeconds = min(60, pow(2, $attempt - 1));
                sleep($backoffSeconds);

            } catch (\Exception $e) {
                $error = $e->getMessage();
                
                if ($attempt >= $maxAttempts) {
                    $webhook->updateTriggerStatus('failed', $error);
                    Log::error('Webhook delivery failed after all retries (exception)', [
                        'webhook_id' => $webhook->id,
                        'url' => $webhook->url,
                        'attempts' => $attempt,
                        'error' => $error,
                    ]);
                    return false;
                }

                // Wait before retry
                $backoffSeconds = min(60, pow(2, $attempt - 1));
                sleep($backoffSeconds);
            }
        }

        return false;
    }

    /**
     * Make HTTP request to webhook endpoint
     */
    protected function makeHttpRequest(Webhook $webhook, array $payload): \Illuminate\Http\Client\Response
    {
        $payloadJson = json_encode($payload);
        
        // Prepare headers
        $headers = array_merge([
            'Content-Type' => 'application/json',
            'User-Agent' => 'FreelanceFlow-Webhook/1.0',
            'X-Webhook-Event' => $payload['event'],
            'X-Webhook-ID' => $webhook->id,
        ], $webhook->headers ?? []);

        // Add signature if secret is configured
        if ($webhook->secret) {
            $signature = 'sha256=' . hash_hmac('sha256', $payloadJson, $webhook->secret);
            $headers['X-Webhook-Signature'] = $signature;
        }

        return Http::withHeaders($headers)
            ->timeout($webhook->timeout)
            ->post($webhook->url, $payload);
    }

    /**
     * Test webhook delivery
     */
    public function testWebhook(Webhook $webhook): array
    {
        $payload = [
            'event' => 'webhook.test',
            'timestamp' => now()->toISOString(),
            'webhook_id' => $webhook->id,
            'user_id' => $webhook->user_id,
            'data' => [
                'message' => 'This is a test webhook delivery from FreelanceFlow',
                'webhook_name' => $webhook->name,
            ]
        ];

        try {
            $response = $this->makeHttpRequest($webhook, $payload);
            
            $result = [
                'success' => $response->successful(),
                'status' => $response->status(),
                'response_time' => $response->handlerStats()['total_time'] ?? 0,
                'response_body' => $response->body(),
            ];

            if ($result['success']) {
                $webhook->updateTriggerStatus('success');
            } else {
                $error = "HTTP {$response->status()}: {$response->body()}";
                $webhook->updateTriggerStatus('failed', $error);
                $result['error'] = $error;
            }

            return $result;

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $webhook->updateTriggerStatus('failed', $error);
            
            return [
                'success' => false,
                'error' => $error,
                'status' => 0,
                'response_time' => 0,
            ];
        }
    }

    /**
     * Get webhook delivery statistics
     */
    public function getWebhookStats(Webhook $webhook): array
    {
        // In a production app, you might want to store delivery logs
        // For now, we'll return basic stats from the webhook model
        return [
            'webhook_id' => $webhook->id,
            'name' => $webhook->name,
            'url' => $webhook->url,
            'active' => $webhook->active,
            'events' => $webhook->events,
            'last_triggered_at' => $webhook->last_triggered_at,
            'last_status' => $webhook->last_status,
            'last_error' => $webhook->last_error,
            'created_at' => $webhook->created_at,
        ];
    }

    /**
     * Validate webhook URL
     */
    public function validateWebhookUrl(string $url): bool
    {
        // Basic URL validation
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check if URL is reachable (optional ping test)
        try {
            $response = Http::timeout(5)->head($url);
            return $response->status() < 500; // Accept any non-server-error status
        } catch (\Exception $e) {
            return false;
        }
    }
}