# FreelanceFlow 🚀

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-purple.svg)](https://livewire.laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-cyan.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**FreelanceFlow** is a comprehensive freelance business management application built with the Laravel TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire). This is a portfolio project showcasing modern web development practices and AI-assisted development workflow.

## ✨ Features

### 🕒 Time Tracking
- **Floating Timer Widget** - Always-visible timer for easy time tracking
- **Calendar Integration** - Visual time entry management
- **Bulk Time Editing** - Efficient time entry modifications
- **Project-based Tracking** - Organize time by projects and tasks

### 📋 Project Management
- **Kanban Boards** - Visual project workflow management
- **Timeline Views** - Project schedules and deadlines
- **Task Management** - Detailed task tracking with drag & drop
- **Project Templates** - Reusable project structures

### 💼 Client Management
- **Contact Database** - Comprehensive client information
- **Project History** - Track all client interactions
- **Revenue Analytics** - Client profitability insights
- **ARES Integration** (Czech Republic) - Automatic company data lookup

### 📄 Smart Invoicing
- **PDF Generation** - Professional invoice templates
- **Multi-Currency Support** - Global business ready
- **Recurring Invoices** - Automated billing cycles
- **Payment Tracking** - Monitor invoice statuses
- **Time-to-Invoice** - Convert tracked time directly to invoices

### 💰 Expense Tracking
- **Receipt Upload** - Digital expense documentation
- **Category Management** - Organized expense tracking
- **Tax Reporting** - Business expense summaries
- **Project Attribution** - Link expenses to specific projects

### 📊 Reports & Analytics
- **Revenue Charts** - Visual business performance
- **Time Analytics** - Productivity insights
- **Financial Reports** - Comprehensive business overview
- **Dashboard Widgets** - Key metrics at a glance

### 🌍 Localization
- **Multi-language Support** - Czech and English included
- **Currency Formatting** - Locale-specific number formats
- **Date Localization** - Regional date formatting
- **Expandable** - Easy to add new languages

### 🔧 Advanced Features
- **Two-Factor Authentication** - Enhanced security
- **API Endpoints** - Integration ready
- **Webhook Support** - External system integration
- **File Attachments** - Document management
- **Dark Mode** - Modern UI experience

## 🖥️ Demo

Experience FreelanceFlow with our live demo:

**Demo URL:** [http://your-demo-url.com](http://your-demo-url.com)

**Demo Credentials:**
- **Email:** demo@freelanceflow.app
- **Password:** password

The demo includes sample data with:
- 8 demo clients
- 15+ projects across different industries
- 200+ time entries
- 50+ invoices with various statuses
- Expense records and financial reports

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (default) or MySQL/PostgreSQL

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/freelanceflow.git
cd freelanceflow
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install JavaScript dependencies**
```bash
npm install
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Database setup**
```bash
php artisan migrate
php artisan db:seed
```

6. **Build assets**
```bash
npm run dev
# or for production
npm run build
```

7. **Start the development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` to see FreelanceFlow in action!

### Demo Data Seeding

To populate your local instance with realistic demo data:

```bash
# English demo data
php artisan db:seed --class=DemoEnSeeder

# Czech demo data (includes ARES integration examples)
php artisan db:seed --class=DemoCzSeeder
```

## 🛠️ Technology Stack

### Backend
- **Laravel 11** - PHP framework
- **Livewire 3** - Full-stack reactive components
- **SQLite/MySQL** - Database options
- **Laravel Sanctum** - API authentication
- **Laravel Queue** - Background job processing

### Frontend
- **TailwindCSS 3** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Heroicons** - Beautiful SVG icons
- **Chart.js** - Data visualization

### Development Tools
- **Laravel Pint** - Code formatting
- **PHPUnit** - Testing framework
- **Vite** - Modern build tool
- **Laravel Debugbar** - Development debugging

### Integrations
- **ARES API** - Czech company registry
- **DomPDF** - PDF generation
- **Laravel Mail** - Email functionality

## 📚 Documentation

### Project Structure
```
freelanceflow/
├── app/
│   ├── Livewire/          # Livewire components
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic
│   ├── Enums/            # Application enums
│   └── Rules/            # Custom validation rules
├── resources/
│   ├── views/
│   │   ├── livewire/     # Component templates
│   │   ├── layouts/      # Layout files
│   │   └── errors/       # Custom error pages
│   ├── lang/             # Localization files
│   └── css/              # Stylesheets
├── database/
│   ├── migrations/       # Database migrations
│   ├── seeders/          # Database seeders
│   └── factories/        # Model factories
└── tests/
    ├── Feature/          # Feature tests
    └── Unit/             # Unit tests
```

### Key Commands

```bash
# Development
php artisan serve              # Start development server
npm run dev                   # Watch for changes
php artisan test              # Run tests
./vendor/bin/pint            # Format code

# Database
php artisan migrate           # Run migrations
php artisan db:seed          # Seed database
php artisan migrate:fresh --seed  # Fresh database

# Cache
php artisan optimize:clear    # Clear all caches
php artisan config:cache     # Cache configuration
php artisan route:cache      # Cache routes

# Queue (for production)
php artisan queue:work       # Process background jobs
```

## 🧪 Testing

FreelanceFlow includes comprehensive tests covering:

- **Feature Tests** - Complete user workflows
- **Unit Tests** - Individual component logic
- **Livewire Tests** - Component behavior
- **API Tests** - Endpoint functionality

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

## 🌐 Deployment

### Production Environment

1. **Server Requirements**
   - PHP 8.2+ with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
   - Composer
   - Node.js (for building assets)
   - Web server (Apache/Nginx)
   - Database (MySQL/PostgreSQL recommended for production)

2. **Environment Variables**
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

MAIL_MAILER=smtp
# Configure your mail settings
```

3. **Deployment Commands**
```bash
composer install --optimize-autoloader --no-dev
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Deployment Options

#### Railway.app (Recommended)
- Easy one-click deployment
- Automatic SSL certificates
- Environment variable management
- Database hosting included

#### Render.com
- Git-based deployments
- Free tier available
- Automatic builds from GitHub

#### DigitalOcean App Platform
- Container-based deployment
- Scalable infrastructure
- Database add-ons

## 🤝 Contributing

We welcome contributions to FreelanceFlow! Please follow these guidelines:

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/amazing-feature`)
3. **Follow coding standards** (run `./vendor/bin/pint`)
4. **Write tests** for new functionality
5. **Commit your changes** (`git commit -m 'Add amazing feature'`)
6. **Push to the branch** (`git push origin feature/amazing-feature`)
7. **Open a Pull Request**

### Development Guidelines

- Follow PSR-12 coding standards
- Write descriptive commit messages
- Include tests for new features
- Update documentation as needed
- Ensure all tests pass before submitting

## 📄 License

FreelanceFlow is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👥 Credits

### Built With
- **Laravel** - The PHP Framework for Web Artisans
- **Livewire** - A magical frontend framework for Laravel
- **TailwindCSS** - A utility-first CSS framework
- **Alpine.js** - A rugged, minimal JavaScript framework

### Development Approach
This project was developed using AI-assisted development with Claude Code, showcasing:
- Modern Laravel development practices
- TALL stack implementation
- Test-driven development
- Comprehensive localization
- Professional UI/UX design

## 🐛 Issues & Support

If you encounter any issues or have questions:

1. **Check existing issues** in the GitHub repository
2. **Search documentation** for common solutions
3. **Create a new issue** with detailed description
4. **Include steps to reproduce** any bugs

## 🔄 Changelog

### Version 1.0.0 (Current)
- ✅ Complete TALL stack implementation
- ✅ Time tracking with floating timer
- ✅ Project management with kanban boards
- ✅ Client management with ARES integration
- ✅ Smart invoicing with PDF generation
- ✅ Expense tracking and reporting
- ✅ Multi-language support (EN/CS)
- ✅ Comprehensive test suite
- ✅ Professional UI with dark mode
- ✅ Demo data and seeding

### Roadmap
- 🔄 API rate limiting and throttling
- 🔄 Advanced reporting dashboard
- 🔄 Mobile app (React Native)
- 🔄 Advanced project templates
- 🔄 Team collaboration features
- 🔄 Payment gateway integration

---

**FreelanceFlow** - Streamline your freelance business with professional tools and modern technology.

Made with ❤️ using Laravel TALL Stack