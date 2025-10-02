# Taptura - Architektura aplikace

## 🏗️ Celkový koncept

```
taptura/
├── 🌐 marketing/                                 # Marketingový web (Astro)
│   ├── 📁 src/
│   │   ├── 📁 pages/                             # Statické stránky
│   │   │   ├── 📄 index.astro                    # Landing page
│   │   │   ├── 📄 funkce.astro                   # Funkce
│   │   │   ├── 📄 cenik.astro                    # Ceník
│   │   │   └── 📁 blog/                          # Blog
│   │   ├── 📁 components/                        # Vue komponenty
│   │   │   ├── 📄 Hero.vue                       # Hlavní banner
│   │   │   ├── 📄 Pricing.vue                    # Ceník
│   │   │   └── 📄 Testimonials.vue               # Reference
│   │   └── 📁 layouts/
│   │       └── 📄 Layout.astro                   # Základní layout
│   └── 📄 astro.config.mjs                       # Astro konfigurace
│
├── 📱 app/                                       # Hlavní aplikace (Vue + Capacitor)
│   ├── 📁 src/
│   │   ├── 📁 views/                             # Stránky aplikace
│   │   │   ├── 📄 Login.vue                      # Přihlášení
│   │   │   ├── 📄 Dashboard.vue                  # Dashboard
│   │   │   ├── 📄 Timer.vue                      # Časovač
│   │   │   ├── 📄 Projects.vue                   # Projekty
│   │   │   ├── 📄 Clients.vue                    # Klienti
│   │   │   ├── 📄 Invoices.vue                   # Faktury
│   │   │   └── 📄 Expenses.vue                   # Výdaje
│   │   ├── 📁 components/                        # UI komponenty
│   │   │   ├── 📄 TimerWidget.vue                # Plovoucí časovač
│   │   │   ├── 📄 ProjectCard.vue                # Karta projektu
│   │   │   ├── 📄 InvoiceBuilder.vue             # Tvůrce faktur
│   │   │   └── 📄 SyncIndicator.vue              # Indikátor synchronizace
│   │   ├── 📁 stores/                            # Pinia stores
│   │   │   ├── 📄 auth.ts                        # Autentifikace
│   │   │   ├── 📄 projects.ts                    # Projekty
│   │   │   ├── 📄 timeEntries.ts                 # Časové záznamy
│   │   │   └── 📄 sync.ts                        # Synchronizace
│   │   ├── 📁 services/                          # Služby
│   │   │   ├── 📄 api.ts                         # API klient
│   │   │   ├── 📄 db.ts                          # SQLite wrapper
│   │   │   ├── 📄 sync.ts                        # Sync služba
│   │   │   └── 📄 offline.ts                     # Offline logika
│   │   ├── 📁 types/                             # TypeScript typy
│   │   │   ├── 📄 TimeEntry.ts                   # Časové záznamy
│   │   │   ├── 📄 Project.ts                     # Projekty
│   │   │   ├── 📄 Invoice.ts                     # Faktury
│   │   │   └── 📄 Client.ts                      # Klienti
│   │   └── 📁 router/                            # Vue Router
│   │       └── 📄 index.ts                       # Routing
│   ├── 📁 android/                               # Android projekt
│   ├── 📁 ios/                                   # iOS projekt
│   ├── 📄 capacitor.config.ts                    # Capacitor konfigurace
│   └── 📄 vite.config.ts                         # Vite konfigurace
│
└── 🔧 api/                                       # Backend API (Laravel)
    ├── 📁 app/
    │   ├── 📁 Http/Controllers/Api/               # API kontrolery
    │   │   ├── 📄 TimeEntryController.php         # Časové záznamy
    │   │   ├── 📄 ProjectController.php           # Projekty
    │   │   ├── 📄 ClientController.php            # Klienti
    │   │   ├── 📄 InvoiceController.php           # Faktury
    │   │   ├── 📄 ExpenseController.php           # Výdaje
    │   │   ├── 📄 SyncController.php              # Synchronizace
    │   │   └── 📄 PublicApiController.php         # Veřejné API
    │   ├── 📁 Models/                             # Eloquent modely
    │   │   ├── 📄 TimeEntry.php                   # Časové záznamy
    │   │   ├── 📄 Project.php                     # Projekty
    │   │   ├── 📄 Client.php                      # Klienti
    │   │   ├── 📄 Invoice.php                     # Faktury
    │   │   ├── 📄 Expense.php                     # Výdaje
    │   │   └── 📄 User.php                        # Uživatelé
    │   ├── 📁 Services/                           # Business logika
    │   │   ├── 📄 AresService.php                 # ARES API
    │   │   ├── 📄 FioBankService.php              # Fio banka API
    │   │   ├── 📄 RossumService.php               # OCR faktury
    │   │   ├── 📄 AIChatService.php               # AI asistent
    │   │   └── 📄 SyncService.php                 # Synchronizace
    │   └── 📁 Http/Resources/                     # API resources
    │       ├── 📄 TimeEntryResource.php           # Časové záznamy
    │       ├── 📄 ProjectResource.php             # Projekty
    │       └── 📄 InvoiceResource.php             # Faktury
    ├── 📁 database/
    │   ├── 📁 migrations/                         # Databázové migrace
    │   │   ├── 📄 create_users_table.php          # Uživatelé
    │   │   ├── 📄 create_projects_table.php       # Projekty
    │   │   ├── 📄 create_time_entries_table.php   # Časové záznamy
    │   │   ├── 📄 create_clients_table.php        # Klienti
    │   │   ├── 📄 create_invoices_table.php       # Faktury
    │   │   └── 📄 create_expenses_table.php       # Výdaje
    │   └── 📁 seeders/                            # Testovací data
    │       └── 📄 DatabaseSeeder.php              # Hlavní seeder
    └── 📁 routes/
        ├── 📄 api.php                             # Interní API routes
        └── 📄 public-api.php                      # Veřejné API routes
```

## 🎯 Technologický stack

### **Marketing web (taptura.cz)**
- **Astro 4.x**: Ultra rychlé statické stránky
- **Vue 3**: Interaktivní komponenty
- **Tailwind CSS**: Styling
- **Hosting**: Vercel/Netlify (zdarma)

### **Aplikace (app.taptura.cz)**
- **Vite + Vue 3 + TypeScript**: SPA framework
- **Vue Router 4**: Routing
- **Pinia**: State management
- **Tailwind CSS + HeadlessUI**: UI komponenty
- **Capacitor 7**: Mobilní wrapper
- **@capacitor-community/sqlite**: Offline databáze
- **Axios**: HTTP klient

### **Backend API (api.taptura.cz)**
- **Laravel 12**: PHP framework
- **PostgreSQL 18**: Produkční databáze
- **Laravel Sanctum**: API autentifikace
- **Redis**: Cache a fronty
- **Laravel Queue**: Asynchronní úlohy

## 🗄️ Databázová architektura

### **PostgreSQL (Cloud)**
```sql
-- Hlavní tabulky
users                    # Uživatelé aplikace
projects                 # Projekty
time_entries            # Časové záznamy
clients                 # Klienti
invoices                # Faktury
invoice_items           # Položky faktur
expenses                # Výdaje
payments                # Platby

-- Sync metadata
sync_queue              # Fronta synchronizace
api_logs                # Log API volání
```

### **SQLite (Offline)**
```sql
-- Stejná struktura jako PostgreSQL + sync sloupce
time_entries            # + synced, server_id, updated_at
projects                # + synced, server_id, updated_at
clients                 # + synced, server_id, updated_at
sync_queue              # Pending operace
```

## 🔌 API Endpoints

### **Interní API (Sanctum auth)**
```
GET    /api/v1/time-entries          # Seznam časových záznamů
POST   /api/v1/time-entries          # Vytvoř časový záznam
PUT    /api/v1/time-entries/{id}     # Aktualizuj záznam
DELETE /api/v1/time-entries/{id}     # Smaž záznam

GET    /api/v1/projects              # Seznam projektů
POST   /api/v1/projects              # Vytvoř projekt
PUT    /api/v1/projects/{id}         # Aktualizuj projekt

GET    /api/v1/clients               # Seznam klientů
POST   /api/v1/clients               # Vytvoř klienta
GET    /api/v1/clients/search-ares   # Vyhledej v ARES

GET    /api/v1/invoices              # Seznam faktur
POST   /api/v1/invoices              # Vytvoř fakturu
GET    /api/v1/invoices/{id}/pdf     # Stáhni PDF

GET    /api/v1/sync/changes          # Změny pro sync
POST   /api/v1/sync/upload           # Upload lokálních změn
```

### **Veřejné API (API key auth)**
```
GET    /public/v1/time-entries       # Export časových záznamů
GET    /public/v1/invoices           # Export faktur
GET    /public/v1/export             # Export do CSV/JSON
POST   /public/v1/projects           # Vytvoř projekt (volitelné)
```

### **Cizí API integrace**
```
ARES API (ares.gov.cz)               # Vyhledání českých firem
Fio Bank API (fioapi.fio.cz)         # Bankovní výpisy
Rossum API (api.elis.rossum.ai)      # OCR faktury
OpenAI/Claude API                    # AI asistent
```

## 📱 Offline synchronizace

### **Architektura**
```
┌─────────────────────────────────────┐
│  ZAŘÍZENÍ (mobil/web)               │
│  ┌─────────────────────────────┐   │
│  │ SQLite databáze              │   │
│  │ - time_entries (100%)        │   │
│  │ - projects (100%)            │   │
│  │ - sync_queue (pending ops)   │   │
│  └─────────────────────────────┘   │
│           ↕ sync                    │
└─────────────────────────────────────┘
           ↕ když je internet
┌─────────────────────────────────────┐
│  CLOUD (PostgreSQL)                 │
│  - Source of truth                  │
└─────────────────────────────────────┘
```

### **Sync strategie**
- **Local-first**: Všechna data vždy lokálně
- **Optimistic UI**: Okamžité zobrazení změn
- **Batch sync**: Každých 5 minut
- **Conflict resolution**: Last write wins
- **Retry logic**: Exponential backoff

## 🏦 Bankovní integrace

### **Fio banka (MVP)**
- **Cena**: ZDARMA
- **API**: Jednoduchý token
- **Funkce**: Stahování výpisů, párování plateb

### **Ostatní banky (Fáze 2)**
- **PSD2 API**: Všechny české banky
- **Agregátor**: GoCardless (€0.50/user/měsíc)
- **Fallback**: CSV import

## 🤖 AI asistence

### **Invoice generator**
- **Funkce**: Automatické generování položek faktur
- **Input**: Časové záznamy projektu
- **Output**: Profesionální popisy práce
- **Náklady**: ~$0.60/user/měsíc

### **Chat asistent**
- **Funkce**: Dotazy na data ("Kolik jsem vydělal?")
- **Kontext**: Přístup k uživatelským datům
- **Provider**: Claude 3.5 Sonnet

### **Weekly insights**
- **Funkce**: Analýza produktivity
- **Frekvence**: Každé pondělí
- **Obsah**: Statistiky, tipy, upomínky

## 📄 OCR a vytěžování dokumentů

### **Rossum.ai (Doporučeno)**
- **Cena**: 100 stránek/měsíc zdarma, pak €0.10/stránka
- **Přesnost**: 95-98% pro české faktury
- **Funkce**: Vytěžování faktur, účtenek, výpisů

### **Workflow**
1. **Upload**: Uživatel nahraje PDF/foto
2. **OCR**: Rossum vytěží strukturovaná data
3. **Review**: Uživatel zkontroluje a opraví
4. **Import**: Automatické uložení do systému
5. **Pairing**: Spárování s bankovními platbami

## 🚀 Deployment strategie

### **Marketing web**
- **Platforma**: Vercel/Netlify
- **Deploy**: Git push → auto deploy
- **Cena**: Zdarma

### **Aplikace (PWA)**
- **Platforma**: Vercel
- **Deploy**: Git push → auto deploy
- **Cena**: Zdarma

### **Mobilní aplikace**
- **iOS**: App Store Connect
- **Android**: Google Play Console
- **Build**: Capacitor → Xcode/Android Studio

### **Backend API**
- **Platforma**: Hetzner VPS / DigitalOcean
- **Management**: Laravel Forge
- **Cena**: ~$20-40/měsíc

## 📊 Monitoring a analytics

### **Error tracking**
- **Sentry**: Crash reporting
- **Laravel Telescope**: Debug toolbar

### **Performance**
- **Laravel Horizon**: Queue monitoring
- **Redis**: Cache monitoring

### **Business metrics**
- **Custom dashboard**: Uživatelé, revenue, usage
- **Google Analytics**: Marketing web

## 🔒 Bezpečnost

### **Autentifikace**
- **Sanctum**: Token-based API auth
- **2FA**: Volitelné dvoufaktorové ověření
- **Password reset**: Email verification

### **Data protection**
- **GDPR**: Compliant
- **Encryption**: At rest a in transit
- **Backup**: Denní zálohy

### **API security**
- **Rate limiting**: 1000 requests/hour
- **CORS**: Properly configured
- **Input validation**: Laravel validation

## 💰 Monetizace

### **Free tier**
- **Limity**: 3 projekty, 50 časových záznamů/měsíc
- **Funkce**: Základní time tracking, jednoduché faktury

### **Pro tier (299 Kč/měsíc)**
- **Limity**: Unlimited
- **Funkce**: Všechny funkce, AI asistent, bankovní integrace

### **Enterprise (na vyžádání)**
- **Funkce**: White-label, API access, priority support

---

*Architektura navržena pro škálovatelnost, offline-first přístup a český trh.*
