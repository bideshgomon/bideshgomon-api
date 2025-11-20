# Bideshgomon - 100% Error-Free SaaS Platform

## 🎯 Project Vision

**What**: Multi-agency SaaS platform for international travel, visa, education, and employment services  
**Market**: Bangladesh (85%+ mobile users, slow networks, budget Android devices)  
**Approach**: Mobile-first design, daily deployments, centralized data, zero errors  
**Timeline**: 12-16 weeks from zero to production launch

---

## 🏗️ Core Architecture Decisions

### 1. **Mobile-First Design (PRIMARY)**
- Design for 375px (iPhone SE) first, scale up to desktop
- Minimum 48x48px touch targets (Apple/Google guidelines)
- Bottom navigation (not sidebar)
- Progressive Web App (PWA) with offline support
- Optimized for slow 2G/3G networks

**Read**: [docs/MOBILE_FIRST_DESIGN_SYSTEM.md](docs/MOBILE_FIRST_DESIGN_SYSTEM.md)

### 2. **Centralized Data (Single Source of Truth)**
- All reference data in central tables (countries, cities, currencies)
- Foreign keys everywhere - NEVER store names as strings
- 195 countries, 64 BD districts, 150+ currencies pre-seeded

**Read**: [docs/MODERN_DATABASE_ARCHITECTURE.md](docs/MODERN_DATABASE_ARCHITECTURE.md)

### 3. **Daily Deployments (CI/CD)**
- Every commit to `main` is production-ready
- Automated tests → Staging → Production
- Zero-downtime deployments

**Read**: [docs/DAILY_DEPLOYMENT_CICD.md](docs/DAILY_DEPLOYMENT_CICD.md)

---

## 📚 Documentation

- **Start Here**: [ZERO_TO_DEPLOYMENT_MASTER_PLAN.md](ZERO_TO_DEPLOYMENT_MASTER_PLAN.md)
- **Quick Reference**: [QUICK_REFERENCE_SUMMARY.md](QUICK_REFERENCE_SUMMARY.md)
- **Architecture**: [SYSTEM_ARCHITECTURE.md](SYSTEM_ARCHITECTURE.md)
- **All Docs**: [docs/README.md](docs/README.md)

---

## 🚀 Tech Stack

**Backend**: Laravel 12, Inertia.js 2.0, MySQL 8.0, Redis 7  
**Frontend**: Vue 3, TailwindCSS 3.4, Vite 7  
**DevOps**: GitHub Actions, Digital Ocean, Cloudflare

---

---

## 🚨 Critical Rules (NEVER Break These)

1. **Mobile First**: Design for 375px, scale up (not down)
2. **Central Data**: Never store country/city/currency names as strings
3. **Demo Data**: Every service needs 100+ demo records
4. **Testing**: Never push without tests passing
5. **Daily Deploy**: Push working code daily
6. **Zero Errors**: 100% test coverage goal

---

## ⚡ Quick Start

```bash
# Clone repository
git clone https://github.com/bideshgomon/bideshgomon-api.git
cd bideshgomon-api

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed --class=ReferenceDataSeeder
php artisan db:seed --class=DemoDataSeeder

# Run development servers
php artisan serve    # Backend: http://localhost:8000
npm run dev         # Frontend: hot reload enabled
```

---

## 🎯 Service Creation Checklist

When creating a new service, follow this checklist:

- [ ] Migration with foreign keys
- [ ] Model with relationships
- [ ] Service class (business logic)
- [ ] Controllers (User, Agency, Admin)
- [ ] Routes registered
- [ ] Vue pages (Index, Create, Show)
- [ ] Demo data seeder (100+ records)
- [ ] Feature tests (CRUD + workflows)
- [ ] Documentation in docs/services/

**Template**: [docs/SERVICE_TEMPLATE_WITH_DEMO_DATA.md](docs/SERVICE_TEMPLATE_WITH_DEMO_DATA.md)

---

## 📱 Mobile Testing

Test on these devices before deploying:

- [ ] iPhone SE (375px - smallest common size)
- [ ] Budget Android phone (slow network)
- [ ] All touch targets >= 48x48px
- [ ] No horizontal scrolling
- [ ] Font size >= 16px on inputs (prevents iOS zoom)
- [ ] Bottom navigation works
- [ ] PWA installable

---

## 🔄 Daily Workflow

```bash
# Morning
git pull origin main
composer install && npm install
php artisan migrate

# Development
# ...make changes...

# Before commit
php artisan test
./vendor/bin/phpstan analyse
./vendor/bin/pint --test

# Commit & auto-deploy
git add .
git commit -m "feat: add feature"
git push origin main
# → GitHub Actions → Staging → Production
```

---

## 📊 Project Status

**Phase**: Planning Complete ✅  
**Next**: Setup development environment (Week 1)  
**Goal**: Production launch in 12-16 weeks

---

## 🎉 What Makes This Project Different

✅ **Mobile-first** (85% Bangladesh users on mobile)  
✅ **Daily deployments** (not weekly/monthly)  
✅ **Centralized data** (no string duplication)  
✅ **Demo data everywhere** (100+ records per service)  
✅ **100% test coverage** goal  
✅ **Zero-downtime deployments**  
✅ **PWA with offline support**  
✅ **Bangladesh optimized** (slow networks, data saver mode)

---

## 📞 Support

- **Documentation**: [docs/README.md](docs/README.md)
- **Issues**: Create GitHub issue
- **Discussions**: GitHub Discussions

---

## 📄 License

Proprietary - All rights reserved by Bideshgomon

---

**Built with ❤️ for Bangladesh 🇧🇩**

