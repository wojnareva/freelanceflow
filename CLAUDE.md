# Claude Code Instructions - FreelanceFlow Project

## 🎯 Project Overview
You are building **FreelanceFlow** - a comprehensive freelance business management application using Laravel TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire). This is a portfolio project showcasing modern web development practices and AI-assisted development workflow.

## ⚠️ CRITICAL: Password & Permission Handling
**NEVER attempt to run commands that require:**
- `sudo` privileges
- Password input
- Interactive authentication
- Database passwords
- API keys or tokens

**When you encounter such commands:**
1. STOP immediately
2. Output this format:
```
⚠️ MANUAL ACTION REQUIRED:
Please run the following command(s) in your terminal:

[command that needs sudo/password]

Type "done" when completed, and I'll continue.
```
3. WAIT for user confirmation before proceeding
4. Continue only after user confirms completion

**Common examples:**
- `sudo apt-get install`
- `mysql -u root -p`
- `railway login`
- Global npm installs requiring sudo
- Any deployment authentication

## 🔧 MCP Tools Configuration
Ensure these MCP tools are enabled and use them actively:
- **filesystem** - for all file operations
- **github** - for version control
- **browser** - for testing UI and user flows
- **fetch** - for API endpoint testing

## 📋 Git Workflow Rules
1. **Commit frequently** - After each meaningful change or feature completion
2. **Auto-generate commit messages** following conventional commits:
   - `feat:` - new feature
   - `fix:` - bug fix
   - `refactor:` - code improvement
   - `style:` - UI/UX changes
   - `test:` - adding tests
   - `docs:` - documentation
   - `chore:` - maintenance tasks

3. **Git commands to execute automatically:**
   ```bash
   git add .
   git commit -m "feat: [description of what was added]"
   git push origin main
   ```

4. **Branch strategy:**
   - Work on `main` for initial development
   - Create feature branches for major features: `feature/invoice-builder`
   - Always push changes before switching context

## 🏗️ Project Structure
```
freelanceflow/
├── app/
│   ├── Livewire/       # All Livewire components
│   ├── Models/         # Eloquent models
│   ├── Services/       # Business logic
│   └── Enums/          # Status enums
├── resources/
│   ├── views/
│   │   ├── livewire/   # Livewire component views
│   │   └── layouts/    # Layout templates
│   └── css/
└── tests/
    ├── Feature/        # Feature tests
    └── Unit/          # Unit tests
```

## ✅ Development Checklist

### Phase 1: Foundation
- [x] Initialize Laravel project with TALL stack
- [x] Configure database (SQLite for development)
- [x] Setup Git repository and push initial commit
- [x] Install and configure all required packages
- [x] Create base authentication system
- [x] Design and implement base layout with navigation
- [x] Setup dark mode support

### Phase 2: Database & Models
- [x] Create all migrations with proper foreign keys
- [x] Implement Eloquent models with relationships
- [x] Add model factories for testing
- [x] Seed database with sample data
- [x] Test all relationships work correctly

### Phase 3: Core Features - COMPLETED ✅
- [x] **Dashboard Module**
  - [x] Stats overview cards (revenue, projects, hours)
  - [x] Activity feed component
  - [x] Revenue chart (last 6 months)
  - [x] Quick actions widget
  
- [x] **Time Tracking Module** ✅
  - [x] Floating timer component (with project selection from 11 projects)
  - [x] Time entries CRUD
  - [x] Calendar view
  - [x] Bulk time entry editor
  
- [x] **Projects Module** ✅
  - [x] Project list with filters (status, client, search)
  - [x] Project detail page
  - [x] Kanban board for tasks (with drag & drop)
  - [x] Project timeline view (single + all projects)
  - [x] Project CRUD with full form (status, dates, budget, rates)
  - [x] Task management with cascade deletion

### Phase 4: Invoicing & Clients - COMPLETED ✅

- [x] **Invoicing Module** ✅
  - [x] Invoice main views (index, create, show)
  - [x] Invoice builder from time entries
  - [x] Invoice templates
  - [x] PDF generation
  - [x] Email sending (basic setup)
  - [x] Payment tracking
  
- [x] **Clients Module** ✅
  - [x] Client CRUD (Create, Read, Update, Delete)
  - [x] Client project history and stats
  - [x] Contact management with search functionality
  - [x] User scoping and proper relationships

### Phase 5: Advanced Features - COMPLETED ✅
- [x] Multi-currency support
- [x] Recurring invoices
- [x] Expense tracking
- [x] Financial reports
- [x] API endpoints
- [x] Webhook integrations
- [x] Two-factor authentication
- [x] File attachments handling

### Phase 6: Czech Localization & ARES Integration - COMPLETED ✅
- [x] **Read and analyze czech.md file** (First task for Claude Code)
- [x] Setup Laravel localization configuration
- [x] Create SetLocale middleware and locale detection
- [x] Add locale preferences to users table migration
- [x] Implement LocalizationService helper class
- [x] Create complete Czech translation files (app, dashboard, clients, projects, invoices, time, auth, validation)
- [x] Implement ARES API service for company data lookup
- [x] Add IČO/DIČ fields to clients table
- [x] Create ValidIco validation rule with Czech IČO algorithm
- [x] Build LocaleSelector Livewire component
- [x] Extend ClientForm with ARES integration and auto-fill
- [x] Implement Czech number/currency/date formatting
- [x] Add locale selector to registration and user settings
- [x] Create comprehensive tests for localization and ARES API
- [x] Mobile responsive testing for Czech UI

### Phase 7: Polish & Testing
- [ ] Write comprehensive feature tests
- [ ] Add loading states and animations
- [ ] Implement keyboard shortcuts
- [ ] Mobile responsive testing
- [ ] Performance optimization
- [ ] Error handling improvements
- [ ] Add help tooltips

### Phase 8: Production Ready
- [ ] Environment configuration
- [ ] Database indexing optimization
- [ ] Cache implementation
- [ ] Queue setup for heavy tasks
- [ ] Deployment configuration
- [ ] Documentation completion
- [ ] **Demo data seeding** (realistic sample data)
- [ ] **Demo account setup** (demo@freelanceflow.app / password)
- [ ] **Landing page** with feature showcase
- [ ] **Public demo link** in README

## 💻 Development Commands

### Commands that need manual execution:
```bash
# These require manual action - CC will ask you to run them:
sudo apt-get update
sudo apt-get install php-xml php-curl
mysql -u root -p
sudo npm install -g something
railway login
fly auth login
```

### After each feature, run:
```bash
# Test the feature
php artisan test

# Check code quality
./vendor/bin/pint

# Clear caches if needed
php artisan optimize:clear

# Run the app and test in browser
php artisan serve
npm run dev
```

### MCP Browser Testing
After implementing each UI component:
1. Use browser tool to navigate to the feature
2. Test all interactions
3. Verify responsive design
4. Check for console errors

## 🎨 UI/UX Guidelines

### Design System
- **Primary Color:** Blue (#3B82F6)
- **Success:** Green (#10B981)
- **Warning:** Yellow (#F59E0B)
- **Danger:** Red (#EF4444)
- **Border Radius:** Use `rounded-lg` consistently
- **Shadows:** `shadow-sm` default, `shadow-md` on hover
- **Spacing:** Use multiples of 4 (p-4, p-8, mt-4, etc.)

### Component Standards
1. **Every Livewire component must have:**
   - Loading states (wire:loading)
   - Error handling
   - Success notifications
   - Proper validation
   - Mobile responsive design

2. **Interactive elements must have:**
   - Hover states
   - Focus states for accessibility
   - Transition animations
   - Loading indicators

3. **Forms must include:**
   - Real-time validation
   - Clear error messages
   - Success feedback
   - Proper labels and placeholders

## 🧪 Testing Strategy

### For each feature, create:
1. **Feature test** - Test the complete user flow
2. **Unit test** - Test service classes and helpers
3. **Livewire test** - Test component behavior

Example test creation:
```php
php artisan make:test InvoiceCreationTest
php artisan make:test InvoiceServiceTest --unit
```

## 📝 Code Quality Standards

1. **Follow Laravel conventions:**
   - Use resource controllers
   - Implement form requests for validation
   - Use policies for authorization
   - Keep controllers thin, services fat

2. **Livewire best practices:**
   - Use computed properties for derived state
   - Implement proper lifecycle hooks
   - Optimize queries with eager loading
   - Use wire:key for loops

3. **Database optimization:**
   - Add indexes on foreign keys
   - Use eager loading to prevent N+1
   - Implement soft deletes where appropriate

## 🚀 Deployment Checklist

Before marking project complete:
- [ ] All tests passing
- [ ] No console errors
- [ ] Mobile responsive verified
- [ ] Loading states implemented
- [ ] Error pages customized (404, 500)
- [ ] Meta tags and SEO basics
- [ ] README.md with setup instructions
- [ ] .env.example updated
- [ ] Database migrations are reversible

## 🌐 Deployment Strategy

### Option 1: Railway.app (Recommended - Easy & Free)
```bash
# MANUAL STEPS - CC will guide you through these:
# 1. Install Railway CLI (may need sudo)
npm install -g @railway/cli

# 2. Login (opens browser for auth)
railway login

# 3. Initialize project (interactive)
railway init

# 4. Add database (interactive selection)
railway add

# 5. Deploy (automatic after setup)
railway up

# 6. CC will prepare all config files and guide you through env variables
```

### Option 2: Render.com (Alternative)
1. Create `render.yaml` in project root:
```yaml
services:
  - type: web
    name: freelanceflow
    env: docker
    plan: free
    buildCommand: |
      composer install --optimize-autoloader --no-dev
      php artisan migrate --force
      npm ci && npm run build
    startCommand: php artisan serve --host 0.0.0.0 --port $PORT
```

### Option 3: Fly.io (More control)
```bash
# Install flyctl
curl -L https://fly.io/install.sh | sh

# Deploy
fly launch
fly deploy

# URL: https://freelanceflow.fly.dev
```

### Database Options (Free):
- **Supabase** - PostgreSQL (best free tier)
- **PlanetScale** - MySQL (generous free tier)
- **Neon** - PostgreSQL (good for development)

### Setup Deployment in Project:
1. Create deployment configuration files
2. Add GitHub Actions for auto-deploy:
```yaml
# .github/workflows/deploy.yml
name: Deploy
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Deploy to Railway
        uses: railwayapp/deploy-action@v1
        with:
          railway_token: ${{ secrets.RAILWAY_TOKEN }}
```

## 🤖 AI Assistant Behavior

1. **Be proactive:** Don't wait for instructions for obvious next steps
2. **Test everything:** Use MCP browser tool to verify UI works
3. **Commit often:** Don't let changes pile up
4. **Write clean code:** Follow PSR-12 and Laravel conventions
5. **Document complex logic:** Add comments where necessary
6. **Handle errors gracefully:** Never leave unhandled exceptions
7. **Think about UX:** Make the app delightful to use
8. **Stop for permissions:** When encountering sudo/password requirements, always stop and ask for manual intervention
9. **Clear communication:** When stuck or needing help, explain clearly what's needed

## 📊 Progress Tracking

**IMPORTANT: Update this file after completing each task!**
- Mark completed items with [x]
- Update the status below
- Commit changes to claude.md regularly

```markdown
Current Phase: Phase 6 - COMPLETED ✅ (15/15 COMPLETE)
Last Completed: Phase 6 COMPLETE ✅ - Czech Localization & ARES Integration with full language support, formatting, API integration, and comprehensive testing
Next Task: Phase 7 - Polish & Testing (comprehensive feature tests, animations, mobile optimization, keyboard shortcuts)
Blockers: None
Last Updated: 2025-09-22 20:35 UTC
```

## 🔄 Auto-Update Instructions
After completing each PHASE (not just checklist items):
1. **Create phase documentation**: Create `phases/phase-X.md` with comprehensive details:
   - Overview and conversation summary
   - Technical achievements and architecture  
   - Files created/modified with descriptions
   - Challenges resolved and solutions
   - Quality assurance and testing results
   - Business value delivered
   - Next steps and technical status
2. **Update checklist**: Mark all phase items as complete: `- [x]`
3. **Update Progress Tracking**: Update current phase, last completed, next task, timestamp
4. **Commit with detailed message**: Include features, testing, UI/UX, business logic, and technical details
5. **Push to repository**: Ensure all changes are saved to GitHub

**Phase Documentation Template**: Follow `phases/phase-1.md`, `phases/phase-2.md`, `phases/phase-3.md` structure for consistency

## 🎯 Success Metrics

The project is complete when:
1. All checklist items are done
2. Test coverage > 70%
3. No critical bugs
4. Mobile responsive
5. Page load < 2 seconds
6. Clean Git history
7. Comprehensive documentation

---
*Remember: This is a portfolio project. Quality > Quantity. Make it impressive!*