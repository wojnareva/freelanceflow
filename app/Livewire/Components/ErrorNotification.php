<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;

class ErrorNotification extends Component
{
    public $notifications = [];

    protected $listeners = [
        'error-occurred' => 'addError',
        'success-occurred' => 'addSuccess',
        'warning-occurred' => 'addWarning',
        'info-occurred' => 'addInfo',
    ];

    public function mount()
    {
        // Add flash messages from session
        if (session()->has('error')) {
            $this->addNotification('error', session('error'));
        }
        if (session()->has('success')) {
            $this->addNotification('success', session('success'));
        }
        if (session()->has('warning')) {
            $this->addNotification('warning', session('warning'));
        }
        if (session()->has('info')) {
            $this->addNotification('info', session('info'));
        }
    }

    #[On('error-occurred')]
    public function addError($data)
    {
        $this->addNotification('error', $data['message'] ?? $data);
    }

    #[On('success-occurred')]
    public function addSuccess($data)
    {
        $this->addNotification('success', $data['message'] ?? $data);
    }

    #[On('warning-occurred')]
    public function addWarning($data)
    {
        $this->addNotification('warning', $data['message'] ?? $data);
    }

    #[On('info-occurred')]
    public function addInfo($data)
    {
        $this->addNotification('info', $data['message'] ?? $data);
    }

    private function addNotification($type, $message)
    {
        $this->notifications[] = [
            'id' => uniqid(),
            'type' => $type,
            'message' => $message,
            'timestamp' => now()->timestamp,
        ];
    }

    public function dismissNotification($id)
    {
        $this->notifications = array_filter($this->notifications, function ($notification) use ($id) {
            return $notification['id'] !== $id;
        });
    }

    public function render()
    {
        return view('livewire.components.error-notification');
    }
}
