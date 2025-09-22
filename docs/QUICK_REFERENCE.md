# FreelanceFlow - Quick Reference

## 🚀 Current Status
- **Phase**: 6 - Czech Localization & ARES Integration
- **Next Task**: Read and analyze czech.md file
- **Database**: SQLite (development)

## ⚡ Common Commands
```bash
# Development
php artisan serve
npm run dev
php artisan test

# Code Quality
./vendor/bin/pint
php artisan optimize:clear

# Git (auto-commit after features)
git add .
git commit -m "feat: [description]"
git push origin main
```

## 🎯 Current Phase Tasks
- [ ] Read and analyze czech.md file
- [ ] Setup Laravel localization
- [ ] Create SetLocale middleware
- [ ] Implement ARES API service
- [ ] Add Czech translations
- [ ] Build LocaleSelector component

## ⚠️ Manual Actions Required
When you see commands requiring sudo/password, output:
```
⚠️ MANUAL ACTION REQUIRED:
Please run: [command]
Type "done" when completed.
```

## 🏗️ Project Structure
```
app/Livewire/     # All components
app/Models/       # Eloquent models  
app/Services/     # Business logic
resources/views/  # Blade templates
```

## 🎨 UI Standards
- Primary: Blue (#3B82F6)
- Use rounded-lg, shadow-sm
- Loading states required
- Mobile responsive
