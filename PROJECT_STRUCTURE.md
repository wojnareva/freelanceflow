# FreelanceFlow - Struktura projektu

## 📁 Přehled adresářů a souborů

```
freelanceflow/
├── 📁 app/                                    # Hlavní aplikační logika
│   ├── 📁 Console/Commands/                   # Artisan příkazy
│   │   └── 📄 [1 příkaz]
│   ├── 📁 Enums/                             # Výčtové typy
│   │   ├── 📄 Currency.php                   # Měny
│   │   ├── 📄 InvoiceStatus.php              # Stavy faktur
│   │   ├── 📄 ProjectStatus.php              # Stavy projektů
│   │   └── 📄 TaskStatus.php                 # Stavy úkolů
│   ├── 📁 Http/                              # HTTP vrstva
│   │   ├── 📁 Controllers/                   # Kontrolery (19 souborů)
│   │   ├── 📁 Middleware/                    # Middleware (1 soubor)
│   │   └── 📁 Requests/                      # Formulářové požadavky (2 soubory)
│   ├── 📁 Livewire/                          # Livewire komponenty
│   │   ├── 📁 Clients/                       # Klient komponenty (3 soubory)
│   │   ├── 📁 Dashboard/                     # Dashboard komponenty (4 soubory)
│   │   ├── 📁 Expenses/                      # Výdaje komponenty (3 soubory)
│   │   ├── 📁 InvoiceTemplates/              # Šablony faktur (1 soubor)
│   │   ├── 📁 Invoicing/                     # Fakturace komponenty (2 soubory)
│   │   ├── 📁 Projects/                      # Projekt komponenty (4 soubory)
│   │   ├── 📁 Reports/                       # Reporty (1 soubor)
│   │   ├── 📁 TimeTracking/                  # Sledování času (4 soubory)
│   │   ├── 📄 CurrencySelector.php           # Výběr měny
│   │   ├── 📄 DarkModeToggle.php             # Přepínač tmavého režimu
│   │   └── 📄 LocaleSelector.php             # Výběr jazyka
│   ├── 📁 Models/                            # Eloquent modely
│   │   ├── 📄 Attachment.php                 # Přílohy
│   │   ├── 📄 Client.php                     # Klienti
│   │   ├── 📄 Expense.php                    # Výdaje
│   │   ├── 📄 Invoice.php                    # Faktury
│   │   ├── 📄 InvoiceItem.php                # Položky faktur
│   │   ├── 📄 InvoiceTemplate.php            # Šablony faktur
│   │   ├── 📄 Payment.php                    # Platby
│   │   ├── 📄 Project.php                    # Projekty
│   │   ├── 📄 Task.php                       # Úkoly
│   │   ├── 📄 TimeEntry.php                  # Časové záznamy
│   │   ├── 📄 User.php                       # Uživatelé
│   │   └── 📄 Webhook.php                    # Webhooky
│   ├── 📁 Observers/                         # Model pozorovatelé
│   │   ├── 📄 InvoiceObserver.php            # Pozorovatel faktur
│   │   ├── 📄 PaymentObserver.php            # Pozorovatel plateb
│   │   └── 📄 ProjectObserver.php            # Pozorovatel projektů
│   ├── 📁 Providers/                         # Service providery
│   │   └── 📄 AppServiceProvider.php         # Hlavní service provider
│   ├── 📁 Rules/                             # Vlastní validační pravidla
│   │   └── 📄 ValidIco.php                   # Validace IČO
│   ├── 📁 Services/                          # Business logika
│   │   ├── 📄 AresService.php                # ARES API služba
│   │   ├── 📄 CurrencyService.php            # Měnová služba
│   │   ├── 📄 LocalizationService.php        # Lokalizační služba
│   │   └── 📄 WebhookService.php             # Webhook služba
│   ├── 📁 View/Components/                   # Blade komponenty (2 soubory)
│   └── 📄 helpers.php                        # Pomocné funkce
├── 📁 bootstrap/                             # Bootstrap soubory
│   ├── 📁 cache/                             # Cache soubory
│   │   ├── 📄 packages.php
│   │   └── 📄 services.php
│   ├── 📄 app.php                            # Hlavní bootstrap
│   └── 📄 providers.php                      # Service providery
├── 📁 config/                                # Konfigurační soubory
│   ├── 📄 app.php                            # Hlavní konfigurace
│   ├── 📄 auth.php                           # Autentifikace
│   ├── 📄 cache.php                          # Cache
│   ├── 📄 database.php                       # Databáze
│   ├── 📄 filesystems.php                    # Souborové systémy
│   ├── 📄 logging.php                        # Logování
│   ├── 📄 mail.php                           # Email
│   ├── 📄 queue.php                          # Fronty
│   ├── 📄 services.php                       # Externí služby
│   └── 📄 session.php                        # Session
├── 📁 database/                              # Databáze
│   ├── 📄 database.sqlite                    # SQLite databáze
│   ├── 📁 factories/                         # Model factory
│   │   ├── 📄 ClientFactory.php
│   │   ├── 📄 ExpenseFactory.php
│   │   ├── 📄 InvoiceFactory.php
│   │   ├── 📄 InvoiceItemFactory.php
│   │   ├── 📄 InvoiceTemplateFactory.php
│   │   ├── 📄 PaymentFactory.php
│   │   ├── 📄 ProjectFactory.php
│   │   ├── 📄 TaskFactory.php
│   │   ├── 📄 TimeEntryFactory.php
│   │   └── 📄 UserFactory.php
│   ├── 📁 migrations/                        # Migrace (22 souborů)
│   └── 📁 seeders/                           # Seedery
│       └── 📄 DatabaseSeeder.php
├── 📁 docs/                                  # Dokumentace
│   ├── 📄 DEVELOPMENT_GUIDE.md               # Vývojový průvodce
│   ├── 📄 PHASE_CHECKLIST.md                 # Checklist fází
│   └── 📄 QUICK_REFERENCE.md                 # Rychlý odkaz
├── 📁 node_modules/                          # Node.js závislosti
├── 📁 phases/                                # Dokumentace fází
│   ├── 📄 freelanceflow-setup.md
│   ├── 📄 phase-1.md
│   ├── 📄 phase-2.md
│   ├── 📄 phase-3.md
│   ├── 📄 phase-4.md
│   └── 📄 phase-5.md
├── 📁 public/                                # Veřejné soubory
│   ├── 📁 build/                             # Sestavené assety
│   │   ├── 📁 assets/                        # CSS, JS soubory
│   │   └── 📄 manifest.json                  # Manifest
│   ├── 📄 favicon.ico                        # Favicon
│   ├── 📄 index.php                          # Vstupní bod
│   └── 📄 robots.txt                         # Robots.txt
├── 📁 resources/                             # Zdroje
│   ├── 📁 css/                               # CSS soubory
│   │   └── 📄 app.css                        # Hlavní CSS
│   ├── 📁 js/                                # JavaScript soubory
│   │   ├── 📄 app.js                         # Hlavní JS
│   │   └── 📄 bootstrap.js                   # Bootstrap JS
│   ├── 📁 lang/                              # Jazykové soubory
│   │   └── 📁 cs/                            # Čeština (8 souborů)
│   └── 📁 views/                             # Blade šablony
│       ├── 📁 auth/                          # Autentifikace (6 souborů)
│       ├── 📁 clients/                       # Klienti (3 soubory)
│       ├── 📁 components/                    # Komponenty (13 souborů)
│       ├── 📁 expenses/                      # Výdaje (2 soubory)
│       ├── 📁 invoices/                      # Faktury (4 soubory)
│       ├── 📁 layouts/                       # Layouty (3 soubory)
│       ├── 📁 livewire/                      # Livewire šablony (24 souborů)
│       ├── 📁 profile/                       # Profil (4 soubory)
│       ├── 📁 projects/                      # Projekty (5 souborů)
│       ├── 📁 time-tracking/                 # Sledování času (3 soubory)
│       ├── 📄 dashboard.blade.php            # Dashboard
│       └── 📄 welcome.blade.php              # Uvítací stránka
├── 📁 routes/                                # Routing
│   ├── 📄 api.php                            # API routes
│   ├── 📄 auth.php                           # Auth routes
│   ├── 📄 console.php                        # Console routes
│   └── 📄 web.php                            # Web routes
├── 📁 storage/                               # Storage
│   ├── 📁 app/                               # Aplikační soubory
│   │   ├── 📁 private/                       # Privátní soubory
│   │   └── 📁 public/                        # Veřejné soubory
│   ├── 📁 debugbar/                          # Debug bar
│   ├── 📁 framework/                         # Framework cache
│   │   ├── 📁 cache/                         # Cache
│   │   ├── 📁 sessions/                      # Session
│   │   ├── 📁 testing/                       # Test cache
│   │   └── 📁 views/                         # View cache
│   └── 📁 logs/                              # Logy
│       └── 📄 laravel.log                    # Laravel log
├── 📁 tests/                                 # Testy
│   ├── 📁 Feature/                           # Feature testy
│   │   ├── 📁 Auth/                          # Auth testy (6 souborů)
│   │   ├── 📄 AresIntegrationFeatureTest.php # ARES integrace
│   │   ├── 📄 ClientManagementTest.php       # Správa klientů
│   │   ├── 📄 CurrencyServiceTest.php        # Měnová služba
│   │   ├── 📄 CzechLocalizationFeatureTest.php # Česká lokalizace
│   │   ├── 📄 DashboardTest.php              # Dashboard
│   │   ├── 📄 ExampleTest.php                # Příklad
│   │   └── 📄 ProfileTest.php                # Profil
│   ├── 📁 Unit/                              # Unit testy
│   │   ├── 📄 ExampleTest.php                # Příklad
│   │   └── 📄 LocalizationTest.php           # Lokalizace
│   └── 📄 TestCase.php                       # Základní test case
├── 📁 vendor/                                # Composer závislosti
├── 📄 .env.example                           # Příklad prostředí
├── 📄 artisan                                # Artisan CLI
├── 📄 CLAUDE.md                              # Claude instrukce
├── 📄 composer.json                          # Composer konfigurace
├── 📄 composer.lock                          # Composer lock
├── 📄 cookies.txt                            # Cookies
├── 📄 czech.md                               # České požadavky
├── 📄 export.txt                             # Export
├── 📄 locale-fix-instructions.md             # Instrukce pro opravu locale
├── 📄 package.json                           # NPM konfigurace
├── 📄 package-lock.json                      # NPM lock
├── 📄 phpunit.xml                            # PHPUnit konfigurace
├── 📄 postcss.config.js                      # PostCSS konfigurace
├── 📄 README.md                              # Dokumentace
├── 📄 tailwind.config.js                     # Tailwind konfigurace
└── 📄 vite.config.js                         # Vite konfigurace
```

## 🏗️ Architektura aplikace

### **Backend (Laravel)**
- **Modely**: 12 hlavních modelů pro správu dat
- **Kontrolery**: 19 kontrolerů pro API a web rozhraní
- **Livewire komponenty**: 30+ komponent pro interaktivní UI
- **Služby**: 4 specializované služby (ARES, měny, lokalizace, webhooky)

### **Frontend (TALL Stack)**
- **Tailwind CSS**: Styling a design systém
- **Alpine.js**: Reaktivní JavaScript
- **Laravel**: Backend framework
- **Livewire**: Full-stack komponenty

### **Databáze**
- **SQLite**: Vývojová databáze
- **22 migrací**: Kompletní schéma databáze
- **Factory pattern**: Testovací data

### **Testování**
- **Feature testy**: 8 hlavních testů
- **Unit testy**: 2 unit testy
- **PHPUnit**: Testovací framework

## 🎯 Hlavní moduly

1. **Dashboard** - Přehled statistik a aktivit
2. **Time Tracking** - Sledování času s plovoucím timerem
3. **Projects** - Správa projektů s Kanban boardem
4. **Clients** - Správa klientů s ARES integrací
5. **Invoicing** - Fakturace s PDF generováním
6. **Expenses** - Sledování výdajů
7. **Reports** - Finanční reporty
8. **Settings** - Nastavení aplikace

## 🌐 Lokalizace

- **Čeština**: Kompletní překlad (8 souborů)
- **ARES API**: Integrace pro české firmy
- **IČO validace**: Český algoritmus validace
- **Formátování**: České formáty čísel, měn a datumů


---