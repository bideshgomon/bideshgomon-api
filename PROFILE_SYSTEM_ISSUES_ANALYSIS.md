# Profile System - Critical Issues Analysis & Fix Plan
**Date:** November 30, 2025  
**Status:** URGENT - Multiple system failures

## Executive Summary
The profile system has **20+ critical failures** across database structure, controller logic, component state management, and data persistence. This requires systematic fixes in order of priority.

---

## 🔴 CRITICAL ISSUES (Blocks user data saving)

### 1. **Social Media & Contact** ❌
**Problem:** Data saves but doesn't display in Edit form  
**Root Cause:** Controller saves to `user_profiles.social_links` (JSON) BUT Edit.vue passes `userProfile?.social_links || {}` and the component initializes correctly. However, after page reload, the prop might not be parsing JSON correctly.

**Database:**
```sql
-- Columns exist:
facebook_url VARCHAR(255) NULL  
linkedin_url VARCHAR(255) NULL  
twitter_url VARCHAR(255) NULL  
instagram_url VARCHAR(255) NULL  
whatsapp_number VARCHAR(20) NULL  
social_links LONGTEXT NULL -- JSON field
```

**Fix Required:**
- Ensure `ProfileController::edit()` passes `social_links` as parsed object
- Verify `SocialLinksSection.vue` initializes form with `props.socialLinks`
- Check if JSON field is being decoded properly in the model

---

### 2. **Language Management** ❌  
**Problem:** "The selected language id is invalid" + "The selected language test id is invalid"  
**Root Cause:** **CRITICAL DATABASE MISMATCH**

**Current Structure:**
```sql
user_languages table:
- language VARCHAR(255) -- NOT language_id!
- test_taken ENUM('none','ielts','toefl','pte','duolingo','cambridge','other')
```

**Controller Trying:**
```php
// ProfileController line 112:
'userLanguages' => $user->languages()->with(['language', 'languageTest'])->get()
```

**Problem:** Table uses string columns, but code tries to eager load relationships that don't exist!

**Fix Required:**
1. **Update `user_languages` table:** Add `language_id` and `language_test_id` foreign keys
2. **Migrate existing data:** Convert string values to IDs
3. **Update User model:** Fix `languages()` relationship  
4. **Update validation:** Use `language_id` and `language_test_id` instead of strings

---

### 3. **Education History** ❌  
**Problem:** Not loading into input fields  
**Analysis:** Controller passes `'educations' => $user->educations` but components might not be initializing forms correctly.

**Fix Required:**
- Check `EducationSection.vue` - verify it receives `educations` prop
- Ensure form initialization loops through existing educations
- Verify date formats are compatible (DD-MM-YYYY vs YYYY-MM-DD)

---

### 4. **Work Experience** ❌  
**Problem:** Not loading into input fields  
**Same as Education** - likely form initialization issue

---

### 5. **Certifications & Licenses** ❌  
**Problem:** 500 Server Error when saving  
**Root Cause:** Validation expects complex nested structure

**Controller Code (line 367):**
```php
public function updateCertifications(Request $request)
{
    $validated = $request->validate([
        'certifications' => ['required', 'array'],
        'certifications.*.id' => ['required'], // Expects array of objects
        'certifications.*.type' => ['required', 'string', ...],
        // ... many required fields
    ]);
    
    $profile->update($validated); // Tries to save array to single JSON field
}
```

**Problem:** 
- Validation requires ALL fields for EVERY certification
- Frontend might be sending empty objects or wrong structure
- No error handling for malformed data

**Fix Required:**
- Make certification fields nullable except essentials
- Add try-catch with proper error logging
- Return validation errors to frontend
- Check if `$profile->update()` can handle JSON field properly

---

### 6. **References** ❌  
**Problem:** 500 Server Error when saving  
**Same issue as Certifications** - overly strict validation + JSON storage problem

---

### 7. **Health Insurance Expiry Date** ❌  
**Problem:** Saves but doesn't display after reload  
**Root Cause:** Likely date format mismatch

**Database:** `health_insurance_expiry_date DATE NULL`  
**Possible Issues:**
- Frontend sends DD-MM-YYYY
- Database expects YYYY-MM-DD
- Model doesn't cast to date
- Component doesn't format on display

**Fix Required:**
- Add date accessor/mutator in UserProfile model
- Ensure consistent format (convert to YYYY-MM-DD for storage)
- Use Bangladesh format helper for display

---

### 8. **Passport Management** ❌  
**Problem:** Not storing passport data  
**Analysis Needed:** Check PassportController methods

**Database Schema:**
```sql
user_passports table has:
- passport_number (UNIQUE)
- passport_type ENUM
- issuing_country VARCHAR(2) -- ISO code
- issue_date, expiry_date
- Many other fields
```

**Potential Issues:**
- Validation might be too strict (all fields required?)
- File upload paths not saving
- Foreign key constraints failing
- Unique constraint on passport_number failing for duplicates

**Fix Required:**
- Check PassportController validation rules
- Make optional fields nullable
- Add proper error handling
- Log validation failures

---

### 9. **Skills & Expertise** ❌  
**Problem:** Saves but doesn't render on Profile page  
**Root Cause:** Profile/Show.vue not displaying skills properly

**Fix Required:**
- Check Profile/Show.vue skills section
- Ensure skills relationship is eager loaded
- Verify skills are being displayed (not just hidden CSS)

---

### 10. **Visa History** ❌  
**Problem:** Not loading into input fields  
**Same as Education/Work Experience**

---

### 11. **Documents Management** ❌  
**Problem:** Keeps loading, no success message, data not stored  
**Root Cause:** Likely file upload + async issue

**Potential Issues:**
- File size too large (server upload_max_filesize)
- Missing CSRF token
- Route not returning proper success response
- Frontend not handling response correctly
- Storage disk not configured properly

**Fix Required:**
- Check server PHP upload limits
- Verify route returns Inertia response
- Add timeout handling
- Check storage permissions
- Log file upload errors

---

### 12. **Family Information** ❌  
**Problem:** Validation warnings - "gender required" + "nationality required"  
**Analysis:**

**Controller Validation:** Likely requires these fields but form doesn't send them

**Fix Required:**
- Update FamilySection component - add gender & nationality fields
- OR make validation optional if fields don't exist
- Check if family_members table has these columns

---

### 13. **Financial Information** ❌  
**Problem:** Save button not responding  
**Root Cause:** Form submission issue

**Potential Issues:**
- Button disabled state stuck
- Form validation failing silently
- Route not defined
- JavaScript error blocking submission

**Fix Required:**
- Check browser console for errors
- Verify route exists: `profile.financial.update`
- Check form validation state
- Add loading state handling

---

### 14. **Security & Background** ❌  
**Problem:** Not storing data  
**Analysis:** Check SecuritySection component and controller

---

### 15. **Privacy & Data Control** ⚠️  
**Problem:** Should default to "security high"  
**Fix:** Update UserProfile model factory or migration default

---

### 16. **Preferences & Settings** ⚠️  
**Problem:** Must load all countries from server + redesign  
**Status:** Countries ARE being passed from controller, check component

---

## 🟡 GLOBAL REQUIREMENTS

### Date Format (DD-MM-YYYY)
**Current:** Database uses YYYY-MM-DD (SQL standard)  
**Required:** All inputs/outputs must show DD-MM-YYYY for Bangladeshi users

**Fix Plan:**
1. Create global date formatter composable (DONE: `useBangladeshFormat.js`)
2. Apply to ALL date inputs (use DateInput component)
3. Convert formats in controller before saving
4. Convert formats in accessor when reading

### Mobile Responsiveness
**Required:** 100% mobile responsive with zero errors  
**Status:** Needs audit of all profile sections

### Edit ↔ Profile Page Sync
**Problem:** Data in Edit doesn't always show in Profile view  
**Required:** Everything in edit must display on profile page

---

## 📋 FIX PRIORITY ORDER

### Phase 1: Data Persistence (Hours 1-4)
1. Fix Languages table structure + migration
2. Fix References/Certifications validation
3. Fix Passport validation
4. Fix Family validation
5. Fix Financial button
6. Fix Security storage
7. Fix Documents upload timeout

### Phase 2: Data Display (Hours 5-6)
1. Fix Social Media not loading
2. Fix Education not loading
3. Fix Work Experience not loading
4. Fix Visa History not loading
5. Fix Skills not rendering on Profile page
6. Fix Health Insurance date display

### Phase 3: UX Improvements (Hours 7-8)
1. Implement global DD-MM-YYYY format
2. Set Privacy default to high
3. Update Preferences country loading
4. Mobile responsiveness audit
5. Sync Edit ↔ Profile page display
6. Update Profile page design

---

## 🔧 RECOMMENDED APPROACH

Given the scope, I recommend:

1. **Database Migration First** - Fix `user_languages` table structure
2. **Validation Relaxation** - Make most fields nullable, add error handling
3. **Component State Fixes** - Fix form initialization for all sections
4. **Systematic Testing** - Test each section after fix
5. **Documentation** - Update API docs for each fixed endpoint

**Estimated Time:** 8-10 hours for complete fix  
**Risk Level:** HIGH - touching core profile system  
**Testing Required:** EXTENSIVE - affects all users

---

## 🚨 IMMEDIATE ACTIONS NEEDED

1. **Backup database** before ANY changes
2. **Create feature branch** for fixes
3. **Fix critical blockers** (Languages, Certifications, References)
4. **Test thoroughly** on local before production
5. **Deploy incrementally** - not all at once

---

**Next Steps:** Choose which issue to fix first based on user impact priority.
