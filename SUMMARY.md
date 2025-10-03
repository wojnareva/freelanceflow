# Taptura – Souhrn aplikace

## Co je aplikace
- Moderní SaaS pro freelancery: sledování času, projekty, klienti, faktury, výdaje, reporty, OCR a bankovní párování.
- Cíl: rychlé denní použití, offline‑first, české prostředí a legislativa.

## Architektura
- Marketing: Astro + Vue (taptura.cz), statický, SEO.
- Aplikace: Vite + Vue 3 + TypeScript + Capacitor 7 (app.taptura.cz, PWA + iOS/Android).
- Backend: Laravel 12 (api.taptura.cz), PostgreSQL 18, Redis, Sanctum.
- Offline: SQLite (mobil/PWA), sync queue, batch sync každých 5 min.

## Klíčové moduly
- Dashboard: metriky, grafy, upozornění (po splatnosti, AI tipy).
- Time Tracking: plovoucí timer, kalendář, CRUD, hromadné úpravy.
- Projekty/Úkoly: filtry, timeline, kanban (drag & drop).
- Klienti: CRUD, ARES autofill (IČO/DIČ/adresa), historie.
- Faktury: generace z času, šablony, PDF, e‑mail, opakované.
- Výdaje: OCR/scan, kategorizace, párování s bankou.
- Reporty: čas, výnosy, faktury, exporty.
- Přílohy: soubory k fakturám/výdajům.
- AI: generátor položek faktur, chat k datům, týdenní „insights“.

## Datový model (výběr)
- Users, Clients, Projects, Tasks, TimeEntries
- Invoices, InvoiceItems, Payments
- Expenses, Attachments
- Webhooks, ApiLogs

## Lokalizace a CZ specifika
- Kompletní čeština (texty, validace, formát čísel/měn/dat).
- ARES integrace (IČO/DIČ), QR platba, ČNB kurzy.
- DIČ/VS, český formát faktur.

## Offline a synchronizace
- Local‑first: vše se zapisuje okamžitě do SQLite.
- Sync queue (create/update/delete), pull/push, „last write wins“.
- Triggery: při startu, každých 5 min, návrat z pozadí, změna sítě.

## Bankovní integrace
- MVP: Fio API (zdarma) — stahování transakcí, automatické párování.
- Fáze 2: PSD2 agregátor (GoCardless) + manuální CSV import.

## AI a OCR
- AI (Claude/OpenAI): položky faktur, popisy času, chat, weekly insights.
- OCR: Rossum.ai (95–98 % přesnost), fallback Google Vision + regex.

## Zabezpečení
- Interní API: Sanctum (Bearer).
- Public API: `X-API-Key`, logování do `api_logs`, rate limiting.
- 2FA (volitelné), role/policies, audit logy kritických akcí.

## Deployment a provoz
- Marketing/PWA: Vercel/Netlify.
- Backend: Hetzner/DigitalOcean + Laravel Forge, Redis/Queue.
- Monitorování: Sentry, Telescope, Horizon; zálohy DB.

## API katalog
Konvence: JSON, camelCase payloady, ISO8601 časy; stránkování `?page=`, filtrování přes query parametry. Chyby: 4xx/5xx s `error/message`.

### Interní API (auth: Sanctum, prefix `/api/v1`)
- Time entries
  - GET `/time-entries` — list (+ filtrování `from`, `to`, `project_id`)
  - POST `/time-entries` — vytvořit
  - GET `/time-entries/{id}` — detail
  - PUT `/time-entries/{id}` — upravit (konflikty 409 podle `client_updated_at`)
  - DELETE `/time-entries/{id}` — smazat
- Projects
  - GET `/projects`
  - POST `/projects`
  - GET `/projects/{id}`
  - PUT `/projects/{id}`
  - DELETE `/projects/{id}`
- Clients
  - GET `/clients`
  - POST `/clients`
  - GET `/clients/{id}`
  - PUT `/clients/{id}`
  - DELETE `/clients/{id}`
  - GET `/clients/search-ares?ico=12345678` — ARES lookup
  - POST `/clients/from-ares` — vytvoření klienta z ARES
- Invoices
  - GET `/invoices` — list (+ filtrování `status`, `from`, `to`)
  - POST `/invoices` — vytvořit (z času nebo ručně)
  - GET `/invoices/{id}`
  - PUT `/invoices/{id}`
  - DELETE `/invoices/{id}`
  - GET `/invoices/{id}/pdf` — stáhnout PDF
- Expenses
  - GET `/expenses`
  - POST `/expenses`
  - GET `/expenses/{id}`
  - PUT `/expenses/{id}`
  - DELETE `/expenses/{id}`
  - POST `/expenses/upload-invoice` — upload + OCR (Rossum)
- Payments
  - GET `/payments` — volitelné (nebo jen vnořené v fakturách)
  - POST `/payments` — ruční záznam platby
- Sync
  - GET `/sync/changes?since=ISO8601` — pull změn
  - POST `/sync/upload` — batch push pending operací
- Bank (Fio)
  - POST `/bank/connect/fio` — uložit token a účet
  - GET `/bank/test` — test spojení
  - POST `/bank/sync` — stáhnout nové transakce
  - DELETE `/bank/connection` — odpojit
- AI
  - POST `/ai/invoice-items` — generovat položky faktury (vstup: `invoice_id`)
  - POST `/ai/chat` — chat k datům (vstup: `message`, `history[]`)
  - POST `/ai/suggest-description` — 3 návrhy popisu pro time entry

### Public API (auth: `X-API-Key`, prefix `/public/v1`)
- GET `/time-entries?from=YYYY-MM-DD&to=...` — export časových záznamů
- GET `/invoices?status=paid|unpaid|all` — export faktur
- GET `/export?type=time-entries|invoices|projects&format=json|csv` — hromadný export
- POST `/projects` — (volitelné) vytvoření projektu

## Poznámky k verzím
- Laravel 12 — aktuální LTS, bez změn v API vrstvě z pohledu klienta.
- Capacitor 7 — požaduje Node 20+, Xcode 16+, JDK 21; uprav `capacitor.config.*` a pluginy.
- PostgreSQL 18 — zvaž `uuidv7()` pro UUID defaulty, přínos v I/O a EXPLAIN/monitoringu.
