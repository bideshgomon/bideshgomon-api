# Phase 3: Profile UI - Test Guide

## Test Credentials
- **User**: john@test.com
- **Password**: password
- **Profile Completion**: 80% (8/10 fields filled)

## Test Steps

### 1. View Profile (Profile Show Page)
1. Login at http://localhost:8000/login
2. Click your name in the top right → Click "Profile" or go to http://localhost:8000/profile
3. **Verify:**
   - ✅ Profile completion percentage shows 80%
   - ✅ Progress bar matches percentage
   - ✅ Basic Information section shows name, email, role, phone (formatted with BD format)
   - ✅ Date of Birth shows in DD/MM/YYYY format (Bangladesh format)
   - ✅ Address Information shows divisions and districts
   - ✅ Documents section shows NID and passport details
   - ✅ "Edit Profile" button visible in header

### 2. Edit Profile (Profile Edit Page)
1. From profile page, click "Edit Profile" button or go to http://localhost:8000/profile/edit
2. **Verify:**
   - ✅ "Update Profile Information" section (Breeze default - name/email)
   - ✅ "Profile Details" section with ALL fields:
     - Bio (textarea)
     - Phone (text input with +880 placeholder)
     - Date of Birth & Gender (side by side)
     - Nationality (defaults to "Bangladeshi")
     - Present Address (Division dropdown, District input, Full address textarea)
     - Permanent Address (Division dropdown, District input, Full address textarea)
     - Identity Documents (NID, Passport Number, Issue/Expiry dates)
   - ✅ Division dropdowns show 8 BD divisions (Dhaka, Chittagong, Rajshahi, etc.)
   - ✅ Update Password section (Breeze default)
   - ✅ Delete Account section (Breeze default)

### 3. Update Profile Details
1. On edit page, scroll to "Profile Details" section
2. Update these fields:
   - Bio: "Full-stack developer from Bangladesh"
   - Phone: "01712345678"
   - Gender: Select "Male"
   - Present Division: Select "Dhaka"
   - Present District: "Dhaka"
   - Present Address: "House 12, Road 5, Dhanmondi"
   - NID: "1234567890"
   - Passport Number: "BN1234567"
3. Click "Save" button
4. **Verify:**
   - ✅ "Saved." message appears briefly
   - ✅ Form stays on edit page (preserveScroll works)
   - ✅ Values are retained in form

### 4. Verify Updated Profile
1. Navigate back to profile view: http://localhost:8000/profile
2. **Verify:**
   - ✅ Bio shows: "Full-stack developer from Bangladesh"
   - ✅ Phone shows formatted: "01712-345678" (Bangladesh format)
   - ✅ Gender shows: "Male"
   - ✅ Present Address shows all fields (Division, District, Full Address)
   - ✅ NID shows: "1234567890"
   - ✅ Passport Number shows: "BN1234567"
   - ✅ Profile completion may have increased (more fields filled)

### 5. Test Bangladesh Formatting
Test the composable functions work:
- **Phone**: Should show as "01712-345678" (not raw "01712345678")
- **Date**: Should show as "07/11/2025" (DD/MM/YYYY, not US format)
- **Currency** (if you add wallet later): Should show as "৳5,000.00"

### 6. Test Navigation
1. From dashboard, can you access profile?
2. From profile, can you navigate to edit?
3. From edit, can you navigate back to profile?

## Expected Results Summary

✅ **Profile Show Page**:
- Clean, organized display of all profile fields
- Bangladesh formatting applied (phone, dates)
- Profile completion indicator with color coding
- Easy navigation to edit page

✅ **Profile Edit Page**:
- Comprehensive form with all Bangladesh-specific fields
- Division dropdowns with 8 divisions
- Proper validation (dates, phone format guidance)
- Separate sections for basic info, addresses, documents
- Maintains existing Breeze features (password update, account deletion)

✅ **Profile Update**:
- Form submission works without page reload (Inertia)
- Success message appears
- Data persists correctly
- Profile completion recalculates

✅ **Bangladesh Localization**:
- Phone numbers formatted with BD pattern
- Division/District fields for addresses
- NID field (10 or 17 digits)
- Passport fields follow BD conventions

## Route Testing

Test all profile routes work:

```powershell
# List profile routes
php artisan route:list --path=profile
```

Expected routes:
- GET /profile → profile.show (View profile)
- GET /profile/edit → profile.edit (Edit form)
- PATCH /profile → profile.update (Update name/email - Breeze)
- POST /profile/details → profile.update.details (Update profile details)
- DELETE /profile → profile.destroy (Delete account - Breeze)

## Common Issues to Check

1. **Ziggy Routes Not Found**: Run `php artisan ziggy:generate`
2. **Profile Null**: Controller creates profile automatically if doesn't exist
3. **Formatting Not Working**: Check useBangladeshFormat composable is imported
4. **Divisions Not Showing**: Verify get_bd_divisions() helper is loaded
5. **Form Not Submitting**: Check route name in form.post() matches routes/web.php

## Files Modified in Phase 3

```
Modified:
- routes/web.php (added profile.show, profile.update.details)
- app/Http/Controllers/ProfileController.php (added show, updateDetails, enhanced edit)

Created:
- resources/js/Pages/Profile/Show.vue (profile view page)
- resources/js/Pages/Profile/Partials/UpdateProfileDetailsForm.vue (profile details form)
- resources/js/Pages/Profile/Edit.vue (updated to include profile details)
```

## Next Steps After Testing

Once profile CRUD is fully working:
1. Commit Phase 3 to git
2. Merge to main branch
3. Update FRESH_BUILD_PROGRESS.md
4. Ready to start Phase 4: Wallet System
