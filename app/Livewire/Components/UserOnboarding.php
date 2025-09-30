<?php

namespace App\Livewire\Components;

use App\Services\UserOnboardingService;
use Livewire\Component;

class UserOnboarding extends Component
{
    public $showOnboarding = false;

    public $step = 1;

    public $totalSteps = 3;

    public function mount()
    {
        $onboardingService = app(UserOnboardingService::class);
        $user = auth()->user();

        // Show onboarding if user is new and has no data
        if ($onboardingService->shouldShowOnboarding($user) &&
            ! $onboardingService->hasMinimalData($user)) {
            $this->showOnboarding = true;
        }
    }

    public function nextStep()
    {
        if ($this->step < $this->totalSteps) {
            $this->step++;
        } else {
            $this->completeOnboarding();
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function skipOnboarding()
    {
        $this->showOnboarding = false;
        auth()->user()->update(['onboarded_at' => now()]);
    }

    public function createSampleData()
    {
        $onboardingService = app(UserOnboardingService::class);
        $onboardingService->createSampleData(auth()->user());
        $this->showOnboarding = false;

        session()->flash('success', 'Sample data created! Explore your new projects, clients, and time entries.');

        // Redirect to dashboard to see the new data
        return redirect()->route('dashboard');
    }

    public function completeOnboarding()
    {
        $this->showOnboarding = false;
        auth()->user()->update(['onboarded_at' => now()]);

        session()->flash('success', 'Welcome to FreelanceFlow! You can always access help from the ? icon in the navigation.');
    }

    public function getStepData()
    {
        $steps = [
            1 => [
                'title' => 'Welcome to FreelanceFlow!',
                'content' => 'FreelanceFlow helps you manage your freelance business with time tracking, project management, invoicing, and financial reporting. Let\'s get you set up!',
                'action' => 'Get Started',
                'icon' => '🎉',
            ],
            2 => [
                'title' => 'Would you like sample data?',
                'content' => 'We can create sample clients, projects, time entries, and invoices to help you explore the features. You can always delete this data later.',
                'action' => 'Create Sample Data',
                'action_alt' => 'Start Fresh',
                'icon' => '📊',
            ],
            3 => [
                'title' => 'You\'re all set!',
                'content' => 'You can now start creating clients, tracking time, managing projects, and generating invoices. Check out the dashboard for an overview of your business.',
                'action' => 'Go to Dashboard',
                'icon' => '🚀',
            ],
        ];

        return $steps[$this->step] ?? $steps[1];
    }

    public function render()
    {
        return view('livewire.components.user-onboarding', [
            'stepData' => $this->getStepData(),
        ]);
    }
}
