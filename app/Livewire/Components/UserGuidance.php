<?php

namespace App\Livewire\Components;

use Livewire\Component;

class UserGuidance extends Component
{
    public $showGuidance = false;

    public $currentStep = 0;

    public $guidanceType = 'welcome';

    protected $listeners = ['showGuidance' => 'startGuidance'];

    public function mount($type = 'welcome')
    {
        $this->guidanceType = $type;

        // Show guidance for new users or when explicitly requested
        if (auth()->user()->created_at->isToday() || request()->has('show_guidance')) {
            $this->showGuidance = true;
        }
    }

    public function startGuidance($type = null)
    {
        if ($type) {
            $this->guidanceType = $type;
        }
        $this->showGuidance = true;
        $this->currentStep = 0;
    }

    public function nextStep()
    {
        $steps = $this->getSteps();
        if ($this->currentStep < count($steps) - 1) {
            $this->currentStep++;
        } else {
            $this->completeGuidance();
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
        }
    }

    public function skipGuidance()
    {
        $this->showGuidance = false;
    }

    public function completeGuidance()
    {
        $this->showGuidance = false;

        // Mark guidance as completed for this type
        $user = auth()->user();
        $completedGuidance = $user->completed_guidance ?? [];
        $completedGuidance[] = $this->guidanceType;
        $user->update(['completed_guidance' => array_unique($completedGuidance)]);

        session()->flash('success', 'Tutorial completed! You can access help anytime from the ? icon in the navigation.');
    }

    public function getSteps()
    {
        return match ($this->guidanceType) {
            'welcome' => [
                [
                    'title' => 'Welcome to FreelanceFlow!',
                    'content' => 'Let\'s get you started with your freelance business management. This quick tour will show you the main features.',
                    'action' => 'Get Started',
                ],
                [
                    'title' => 'Dashboard Overview',
                    'content' => 'Your dashboard shows key metrics: monthly revenue, unpaid invoices, active projects, and this week\'s hours. These update automatically as you add data.',
                    'action' => 'Next',
                ],
                [
                    'title' => 'Time Tracking',
                    'content' => 'Use the floating timer to track work time. Click the play button, select a project, and start working. Your time will be saved automatically.',
                    'action' => 'Next',
                ],
                [
                    'title' => 'Creating Projects',
                    'content' => 'Add clients first, then create projects with budgets and hourly rates. This helps track profitability and generate accurate invoices.',
                    'action' => 'Next',
                ],
                [
                    'title' => 'Generating Invoices',
                    'content' => 'Convert your tracked time into professional invoices automatically. Choose time entries, and FreelanceFlow will calculate totals and generate PDF invoices.',
                    'action' => 'Finish',
                ],
            ],
            'projects' => [
                [
                    'title' => 'Project Management',
                    'content' => 'Projects help organize your work and track profitability. Each project belongs to a client and can have a budget, hourly rate, and estimated hours.',
                    'action' => 'Next',
                ],
                [
                    'title' => 'Project Status',
                    'content' => 'Use status to organize projects: Draft (planning), Active (current work), On Hold (paused), Completed (finished), Archived (old projects).',
                    'action' => 'Next',
                ],
                [
                    'title' => 'Budget & Rates',
                    'content' => 'Set a fixed budget for the entire project, an hourly rate for time tracking, and estimated hours for planning. These help with profitability tracking.',
                    'action' => 'Finish',
                ],
            ],
            default => []
        };
    }

    public function getCurrentStep()
    {
        $steps = $this->getSteps();

        return $steps[$this->currentStep] ?? null;
    }

    public function render()
    {
        return view('livewire.components.user-guidance', [
            'currentStepData' => $this->getCurrentStep(),
            'totalSteps' => count($this->getSteps()),
        ]);
    }
}
