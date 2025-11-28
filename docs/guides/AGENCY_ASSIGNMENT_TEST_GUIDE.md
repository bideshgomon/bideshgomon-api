# Agency Assignment System - Complete Test Guide

## ✅ System Setup Complete

### 🏢 Created Agencies

1. **BideshGomon Travel Agency** (test@example.com)
   - Specialization: Tourist Visa Services
   - Commission: 15%
   - Countries: Malaysia, Thailand, Singapore
   
2. **Global Education Consultancy** (global.edu@example.com)
   - Specialization: Student Visa Services
   - Commission: 18%
   - Countries: UK, USA
   
3. **Work Permit Experts** (workpermit@example.com)
   - Specialization: Work Visa Services
   - Commission: 20%
   - Countries: Singapore, Malaysia
   
4. **Sky Travel Services** (skytravel@example.com)
   - Specialization: Flight & Hotel Booking
   - Commission: 10%
   - Scope: Global

---

## 🧪 Test Plan

### Test 1: Admin - Multiple Country Assignment
**URL:** `http://127.0.0.1:8000/admin/agency-assignments/create`

**Steps:**
1. Login as admin (admin@bideshgomon.com / password)
2. Navigate to Admin → Agency Assignments → Create
3. Select "BideshGomon Travel Agency" from agency dropdown
4. Select "Tourist Visa" from service module dropdown
5. ✅ **Enable "Assign multiple countries at once" checkbox**
6. Select multiple countries: Malaysia, Thailand, Indonesia, Vietnam
7. Select "Tourist" visa type
8. Set assignment scope: "Visa Specific"
9. Set commission rate: 15%
10. Enable all permissions
11. Click "Assign Agency"

**Expected Result:**
- Success message showing "Successfully assigned agency to 4 countries!"
- Redirect to assignments index page
- All 4 country assignments visible in the list

---

### Test 2: Agency Dashboard - View Assignments
**URL:** `http://127.0.0.1:8000/agency/country-assignments`

**For each agency:**

#### BideshGomon Travel (test@example.com / password)
**Expected to see:**
- 🌍 Countries assigned: MY, TH, SG (or more if Test 1 completed)
- Tourist Visa service
- 15% commission rate
- All permissions enabled
- Applications count: 0 (no apps yet)

#### Global Education (global.edu@example.com / password)
**Expected to see:**
- 🌍 Countries assigned: UK, USA
- Student Visa service
- 18-20% commission rates
- All permissions enabled
- Country-specific assignments

#### Work Permit Experts (workpermit@example.com / password)
**Expected to see:**
- 🌍 Countries assigned: SG, MY
- Work Visa service
- 18-20% commission rates
- All permissions enabled

#### Sky Travel (skytravel@example.com / password)
**Expected to see:**
- 🌐 Global assignments (2)
- Flight Booking: 10% commission
- Hotel Booking: 8% commission
- Limited permissions (no edit requirements)

---

### Test 3: Service Module Filtering (Admin)
**URL:** `http://127.0.0.1:8000/admin/agency-assignments/create`

**Steps:**
1. Login as admin
2. Go to Create Assignment page
3. Select service module: "Tourist Visa"
4. Check agency dropdown - should show agencies assigned to Tourist Visa
5. Change service module to "Student Visa"
6. Agency dropdown should update to show only Global Education
7. Change to "Flight Booking"
8. Should show Sky Travel Services

**Expected Result:**
- Agency dropdown filters dynamically based on selected service module
- Shows agencies already assigned to that service

---

### Test 4: Agency Country Display
**Each agency should see only their assigned countries**

**Steps:**
1. Login as BideshGomon (test@example.com)
2. Go to Dashboard → My Countries
3. Should see only Tourist Visa countries
4. **Should NOT see** Student Visa or Work Visa assignments

**Expected Result:**
- Clean separation between agency services
- No cross-contamination of assignments

---

### Test 5: Permissions Verification

**Test Edit Requirements Permission:**
1. Login as BideshGomon (test@example.com)
2. View country assignment for Malaysia
3. Should show "Edit Requirements: ✅" enabled
4. Login as Sky Travel (skytravel@example.com)
5. View global flight assignment
6. Should show "Edit Requirements: ❌" disabled

**Expected Result:**
- Permissions correctly displayed
- Green checkmark for enabled, gray X for disabled

---

### Test 6: Commission Rates

**Verify different commission structures:**

| Agency | Service | Commission Type | Rate |
|--------|---------|-----------------|------|
| BideshGomon | Tourist Visa | Percentage | 15% |
| Global Education | Student Visa (UK) | Percentage | 18% |
| Global Education | Student Visa (USA) | Percentage | 20% |
| Work Permit | Work Visa (SG) | Percentage | 20% |
| Work Permit | Work Visa (MY) | Percentage | 18% |
| Sky Travel | Flight Booking | Percentage | 10% |
| Sky Travel | Hotel Booking | Percentage | 8% |

**Expected Result:**
- All commission rates display correctly
- Average commission ~15-16%

---

## 📊 Test Verification Script

Run the automated test script:
```bash
php test-agency-assignments.php
```

**Expected Output:**
- ✅ 4 agencies created
- ✅ 6+ assignments created (more if manual tests done)
- ✅ All active assignments
- ✅ Multiple service modules covered
- ✅ 4+ countries with assignments

---

## 🔍 Database Verification Queries

```sql
-- Check all assignments
SELECT 
    a.name as agency,
    sm.name as service,
    ac.country,
    ac.visa_type,
    ac.assignment_scope,
    ac.platform_commission_rate
FROM agency_country_assignments ac
JOIN agencies a ON ac.agency_id = a.id
JOIN service_modules sm ON ac.service_module_id = sm.id
ORDER BY a.name, sm.name;

-- Check agencies without assignments
SELECT u.name, u.email
FROM users u
JOIN agencies a ON u.id = a.user_id
LEFT JOIN agency_country_assignments ac ON a.id = ac.agency_id
WHERE ac.id IS NULL;

-- Count assignments per service
SELECT 
    sm.name as service,
    COUNT(ac.id) as assignment_count,
    COUNT(DISTINCT ac.agency_id) as agency_count
FROM service_modules sm
LEFT JOIN agency_country_assignments ac ON sm.id = ac.service_module_id
GROUP BY sm.id
ORDER BY assignment_count DESC;
```

---

## 🐛 Known Issues / Limitations

1. **Tourist Visa Assignments**: If not visible, run seeder again:
   ```bash
   php artisan db:seed --class=AgencyTestSeeder
   ```

2. **Service Module Filtering**: Requires page reload when changing service module

3. **Global Assignments**: Must use unique country names (e.g., "Global - Flight", "Global - Hotel")

---

## 📝 Test Checklist

### Admin Features
- [ ] Can create single country assignment
- [ ] Can create multiple country assignments at once
- [ ] Can filter agencies by service module
- [ ] Can view all assignments in list
- [ ] Can edit existing assignments
- [ ] Can toggle assignment active status
- [ ] Can delete assignments

### Agency Features
- [ ] Can view assigned countries
- [ ] Can see commission rates
- [ ] Can view permissions
- [ ] Can see assignment notes
- [ ] Dashboard shows correct stats
- [ ] Navigation links work correctly

### System Integrity
- [ ] No duplicate assignments
- [ ] Correct permission enforcement
- [ ] Commission calculations accurate
- [ ] Country filtering works
- [ ] Service module filtering works
- [ ] Database relationships intact

---

## 🚀 Next Steps After Testing

1. **Create real applications**: Test full workflow from user → agency → quote → payment
2. **Test quote submission**: Agency submits quotes for applications
3. **Test filtering**: Agency applications page filters by assigned countries
4. **Test earnings**: Verify commission calculations
5. **Test multiple agencies**: Assign same country to multiple agencies
6. **Test deactivation**: Disable assignment and verify agency can't access

---

## 📧 Test User Credentials

All passwords are: **password**

| Role | Email | Agency | Specialization |
|------|-------|--------|----------------|
| Agency | test@example.com | BideshGomon Travel | Tourist Visa |
| Agency | global.edu@example.com | Global Education | Student Visa |
| Agency | workpermit@example.com | Work Permit Experts | Work Visa |
| Agency | skytravel@example.com | Sky Travel Services | Flight & Hotel |
| Admin | admin@bideshgomon.com | N/A | System Admin |

---

## ✅ Success Criteria

The system is working correctly if:
1. Admin can assign agencies to multiple countries in one action
2. Agencies only see their assigned countries
3. Service module filtering works for admin
4. Commission rates are correctly configured
5. Permissions are properly enforced
6. Navigation works smoothly
7. No errors in browser console
8. Database queries return expected results

---

## 🎉 Test Complete!

Once all tests pass, the agency assignment system is **production-ready** for:
- Tourist visa agencies
- Student visa consultants
- Work permit processors
- Travel booking services
- Any service-based agency model
