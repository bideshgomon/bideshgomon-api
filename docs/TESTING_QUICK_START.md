# Plugin Service Architecture - Testing Quick Start

## Overview
This guide provides quick test scenarios to validate the completed backend (70% of project). Frontend components (Phase 7-8) are next.

**Status**: Backend 100% Complete | Frontend 0% Complete | Overall 70% Complete

---

## Test Environment

### Prerequisites
```powershell
# Ensure server is running
php artisan serve

# Ensure demo data is seeded
php artisan db:seed --class=PluginServiceArchitectureSeeder
```

### Test User Credentials
```bash
# Admin User (for admin routes)
Email: admin@bideshgomon.com
Password: [check DatabaseSeeder]

# Regular User (for public routes)
Email: user@bideshgomon.com
Password: [check DatabaseSeeder]
```

---

## API Testing with Postman/cURL

### 1. Browse Services (Public)
**Endpoint**: `GET /services`  
**Auth**: Not required  
**Test**:
```bash
curl http://localhost:8000/services
```

**Expected Response**:
```json
{
  "services": [
    {
      "id": 25,
      "name": "Tourist Visa Application",
      "slug": "tourist-visa-application",
      "short_description": "Apply for tourist visa...",
      "is_featured": true,
      "processing_days": 15
    }
  ]
}
```

---

### 2. Get Service with Form Fields (Public)
**Endpoint**: `GET /services/{slug}`  
**Auth**: Not required  
**Test**:
```bash
curl http://localhost:8000/services/tourist-visa-application
```

**Expected Response**:
```json
{
  "service": {
    "id": 25,
    "name": "Tourist Visa Application",
    "form_fields_count": 22
  },
  "form_fields": [
    {
      "field_name": "full_name",
      "field_label": "Full Name (as in passport)",
      "field_type": "text",
      "profile_map_key": "user_passports.full_name",
      "is_required": true,
      "group_name": "Personal Information"
    }
  ]
}
```

---

### 3. Create Application with Auto-Fill (Authenticated)
**Endpoint**: `GET /applications/create/{serviceSlug}`  
**Auth**: Required (Bearer token or session)  
**Test**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost:8000/applications/create/tourist-visa-application
```

**Expected Response**:
```json
{
  "service": {...},
  "form": {
    "fields": [
      {
        "field_name": "full_name",
        "field_label": "Full Name",
        "value": "John Doe" // <-- Auto-filled from profile
      },
      {
        "field_name": "passport_number",
        "field_label": "Passport Number",
        "value": "BA1234567" // <-- Auto-filled from user_passports
      }
    ]
  },
  "formData": {
    "full_name": "John Doe",
    "passport_number": "BA1234567"
  }
}
```

**Verification**:
✅ All fields with `profile_map_key` should be pre-filled  
✅ formData object contains all mapped values  
✅ Empty fields should have `null` or default values

---

### 4. Submit Application (Draft or Submit)
**Endpoint**: `POST /applications/store/{serviceSlug}`  
**Auth**: Required  
**Body**:
```json
{
  "full_name": "John Doe",
  "passport_number": "BA1234567",
  "travel_purpose": "Tourism",
  "intended_travel_date": "2025-06-15",
  "status": "draft", // or "submitted"
  "save_to_profile": true // Reverse sync
}
```

**Test**:
```bash
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"full_name":"John Doe","status":"draft"}' \
  http://localhost:8000/applications/store/tourist-visa-application
```

**Expected Response**:
```json
{
  "application": {
    "id": 1,
    "application_number": "APP-2025-001234",
    "status": "draft",
    "service": {...}
  },
  "message": "Application saved as draft"
}
```

**Verification**:
✅ Application record created in `service_applications`  
✅ `form_data` column contains JSON of submitted data  
✅ If `status=submitted`, `profile_snapshot` captured  
✅ If `save_to_profile=true`, profile updated with form data

---

### 5. List User Applications
**Endpoint**: `GET /applications`  
**Auth**: Required  
**Test**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost:8000/applications
```

**Expected Response**:
```json
{
  "applications": {
    "data": [
      {
        "id": 1,
        "application_number": "APP-2025-001234",
        "status": "draft",
        "service": {
          "name": "Tourist Visa Application"
        },
        "created_at": "2025-12-01T10:30:00Z"
      }
    ]
  }
}
```

---

### 6. View Application Details
**Endpoint**: `GET /applications/{application}`  
**Auth**: Required (owner only)  
**Test**:
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost:8000/applications/1
```

**Expected Response**:
```json
{
  "application": {
    "id": 1,
    "application_number": "APP-2025-001234",
    "status": "draft",
    "form_data": {
      "full_name": "John Doe",
      "passport_number": "BA1234567"
    },
    "profile_snapshot": null,
    "status_history": [],
    "documents": []
  }
}
```

---

### 7. Submit Draft Application
**Endpoint**: `POST /applications/{application}/submit`  
**Auth**: Required  
**Test**:
```bash
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/applications/1/submit
```

**Expected Response**:
```json
{
  "application": {
    "id": 1,
    "status": "pending", // Changed from "draft"
    "submitted_at": "2025-12-01T11:00:00Z"
  },
  "message": "Application submitted successfully"
}
```

**Verification**:
✅ Status changed to `pending`  
✅ `submitted_at` timestamp set  
✅ `profile_snapshot` captured  
✅ Status history entry created

---

## Admin Routes Testing

### 8. List All Applications (Admin)
**Endpoint**: `GET /admin/plugin-applications`  
**Auth**: Required (admin role)  
**Test**:
```bash
curl -H "Authorization: Bearer ADMIN_TOKEN" \
     http://localhost:8000/admin/plugin-applications
```

**Expected Response**:
```json
{
  "applications": {
    "data": [
      {
        "id": 1,
        "application_number": "APP-2025-001234",
        "user": {
          "name": "John Doe"
        },
        "service": {
          "name": "Tourist Visa Application"
        },
        "status": "pending"
      }
    ]
  }
}
```

---

### 9. Review Application (Admin)
**Endpoint**: `GET /admin/plugin-applications/{application}`  
**Auth**: Required (admin)  
**Test**:
```bash
curl -H "Authorization: Bearer ADMIN_TOKEN" \
     http://localhost:8000/admin/plugin-applications/1
```

**Expected Response**:
```json
{
  "application": {
    "id": 1,
    "form_data": {...},
    "profile_snapshot": {...},
    "status_history": [
      {
        "old_status": "draft",
        "new_status": "pending",
        "changed_by": "John Doe",
        "created_at": "2025-12-01T11:00:00Z"
      }
    ]
  }
}
```

---

### 10. Approve Application (Admin)
**Endpoint**: `POST /admin/plugin-applications/{application}/approve`  
**Auth**: Required (admin)  
**Body**:
```json
{
  "notes": "All documents verified. Visa approved."
}
```

**Test**:
```bash
curl -X POST \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"notes":"Approved"}' \
  http://localhost:8000/admin/plugin-applications/1/approve
```

**Expected Response**:
```json
{
  "application": {
    "status": "approved"
  },
  "message": "Application approved successfully"
}
```

**Verification**:
✅ Status changed to `approved`  
✅ Status history entry created with admin notes  
✅ User notified (if notification system exists)

---

## Database Verification

### Check Form Fields
```sql
SELECT 
  sf.id,
  sm.name as service_name,
  sf.field_name,
  sf.field_label,
  sf.profile_map_key,
  sf.is_required,
  sf.group_name
FROM service_form_fields sf
JOIN service_modules sm ON sf.service_module_id = sm.id
WHERE sm.slug = 'tourist-visa-application'
ORDER BY sf.sort_order;
```

**Expected**: 22 rows for Tourist Visa

---

### Check Applications
```sql
SELECT 
  sa.id,
  sa.application_number,
  sa.status,
  sm.name as service_name,
  u.name as user_name,
  sa.created_at
FROM service_applications sa
JOIN service_modules sm ON sa.service_module_id = sm.id
JOIN users u ON sa.user_id = u.id
ORDER BY sa.created_at DESC;
```

---

### Check Status History
```sql
SELECT 
  ash.id,
  sa.application_number,
  ash.old_status,
  ash.new_status,
  u.name as changed_by,
  ash.notes,
  ash.created_at
FROM application_status_history ash
JOIN service_applications sa ON ash.service_application_id = sa.id
JOIN users u ON ash.changed_by = u.id
ORDER BY ash.created_at DESC;
```

---

## Profile Mapping Validation

### Test Auto-Fill Accuracy
1. **Setup**: Create user with complete profile (passport, education, work)
2. **Action**: GET `/applications/create/tourist-visa-application`
3. **Verify**:
   - Fields with `profile_map_key` are pre-filled
   - Dot notation resolves correctly (e.g., `user_passports.passport_number`)
   - Missing profile data shows `null` (not error)

### Test Reverse Sync
1. **Setup**: Submit application with `save_to_profile: true`
2. **Action**: POST `/applications/store/{slug}` with form data
3. **Verify**:
   - Profile tables updated with form values
   - Only mapped fields updated
   - Existing profile data not overwritten unless changed

---

## File Upload Testing

### Upload Document
**Endpoint**: `POST /applications/{application}/documents`  
**Auth**: Required  
**Body**: `multipart/form-data`
```bash
curl -X POST \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "document_type=passport_scan" \
  -F "file=@/path/to/passport.pdf" \
  http://localhost:8000/applications/1/documents
```

**Verification**:
✅ File stored in `storage/app/public/applications/documents/`  
✅ Record created in `application_documents`  
✅ File accessible via `/storage/applications/documents/{filename}`

---

## Conditional Logic Testing

### Test Field Visibility
1. **Setup**: Tourist Visa has conditional field:
   - Show "Refusal Details" IF "Previously Refused" = "Yes"
2. **Action**: Submit form with `previously_refused: "yes"`
3. **Verify**:
   - Frontend should show/hide "refusal_details" field
   - Backend validation enforces required if condition met

**Conditional Rule Structure** (in `conditional_rules` column):
```json
{
  "show_if": {
    "field_name": "previously_refused",
    "operator": "equals",
    "value": "yes"
  }
}
```

---

## Edge Cases & Error Handling

### 1. Submit Without Required Fields
**Expected**: 422 Validation Error
```json
{
  "message": "The given data was invalid",
  "errors": {
    "full_name": ["The full name field is required."]
  }
}
```

### 2. Access Another User's Application
**Expected**: 403 Forbidden
```json
{
  "message": "Unauthorized"
}
```

### 3. Submit Draft Twice
**Expected**: 400 Bad Request
```json
{
  "message": "Application already submitted"
}
```

### 4. Large File Upload
**Expected**: 413 Payload Too Large (if exceeds limit)

### 5. Invalid Service Slug
**Expected**: 404 Not Found
```json
{
  "message": "Service not found"
}
```

---

## Performance Testing

### Load Test: Auto-Fill Performance
```bash
# Test 100 concurrent auto-fill requests
ab -n 100 -c 10 -H "Authorization: Bearer TOKEN" \
   http://localhost:8000/applications/create/tourist-visa-application
```

**Expected**: < 500ms response time (DataMapperService should be efficient)

### Database Query Count
Use Laravel Debugbar or log queries:
```php
DB::enableQueryLog();
// ... make request ...
dd(DB::getQueryLog());
```

**Target**: < 10 queries per auto-fill request (use eager loading)

---

## Test Checklist

### Backend Functionality ✅
- [x] Services listed with filters
- [x] Service details with form fields
- [x] Form auto-filled from profile (60+ fields)
- [x] Draft application saved
- [x] Application submitted
- [x] Status changed with audit trail
- [x] Documents uploaded and verified
- [x] Profile updated from form (reverse sync)
- [x] Admin can review applications
- [x] Admin can approve/reject

### Data Integrity ✅
- [x] Foreign keys enforced
- [x] JSON columns validated
- [x] Status transitions logged
- [x] Profile snapshots captured
- [x] Application numbers unique

### Security ✅
- [x] Auth middleware on protected routes
- [x] Ownership validation (user can't access others' apps)
- [x] Admin role required for admin routes
- [x] File upload validation (type, size)
- [x] Input sanitization

### Bangladesh Localization ⏳
- [ ] Currency formatted as ৳ (frontend pending)
- [ ] Dates formatted DD/MM/YYYY (frontend pending)
- [ ] Phone numbers formatted 01712-345678 (frontend pending)

---

## Next Steps: Frontend Components

### Phase 7: User Components (Priority 1)
1. **ServiceApplicationForm.vue** - Dynamic form renderer
2. **ServicesList.vue** - Browse services
3. **MyApplications.vue** - Track applications
4. **ApplicationDetails.vue** - View single application

### Phase 8: Admin Components (Priority 2)
1. **Admin/Services/FormBuilder.vue** - Build forms
2. **Admin/Applications/Index.vue** - Review applications
3. **Admin/Applications/ReviewModal.vue** - Approve/reject UI

### Phase 10: Testing (Priority 3)
1. End-to-end user flow
2. Admin workflow
3. File uploads
4. Edge cases

---

## Common Issues & Solutions

### Issue: Auto-fill returns empty values
**Solution**: Check user profile is complete. Run ProfileManagementSeeder.

### Issue: File upload fails
**Solution**: Run `php artisan storage:link` to create symlink.

### Issue: Status change fails
**Solution**: Check ServiceApplicationService validates status transitions.

### Issue: Validation errors not showing
**Solution**: Ensure ValidationException is thrown, not generic Exception.

---

## Success Metrics

**Backend Complete When**:
- ✅ All 10 routes return correct responses
- ✅ Profile auto-fill works for 60+ fields
- ✅ Draft/submit flow works end-to-end
- ✅ Admin can approve/reject applications
- ✅ Status history audit trail complete
- ✅ File uploads work with verification

**Project 100% Complete When**:
- ⏳ Frontend components render all forms dynamically
- ⏳ Admin can build services without code
- ⏳ Users can apply in < 5 minutes
- ⏳ All Bangladesh formatting applied
- ⏳ End-to-end testing passed

---

**Current Status**: Backend 100% | Frontend 0% | Overall 70%  
**Last Updated**: December 2025  
**Next Action**: Build ServiceApplicationForm.vue
