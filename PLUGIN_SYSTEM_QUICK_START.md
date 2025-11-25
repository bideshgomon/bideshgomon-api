# 🔌 Plugin System - Quick Start Guide

## 🚀 Access Points

### Admin
```
URL: /admin/service-applications
Features: Manage all applications, view quotes, export data
```

### Agency
```
URL: /agency/applications
Features: View available applications, submit quotes
```

### User
```
URL: /services (Browse & Apply)
URL: /my-applications (Track Applications)
URL: /my-applications/{id}/quotes (Compare Quotes)
```

---

## 📝 Quick Commands

### Verify System
```bash
php verify-plugin-system.php
```

### Check Routes
```bash
php artisan route:list --name=services
php artisan route:list --name=user.applications
```

### Rebuild Frontend
```bash
npm run build
```

---

## 🎯 Service Categories (38 Total)

| Category | Count | Examples |
|----------|-------|----------|
| 📝 Documentation | 8 | Passport, Translation, Apostille |
| 🎓 Education | 5 | Admission, Visa, Scholarships |
| 💼 Employment | 6 | Job Search, Work Permit, CV Writing |
| 🏠 Housing | 4 | Accommodation, Lease, Registration |
| 🏥 Healthcare | 3 | Insurance, Appointments, Vaccination |
| 💰 Financial | 4 | Bank Account, Tax, Planning |
| 🚗 Transportation | 5 | Airport, License, Vehicle |
| ⚖️ Legal | 3 | Consultation, Contracts, Appeals |

---

## 🔄 Application Workflow

```
1. USER browses services → /services
2. USER applies for service → Creates application
3. AGENCY views application → /agency/applications
4. AGENCY submits quote → Application gets "quoted" status
5. USER compares quotes → /my-applications/{id}/quotes
6. USER accepts quote → Application moves to "in_progress"
7. AGENCY completes work → Updates to "completed"
8. ADMIN monitors all → /admin/service-applications
```

---

## 📊 Database Tables

### service_modules
```sql
- id, name, slug, category
- description, icon, base_price
- is_active, created_at
```

### service_applications
```sql
- id, user_id, service_module_id
- status (pending/quoted/in_progress/completed/cancelled)
- form_data (JSON), created_at
```

### service_quotes
```sql
- id, service_application_id, agency_id
- quoted_price, notes, status
- created_at, updated_at
```

---

## 🎨 Status Colors

| Status | Color | Icon |
|--------|-------|------|
| Pending | Yellow | ⏳ |
| Quoted | Blue | 💬 |
| In Progress | Purple | 🔄 |
| Completed | Green | ✅ |
| Cancelled | Red | ❌ |

---

## 🧪 Test Data

### Users
- Admin: admin@example.com
- Test User: test@example.com

### Sample Applications: 6
- 2 Accepted
- 4 Pending

### Sample Quotes: 4
- 2 Accepted
- 2 Pending

---

## 📱 Mobile Navigation

### User Menu (Top Right)
- Dashboard
- 🔌 Services ← NEW
- 📋 My Applications ← NEW
- Documents
- Notifications
- Profile

---

## 🔗 Integration Points

### Frontend (Vue 3)
```javascript
// Navigate to services
<Link :href="route('services.index')">Browse Services</Link>

// Navigate to applications
<Link :href="route('user.applications.index')">My Applications</Link>

// Compare quotes
<Link :href="route('user.applications.quotes', application.id)">
  View Quotes
</Link>
```

### Backend (Laravel)
```php
// Controllers
ServiceController::class              // Public services
UserApplicationController::class      // User applications
ServiceApplicationController::class   // Admin management

// Models
ServiceModule::class
ServiceApplication::class
ServiceQuote::class
```

---

## 🎯 Key Features

### For Users
✅ Browse 38 services  
✅ Apply for services  
✅ Track applications  
✅ Compare quotes  
✅ Accept/reject quotes  
✅ View status updates  

### For Agencies
✅ View available applications  
✅ Submit competitive quotes  
✅ Track submissions  
✅ Manage accepted work  

### For Admins
✅ Monitor all applications  
✅ View statistics  
✅ Export reports  
✅ Manage status  
✅ Assign agencies  

---

## 💡 Tips

1. **Search** - Use the search bar to find specific services or applications
2. **Filters** - Filter by status, category, date range
3. **Dark Mode** - Toggle in top-right corner
4. **Export** - Use CSV export for reporting
5. **Mobile** - Fully responsive on all devices

---

## 🐛 Troubleshooting

### Routes not found?
```bash
php artisan route:clear
php artisan route:cache
```

### Frontend not updating?
```bash
npm run build
php artisan view:clear
```

### Database issues?
```bash
php artisan migrate:fresh --seed
```

---

## 📞 Quick Support

### File Locations
```
Admin Pages:    resources/js/Pages/Admin/ServiceApplications/
Agency Pages:   resources/js/Pages/Agency/Applications/
User Pages:     resources/js/Pages/Services/
                resources/js/Pages/User/Applications/
Controllers:    app/Http/Controllers/
Routes:         routes/web.php
Layout:         resources/js/Layouts/AuthenticatedLayout.vue
```

---

**✨ Everything is ready! Start browsing services at `/services`**
