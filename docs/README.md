# Documentation Structure

## 📁 Organization

All documentation consolidated in `/docs` folder with clear categorization.

```
docs/
├── README.md (this file - navigation guide)
├── architecture/
│   ├── MODERN_DATABASE_ARCHITECTURE.md
│   ├── SYSTEM_ARCHITECTURE.md (from root)
│   └── MOBILE_FIRST_DESIGN_SYSTEM.md
├── guides/
│   ├── SERVICE_TEMPLATE_WITH_DEMO_DATA.md
│   ├── QUICK_REFERENCE_SUMMARY.md (from root)
│   └── DAILY_DEPLOYMENT_CICD.md
├── deployment/
│   ├── ZERO_TO_DEPLOYMENT_MASTER_PLAN.md (from root)
│   └── PHASE_GUIDES/ (implementation phases)
└── reference/
    ├── API.md (endpoints documentation)
    ├── DATABASE_SCHEMA.md
    └── TESTING.md
```

---

## 📚 Quick Navigation

### For Developers Starting Fresh
1. **Read First**: [ZERO_TO_DEPLOYMENT_MASTER_PLAN.md](../ZERO_TO_DEPLOYMENT_MASTER_PLAN.md)
2. **Quick Reference**: [QUICK_REFERENCE_SUMMARY.md](../QUICK_REFERENCE_SUMMARY.md)
3. **Database Design**: [MODERN_DATABASE_ARCHITECTURE.md](./MODERN_DATABASE_ARCHITECTURE.md)
4. **System Overview**: [SYSTEM_ARCHITECTURE.md](../SYSTEM_ARCHITECTURE.md)

### For Building Features
1. **Service Template**: [SERVICE_TEMPLATE_WITH_DEMO_DATA.md](./SERVICE_TEMPLATE_WITH_DEMO_DATA.md)
2. **Mobile Design**: [MOBILE_FIRST_DESIGN_SYSTEM.md](./MOBILE_FIRST_DESIGN_SYSTEM.md)
3. **Database Patterns**: [MODERN_DATABASE_ARCHITECTURE.md](./MODERN_DATABASE_ARCHITECTURE.md)
4. **Wallet System**: [WALLET_SYSTEM_COMPLETE.md](./WALLET_SYSTEM_COMPLETE.md) ✅ Complete

### For Deployment
1. **Daily Deployment**: [DAILY_DEPLOYMENT_CICD.md](./DAILY_DEPLOYMENT_CICD.md)
2. **Master Plan**: [ZERO_TO_DEPLOYMENT_MASTER_PLAN.md](../ZERO_TO_DEPLOYMENT_MASTER_PLAN.md)

---

## 🎯 Documentation Principles

1. **No Duplicates** - One source of truth for each topic
2. **Always Updated** - Update docs when code changes
3. **Examples Required** - Every concept has code example
4. **Mobile First** - All UI documentation assumes mobile-first design
5. **Bangladesh Context** - All examples use Bangladesh data (৳, +880, etc.)

---

## 📝 Contributing to Docs

When adding new documentation:

1. **Choose the right category**:
   - Architecture docs → `architecture/`
   - How-to guides → `guides/`
   - Deployment → `deployment/`
   - API/Reference → `reference/`

2. **Follow naming convention**:
   - Use UPPERCASE_WITH_UNDERSCORES.md
   - Be specific: `USER_AUTHENTICATION_GUIDE.md` not `AUTH.md`

3. **Include these sections**:
   - 🎯 Goal/Purpose
   - Code examples with comments
   - ✅ DO's and ❌ DON'Ts
   - Checklist at the end

4. **Update this README** with link to your new doc

---

## 🔍 Search by Topic

### Architecture
- Database design: [MODERN_DATABASE_ARCHITECTURE.md](./MODERN_DATABASE_ARCHITECTURE.md)
- System overview: [SYSTEM_ARCHITECTURE.md](../SYSTEM_ARCHITECTURE.md)
- Mobile-first: [MOBILE_FIRST_DESIGN_SYSTEM.md](./MOBILE_FIRST_DESIGN_SYSTEM.md)

### Development
- Creating services: [SERVICE_TEMPLATE_WITH_DEMO_DATA.md](./SERVICE_TEMPLATE_WITH_DEMO_DATA.md)
- Quick reference: [QUICK_REFERENCE_SUMMARY.md](../QUICK_REFERENCE_SUMMARY.md)

### Deployment
- CI/CD pipeline: [DAILY_DEPLOYMENT_CICD.md](./DAILY_DEPLOYMENT_CICD.md)
- Full plan: [ZERO_TO_DEPLOYMENT_MASTER_PLAN.md](../ZERO_TO_DEPLOYMENT_MASTER_PLAN.md)

---

## 🗑️ Deprecated Docs

Old phase documents from bgproject folder are **reference only**. New project follows:
- Mobile-first design (not desktop-first)
- Centralized data (no string duplication)
- Daily deployments (not manual)
- 100% test coverage (zero errors)

---

**Last Updated**: November 19, 2025
