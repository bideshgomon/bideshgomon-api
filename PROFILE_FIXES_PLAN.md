# Profile Management Issues - Fix Plan

## Issues Identified (16 Total)

### 1. Social Media & Contact Data
- **Issue**: Data saves but not showing on Edit Profile page
- **Location**: ProfileController@edit, UserProfile model
- **Fix**: Check if social_links is loaded and returned to Inertia

### 2. Education History
- **Issue**: Not loading into input fields
- **Location**: UserEducationController@index, Edit page component
- **Fix**: Ensure API returns data properly, check Vue component mounting

### 3. Work Experience
- **Issue**: Not loading into input fields
- **Location**: UserWorkExperienceController@index
- **Fix**: Same as education - API + component issue

### 4. Add New Language
- **Issue**: "The selected language id is invalid."
- **Location**: UserLanguage validation
- **Fix**: Check language_id validation rules and Language model exists

### 5. Test Type
- **Issue**: "The selected language test id is invalid."
- **Location**: UserLanguage validation for test
- **Fix**: Check language_test_id validation and LanguageTest seeder

### 6. Certifications & Licenses
- **Issue**: 500 Server Error on save
- **Location**: Need to find controller
- **Fix**: Check error logs, likely validation or DB issue

### 7. References
- **Issue**: 500 Server Error on save
- **Location**: Need to find controller
- **Fix**: Check error logs and validation

### 8. Health Insurance Expiry Date
- **Issue**: Saves but doesn't show after reload
- **Location**: UserProfile or HealthInsurance model
- **Fix**: Check date format and retrieval

### 9. Passport Management
- **Issue**: Not storing passport
- **Location**: PassportController@store
- **Fix**: Check validation and file upload handling

### 10. Skills & Expertise
- **Issue**: Saves but not rendering on Profile page
- **Location**: Profile/Show component
- **Fix**: Check if skills relationship is loaded

### 11. Visa History
- **Issue**: Not loading into input fields
- **Location**: VisaHistoryController
- **Fix**: API response + component issue

### 12. Documents Management
- **Issue**: Keeps loading, no success, data not stored
- **Location**: DocumentController
- **Fix**: File upload issue, async handling

### 13. Family Information
- **Issue**: Validation errors for gender and nationality
- **Location**: FamilyMemberController validation
- **Fix**: Add gender and nationality to fillable, fix validation rules

### 14. Financial Information
- **Issue**: Save button not responding
- **Location**: FinancialInformationController
- **Fix**: Frontend event handling + backend validation

### 15. Security & Background
- **Issue**: Not storing data
- **Location**: SecurityInformationController
- **Fix**: Check validation and model fillable

### 16. Privacy & Data Control
- **Issue**: Should default to security high
- **Location**: User model or UserProfile
- **Fix**: Set default in migration/model

## Global Requirements

### Date Format (DD-MM-YYYY)
- **Actions**:
  1. Update all date inputs in Vue components
  2. Create useBangladeshFormat composable (already exists)
  3. Ensure all API responses format dates properly
  4. Add date accessor/mutator in models

### Mobile Responsiveness
- **Actions**:
  1. Audit all profile pages for responsive classes
  2. Test on mobile viewport
  3. Fix layout issues

## Priority Order

**Phase 1 - Critical Data Issues** (P0):
1. Family Information (validation)
2. Passport Management (not storing)
3. Documents Management (not storing)
4. Financial Information (button not working)
5. Security & Background (not storing)

**Phase 2 - Data Loading Issues** (P1):
6. Education History (not loading)
7. Work Experience (not loading)
8. Visa History (not loading)
9. Skills & Expertise (not rendering)

**Phase 3 - Validation Errors** (P2):
10. Add New Language (invalid ID)
11. Test Type (invalid ID)

**Phase 4 - 500 Errors** (P2):
12. Certifications & Licenses
13. References

**Phase 5 - Minor Issues** (P3):
14. Social Media display
15. Health Insurance expiry
16. Privacy defaults

**Phase 6 - Global Requirements** (P1):
17. Date format standardization
18. Mobile responsiveness

## Implementation Strategy

1. **Check existing controllers and routes** for all profile endpoints
2. **Verify database table structures** match expected columns
3. **Fix validation rules** to allow proper data
4. **Update model $fillable arrays**
5. **Ensure proper relationship loading**
6. **Fix Vue components** to properly display/edit data
7. **Test each fix** before moving to next

## Files to Check/Modify

### Controllers:
- ProfileController
- UserEducationController  
- UserWorkExperienceController
- FamilyMemberController
- PassportController
- VisaHistoryController
- DocumentController (find it)
- FinancialInformationController (find it)
- SecurityInformationController (find it)
- UserLanguageController (find it)
- CertificationController (find it)
- ReferenceController (find it)

### Models:
- User
- UserProfile
- UserEducation
- UserWorkExperience
- UserFamilyMember
- UserPassport
- UserVisaHistory
- UserDocument
- UserFinancialInformation
- UserSecurityInformation
- UserLanguage
- UserSkill
- Language
- LanguageTest

### Vue Components:
- Profile/Edit.vue
- Profile/Show.vue
- Components/Profile/*

### Composables:
- useBangladeshFormat.js

## Next Steps

1. Run semantic search to find missing controllers
2. Check routes/web.php for profile routes
3. Start with Phase 1 critical fixes
4. Test each fix on live server
5. Document changes
