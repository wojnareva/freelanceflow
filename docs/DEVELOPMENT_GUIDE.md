# Development Guide - FreelanceFlow

## 🔧 MCP Tools Usage
- **filesystem** - File operations
- **github** - Version control  
- **browser** - UI testing
- **fetch** - API testing

## 📋 Git Workflow
1. Commit after each feature
2. Use conventional commits: `feat:`, `fix:`, `refactor:`
3. Auto-push to main branch
4. Create feature branches for major features

## 🧪 Testing Strategy
For each feature create:
1. Feature test - Complete user flow
2. Unit test - Service classes
3. Livewire test - Component behavior

## 🎨 UI/UX Guidelines
### Design System
- Primary: Blue (#3B82F6)
- Success: Green (#10B981) 
- Warning: Yellow (#F59E0B)
- Danger: Red (#EF4444)
- Border: rounded-lg
- Shadows: shadow-sm default, shadow-md hover

### Component Requirements
Every Livewire component must have:
- Loading states (wire:loading)
- Error handling
- Success notifications
- Proper validation
- Mobile responsive design

## 🚀 Deployment Options
1. **Railway.app** (Recommended)
2. **Render.com** 
3. **Fly.io**

## 📊 Quality Standards
- Follow Laravel conventions
- Use resource controllers
- Implement form requests
- Keep controllers thin, services fat
- Add indexes on foreign keys
- Use eager loading to prevent N+1
