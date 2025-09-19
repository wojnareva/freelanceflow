# FreelanceFlow - Laravel TALL Stack Project Setup

## 🚀 Inicializace projektu

### 1. Základní Laravel instalace
```bash
# Vytvoření nového Laravel projektu
composer create-project laravel/laravel freelanceflow
cd freelanceflow

# Inicializace Git repository
git init
git add .
git commit -m "Initial Laravel setup"
```

### 2. TALL Stack instalace
```bash
# Livewire
composer require livewire/livewire

# Tailwind CSS + Vite setup
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
npm install alpinejs

# Tailwind Forms & Typography
npm install -D @tailwindcss/forms @tailwindcss/typography

# Development tools
composer require --dev barryvdh/laravel-debugbar
composer require --dev laravel/pint
```

### 3. Dodatečné packages
```bash
# PDF generování pro faktury
composer require barryvdh/laravel-dompdf

# Autentizace & autorizace
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Filament (optional - pro rychlý admin panel)
composer require filament/filament:"^3.0"

# Spatie packages pro permissions & media
composer require spatie/laravel-permission
composer require spatie/laravel-medialibrary

# Money handling
composer require moneyphp/money

# UUID pro faktury
composer require ramsey/uuid
```

## 📁 Struktura projektu

```
freelanceflow/
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Client.php
│   │   ├── Project.php
│   │   ├── TimeEntry.php
│   │   ├── Task.php
│   │   ├── Invoice.php
│   │   ├── InvoiceItem.php
│   │   ├── Payment.php
│   │   └── Expense.php
│   ├── Livewire/
│   │   ├── Dashboard/
│   │   │   ├── StatsOverview.php
│   │   │   ├── RecentActivity.php
│   │   │   └── RevenueChart.php
│   │   ├── Projects/
│   │   │   ├── ProjectList.php
│   │   │   ├── ProjectForm.php
│   │   │   ├── KanbanBoard.php
│   │   │   └── ProjectDetail.php
│   │   ├── TimeTracking/
│   │   │   ├── Timer.php
│   │   │   ├── TimeEntryList.php
│   │   │   └── TimeEntryForm.php
│   │   ├── Invoices/
│   │   │   ├── InvoiceList.php
│   │   │   ├── InvoiceBuilder.php
│   │   │   ├── InvoicePreview.php
│   │   │   └── InvoiceSettings.php
│   │   └── Clients/
│   │       ├── ClientList.php
│   │       ├── ClientForm.php
│   │       └── ClientDetail.php
│   ├── Services/
│   │   ├── InvoiceService.php
│   │   ├── TimeTrackingService.php
│   │   ├── ReportingService.php
│   │   └── CurrencyService.php
│   └── Enums/
│       ├── ProjectStatus.php
│       ├── InvoiceStatus.php
│       └── PaymentMethod.php
├── database/
│   └── migrations/
│       ├── 001_create_clients_table.php
│       ├── 002_create_projects_table.php
│       ├── 003_create_time_entries_table.php
│       ├── 004_create_tasks_table.php
│       ├── 005_create_invoices_table.php
│       ├── 006_create_invoice_items_table.php
│       ├── 007_create_payments_table.php
│       └── 008_create_expenses_table.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   └── guest.blade.php
│   │   ├── livewire/
│   │   │   └── [komponenty views]
│   │   └── pdf/
│   │       └── invoice.blade.php
│   └── css/
│       └── app.css
└── routes/
    ├── web.php
    └── api.php
```

## 🗄️ Database Schema

### Migrations příklady:

```php
// 001_create_clients_table.php
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('company')->nullable();
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->string('vat_number')->nullable();
    $table->text('address')->nullable();
    $table->string('currency', 3)->default('CZK');
    $table->decimal('hourly_rate', 10, 2)->nullable();
    $table->json('settings')->nullable();
    $table->timestamps();
});

// 002_create_projects_table.php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained();
    $table->string('name');
    $table->text('description')->nullable();
    $table->enum('status', ['draft', 'active', 'on_hold', 'completed', 'archived']);
    $table->decimal('budget', 12, 2)->nullable();
    $table->decimal('hourly_rate', 10, 2)->nullable();
    $table->date('deadline')->nullable();
    $table->date('started_at')->nullable();
    $table->date('completed_at')->nullable();
    $table->string('color', 7)->default('#3B82F6');
    $table->timestamps();
});

// 003_create_time_entries_table.php
Schema::create('time_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('project_id')->constrained();
    $table->foreignId('task_id')->nullable()->constrained();
    $table->text('description');
    $table->integer('duration'); // in minutes
    $table->decimal('hourly_rate', 10, 2);
    $table->boolean('billable')->default(true);
    $table->boolean('billed')->default(false);
    $table->foreignId('invoice_item_id')->nullable();
    $table->date('date');
    $table->time('started_at')->nullable();
    $table->time('ended_at')->nullable();
    $table->timestamps();
});

// 005_create_invoices_table.php
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number')->unique();
    $table->foreignId('client_id')->constrained();
    $table->foreignId('project_id')->nullable()->constrained();
    $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled']);
    $table->date('issue_date');
    $table->date('due_date');
    $table->decimal('subtotal', 12, 2);
    $table->decimal('tax_rate', 5, 2)->default(0);
    $table->decimal('tax_amount', 12, 2)->default(0);
    $table->decimal('total', 12, 2);
    $table->string('currency', 3)->default('CZK');
    $table->text('notes')->nullable();
    $table->json('client_details'); // Snapshot klienta při vytvoření
    $table->date('paid_at')->nullable();
    $table->timestamps();
});
```

## 🎨 Tailwind Configuration

```javascript
// tailwind.config.js
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
```

## 🚦 První Livewire komponenty

### Dashboard Stats komponenta
```php
// app/Livewire/Dashboard/StatsOverview.php
<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;

class StatsOverview extends Component
{
    public $monthlyRevenue;
    public $unpaidInvoices;
    public $activeProjects;
    public $hoursThisWeek;

    public function mount()
    {
        $this->calculateStats();
    }

    public function calculateStats()
    {
        $this->monthlyRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->sum('total');

        $this->unpaidInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->sum('total');

        $this->activeProjects = Project::where('status', 'active')->count();

        $this->hoursThisWeek = TimeEntry::whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->sum('duration') / 60;
    }

    public function render()
    {
        return view('livewire.dashboard.stats-overview');
    }
}
```

### Timer komponenta
```php
// app/Livewire/TimeTracking/Timer.php
<?php

namespace App\Livewire\TimeTracking;

use Livewire\Component;
use App\Models\TimeEntry;
use App\Models\Project;
use Carbon\Carbon;

class Timer extends Component
{
    public $isRunning = false;
    public $startTime = null;
    public $duration = 0;
    public $selectedProjectId;
    public $description = '';
    public $projects;

    protected $rules = [
        'selectedProjectId' => 'required|exists:projects,id',
        'description' => 'required|min:3',
    ];

    public function mount()
    {
        $this->projects = Project::where('status', 'active')->get();
    }

    public function startTimer()
    {
        $this->validate(['selectedProjectId' => 'required']);
        
        $this->isRunning = true;
        $this->startTime = now();
        $this->dispatch('timer-started');
    }

    public function stopTimer()
    {
        if (!$this->isRunning) return;

        $this->validate();

        $duration = Carbon::parse($this->startTime)->diffInMinutes(now());
        
        TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $this->selectedProjectId,
            'description' => $this->description,
            'duration' => $duration,
            'date' => today(),
            'started_at' => Carbon::parse($this->startTime)->format('H:i:s'),
            'ended_at' => now()->format('H:i:s'),
            'hourly_rate' => Project::find($this->selectedProjectId)->hourly_rate,
        ]);

        $this->reset(['isRunning', 'startTime', 'duration', 'description']);
        $this->dispatch('timer-stopped');
    }

    public function render()
    {
        if ($this->isRunning && $this->startTime) {
            $this->duration = Carbon::parse($this->startTime)->diffInSeconds(now());
        }

        return view('livewire.time-tracking.timer');
    }
}
```

## 🎯 Claude Code instrukce

### Pro Claude Code použij tyto příkazy postupně:

```bash
# 1. Inicializace projektu
"Create a new Laravel project called freelanceflow with TALL stack (Tailwind, Alpine, Laravel, Livewire)"

# 2. Database setup
"Create all necessary migrations for freelance project management app with clients, projects, time entries, tasks, invoices, payments, and expenses tables"

# 3. Models
"Create all Eloquent models with proper relationships for the freelance app"

# 4. Livewire komponenty
"Create Livewire component for dashboard stats overview showing monthly revenue, unpaid invoices, active projects, and hours worked this week"

# 5. Timer feature
"Create a floating timer Livewire component with start/stop functionality that saves time entries to database"

# 6. Project management
"Create project CRUD with Livewire including list view with filters and detailed project page"

# 7. Invoice builder
"Create invoice builder Livewire component that can generate invoices from time entries"

# 8. PDF generation
"Implement PDF generation for invoices using dompdf with professional template"

# 9. Client portal
"Create guest access area where clients can view their invoices and project status"

# 10. Testing
"Create feature tests for main functionality"
```

## 🔥 Pro tips pro Claude Code:

1. **Vždy začni s:** `cd freelanceflow` před každým příkazem
2. **Požaduj TDD:** "Write tests first, then implement the feature"
3. **UI komponenty:** "Use Tailwind UI design patterns for [component]"
4. **Real-time features:** "Make this reactive with Livewire wire:poll or events"
5. **Validace:** "Add proper form validation with real-time feedback"

## 🎨 UI Design guidelines:

- **Primary color:** Blue (#3B82F6)
- **Border radius:** Mírně zaoblené (rounded-lg)
- **Shadows:** Jemné (shadow-sm, hover:shadow-md)
- **Spacing:** Consistent padding (p-6 pro cards, p-4 pro menší sekce)
- **Typography:** Inter font, clean hierarchy
- **Dark mode:** Připravit classes pro dark mode support

## 📱 Responsive breakpoints:

- Mobile first approach
- `sm:` (640px) - Tablets portrait
- `md:` (768px) - Tablets landscape  
- `lg:` (1024px) - Desktop
- `xl:` (1280px) - Large screens

## ⚡ Performance optimalizace:

1. Eager loading relationships
2. Database indexy na foreign keys
3. Livewire lazy loading pro heavy komponenty
4. Vite pro asset bundling
5. Redis cache pro dashboard stats

Tohle je tvůj complete blueprint pro FreelanceFlow! 🚀