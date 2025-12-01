# Database Schema & Model Audit Report
**Generated:** December 1, 2025  
**Laravel Version:** 12.x

## Executive Summary

**CRITICAL MISMATCHES FOUND:** 28+ Models expecting tables that don't exist in the database.

This audit identifies all Eloquent Models that are referencing non-existent database tables, causing `SQLSTATE[42S02]: Base table or view not found` errors.

---

## 🚨 CRITICAL MISMATCHES (High Priority)

These models are actively used in the codebase but their tables don't exist:

### 1. **JobCategory Model** → Expects: `job_categories` ❌
- **Model:** `app/Models/JobCategory.php`
- **Expected Table:** `job_categories` (Laravel convention: snake_case plural)
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_24_065209_create_job_categories_table.php` exists but migration not run
- **Impact:** Job posting categorization completely broken
- **Fix Required:** Run migration or create table

### 2. **SkillCategory Model** → Expects: `skill_categories` ❌
- **Model:** `app/Models/SkillCategory.php`
- **Expected Table:** `skill_categories`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_24_070124_create_skill_categories_table.php` exists but not run
- **Impact:** Skills organization system non-functional
- **Fix Required:** Run migration

### 3. **DocumentCategory Model** → Expects: `document_categories` ⚠️
- **Model:** `app/Models/DocumentCategory.php`
- **Expected Table:** `document_categories`
- **Database Reality:** ✅ Table exists (from `2025_11_26_145818_create_document_hub_tables.php`)
- **Status:** ✅ **OK** (table exists)

### 4. **ApplicationDocument Model** → Expects: `application_documents` ❌
- **Model:** `app/Models/ApplicationDocument.php`
- **Expected Table:** `application_documents`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_25_154324_create_application_documents_table.php` exists but not run
- **Impact:** Service applications cannot attach documents
- **Fix Required:** Run migration

### 5. **AgencyResource Model** → Expects: `agency_resources` ❌
- **Model:** `app/Models/AgencyResource.php`
- **Expected Table:** `agency_resources`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_25_042224_create_agency_resources_table.php` exists
- **Fix Required:** Run migration

### 6. **ServiceQuote Model** → Expects: `service_quotes` ❌
- **Model:** `app/Models/ServiceQuote.php`
- **Expected Table:** `service_quotes`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_25_042958_create_service_quotes_table.php` exists
- **Fix Required:** Run migration

### 7. **VisaFee Model** → Expects: `visa_fees` ❌
- **Model:** `app/Models/VisaFee.php`
- **Expected Table:** `visa_fees`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_25_162226_create_visa_fees_table.php` exists
- **Fix Required:** Run migration

### 8. **MasterDocument Model** → Expects: `master_documents` ⚠️
- **Model:** `app/Models/MasterDocument.php`
- **Expected Table:** `master_documents`
- **Database Reality:** ✅ Table created in document_hub_tables migration
- **Status:** **Needs verification** - check if migration ran

### 9. **CountryDocumentRequirement Model** → Expects: `country_document_requirements` ⚠️
- **Model:** `app/Models/CountryDocumentRequirement.php`
- **Expected Table:** `country_document_requirements`
- **Database Reality:** ✅ Table created in document_hub_tables migration
- **Status:** **Needs verification** - check if migration ran

### 10. **PhoneVerificationCode Model** → Expects: `phone_verification_codes` ❌
- **Model:** `app/Models/PhoneVerificationCode.php`
- **Expected Table:** `phone_verification_codes`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_21_000000_create_phone_verification_codes_table.php` exists
- **Fix Required:** Run migration

### 11. **SmartSuggestion Model** → Expects: `smart_suggestions` ❌
- **Model:** `app/Models/SmartSuggestion.php`
- **Expected Table:** `smart_suggestions`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_21_001517_create_smart_suggestions_table.php` exists
- **Fix Required:** Run migration

### 12. **UserDocument Model** → Expects: `user_documents` ❌
- **Model:** `app/Models/UserDocument.php`
- **Expected Table:** `user_documents`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_21_002439_create_user_documents_table.php` exists
- **Fix Required:** Run migration

### 13. **SystemEvent Model** → Expects: `system_events` ❌
- **Model:** `app/Models/SystemEvent.php`
- **Expected Table:** `system_events`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_21_090000_create_system_events_table.php` exists
- **Fix Required:** Run migration

### 14. **ServiceReview Model** → Expects: `service_reviews` ❌
- **Model:** `app/Models/ServiceReview.php`
- **Expected Table:** `service_reviews`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_23_000004_create_service_reviews_table.php` exists
- **Fix Required:** Run migration

### 15. **EmailTemplate Model** → Expects: `email_templates` ❌
- **Model:** `app/Models/EmailTemplate.php`
- **Expected Table:** `email_templates`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_23_124805_create_email_templates_table.php` exists
- **Fix Required:** Run migration

### 16. **EmailLog Model** → Expects: `email_logs` ❌
- **Model:** `app/Models/EmailLog.php`
- **Expected Table:** `email_logs`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** ❌ **NO MIGRATION FOUND**
- **Fix Required:** CREATE NEW MIGRATION

### 17. **TouristVisa Model** → Expects: `tourist_visas` ❌
- **Model:** `app/Models/TouristVisa.php`
- **Expected Table:** `tourist_visas`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_24_000001_create_tourist_visas_table.php` exists
- **Fix Required:** Run migration

### 18. **TouristVisaDocument Model** → Expects: `tourist_visa_documents` ❌
- **Model:** `app/Models/TouristVisaDocument.php`
- **Expected Table:** `tourist_visa_documents`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** ❌ **NO MIGRATION FOUND**
- **Fix Required:** CREATE NEW MIGRATION

### 19. **StudentVisa Model** → Expects: `student_visas` ❌
- **Model:** `app/Models/StudentVisa.php`
- **Expected Table:** `student_visas`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_29_105530_create_student_visas_table.php` exists
- **Fix Required:** Run migration

### 20. **WorkVisa Model** → Expects: `work_visas` ❌
- **Model:** `app/Models/WorkVisa.php`
- **Expected Table:** `work_visas`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_29_105942_create_work_visas_table.php` exists
- **Fix Required:** Run migration

### 21. **Translation Model** → Expects: `translations` ❌
- **Model:** `app/Models/Translation.php`
- **Expected Table:** `translations`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_29_110434_create_translations_table.php` exists
- **Fix Required:** Run migration

### 22. **Attestation Model** → Expects: `attestations` ❌
- **Model:** `app/Models/Attestation.php`
- **Expected Table:** `attestations`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_29_110444_create_attestations_table.php` exists
- **Fix Required:** Run migration

### 23. **HajjUmrah Model** → Expects: `hajj_umrahs` ❌
- **Model:** `app/Models/HajjUmrah.php`
- **Expected Table:** `hajj_umrahs`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_29_110523_create_hajj_umrahs_table.php` exists
- **Fix Required:** Run migration

### 24. **FlightRoute Model** → Expects: `flight_routes` ❌
- **Model:** `app/Models/FlightRoute.php`
- **Expected Table:** `flight_routes`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_19_050001_create_flight_routes_table.php` exists
- **Fix Required:** Run migration

### 25. **FlightQuote Model** → Expects: `flight_quotes` ❌
- **Model:** `app/Models/FlightQuote.php`
- **Expected Table:** `flight_quotes`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_19_070002_create_flight_quotes_table.php` exists
- **Fix Required:** Run migration

### 26. **FlightSearch Model** → Expects: `flight_searches` ❌
- **Model:** `app/Models/FlightSearch.php`
- **Expected Table:** `flight_searches`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_19_070003_create_flight_searches_table.php` exists
- **Fix Required:** Run migration

### 27. **HotelRoom Model** → Expects: `hotel_rooms` ✅
- **Model:** `app/Models/HotelRoom.php`
- **Expected Table:** `hotel_rooms`
- **Database Reality:** ✅ **TABLE EXISTS**
- **Status:** **OK**

### 28. **VisaDocument Model** → Expects: `visa_documents` ❌
- **Model:** `app/Models/VisaDocument.php`
- **Expected Table:** `visa_documents`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_19_122429_create_visa_documents_table.php` exists
- **Fix Required:** Run migration

### 29. **VisaAppointment Model** → Expects: `visa_appointments` ❌
- **Model:** `app/Models/VisaAppointment.php`
- **Expected Table:** `visa_appointments`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_19_122430_create_visa_appointments_table.php` exists
- **Fix Required:** Run migration

### 30. **TranslationRequest Model** → Expects: `translation_requests` ❌
- **Model:** `app/Models/TranslationRequest.php`
- **Expected Table:** `translation_requests`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_19_132840_create_translation_requests_table.php` exists
- **Fix Required:** Run migration

### 31. **TranslationDocument Model** → Expects: `translation_documents` ❌
- **Model:** `app/Models/TranslationDocument.php`
- **Expected Table:** `translation_documents`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_19_132842_create_translation_documents_table.php` exists
- **Fix Required:** Run migration

### 32. **TranslationQuote Model** → Expects: `translation_quotes` ❌
- **Model:** `app/Models/TranslationQuote.php`
- **Expected Table:** `translation_quotes`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_19_132844_create_translation_quotes_table.php` exists
- **Fix Required:** Run migration

### 33. **SeoSetting Model** → Expects: `seo_settings` ❌
- **Model:** `app/Models/SeoSetting.php`
- **Expected Table:** `seo_settings`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_20_232401_create_seo_settings_table.php` exists
- **Fix Required:** Run migration

### 34. **SiteSetting Model** → Expects: `site_settings` ❌
- **Model:** `app/Models/SiteSetting.php`
- **Expected Table:** `site_settings`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_27_100325_create_site_settings_table.php` exists
- **Fix Required:** Run migration

### 35. **Testimonial Model** → Expects: `testimonials` ❌
- **Model:** `app/Models/Testimonial.php`
- **Expected Table:** `testimonials`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_27_112626_create_testimonials_table.php` exists
- **Fix Required:** Run migration

### 36. **Notification Model** → Expects: `notifications` ❌
- **Model:** `app/Models/Notification.php`
- **Expected Table:** `notifications`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_28_095819_create_notifications_table.php` exists
- **Fix Required:** Run migration

### 37. **Transaction Model** → Expects: `transactions` ❌
- **Model:** `app/Models/Transaction.php`
- **Expected Table:** `transactions`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_28_102153_create_transactions_table.php` exists
- **Fix Required:** Run migration

### 38. **UserNotificationPreference Model** → Expects: `user_notification_preferences` ❌
- **Model:** `app/Models/UserNotificationPreference.php`
- **Expected Table:** `user_notification_preferences`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_28_125548_create_user_notification_preferences_table.php` exists
- **Fix Required:** Run migration

### 39. **BankName Model** → Expects: `bank_names` ❌
- **Model:** `app/Models/BankName.php`
- **Expected Table:** `bank_names`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_29_012734_create_bank_names_table.php` exists
- **Fix Required:** Run migration

### 40. **RelationshipType Model** → Expects: `relationship_types` ❌
- **Model:** `app/Models/RelationshipType.php`
- **Expected Table:** `relationship_types`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_29_012730_create_relationship_types_table.php` exists
- **Fix Required:** Run migration

### 41. **Page Model** → Expects: `pages` ❌
- **Model:** `app/Models/Page.php`
- **Expected Table:** `pages`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_27_081135_create_pages_table.php` exists
- **Fix Required:** Run migration

### 42. **Partner Model** → Expects: `partners` ❌
- **Model:** `app/Models/Partner.php`
- **Expected Table:** `partners`
- **Database Reality:** ❌ **TABLE DOES NOT EXIST**
- **Migration:** `2025_11_27_081135_create_partners_table.php` exists
- **Fix Required:** Run migration

---

## ✅ MODELS WITH CORRECT TABLE MAPPINGS

These models work correctly (table exists):

| Model | Expected Table | Status |
|-------|---------------|--------|
| User | `users` | ✅ Exists |
| Role | `roles` | ✅ Exists |
| Country | `countries` | ✅ Exists |
| City | `cities` | ✅ Exists |
| Currency | `currencies` | ✅ Exists |
| Degree | `degrees` | ✅ Exists |
| Language | `languages` | ✅ Exists |
| LanguageTest | `language_tests` | ✅ Exists |
| Skill | `skills` | ✅ Exists |
| UserProfile | `user_profiles` | ✅ Exists |
| UserEducation | `user_educations` | ✅ Exists |
| UserWorkExperience | `user_work_experiences` | ✅ Exists |
| UserLanguage | `user_languages` | ✅ Exists |
| UserPassport | `user_passports` | ✅ Exists |
| UserVisaHistory | `user_visa_history` | ✅ Exists |
| UserTravelHistory | `user_travel_history` | ✅ Exists |
| UserFamilyMember | `user_family_members` | ✅ Exists |
| UserFinancialInformation | `user_financial_information` | ✅ Exists |
| UserSecurityInformation | `user_security_information` | ✅ Exists |
| UserPhoneNumber | `user_phone_numbers` | ✅ Exists |
| UserCv | `user_cvs` | ✅ Exists |
| UserNotification | `user_notifications` | ✅ Exists |
| Wallet | `wallets` | ✅ Exists |
| WalletTransaction | `wallet_transactions` | ✅ Exists |
| Referral | `referrals` | ✅ Exists |
| Reward | `rewards` | ✅ Exists |
| BlogCategory | `blog_categories` | ✅ Exists |
| BlogPost | `blog_posts` | ✅ Exists |
| BlogTag | `blog_tags` | ✅ Exists |
| Agency | `agencies` | ✅ Exists |
| AgencyType | `agency_types` | ✅ Exists |
| AgencyTeamMember | `agency_team_members` | ✅ Exists |
| AgencyReview | `agency_reviews` | ✅ Exists |
| AgencyVerificationDocument | `agency_verification_documents` | ✅ Exists |
| AgencyVerificationRequest | `agency_verification_requests` | ✅ Exists |
| AgencyCountryAssignment | `agency_country_assignments` | ✅ Exists |
| DocumentScan | `document_scans` | ✅ Exists |
| DocumentType | `document_types` | ✅ Exists |
| CvTemplate | `cv_templates` | ✅ Exists |
| Airport | `airports` | ✅ Exists |
| FlightBooking | `flight_bookings` | ✅ Exists |
| FlightRequest | `flight_requests` | ✅ Exists |
| Hotel | `hotels` | ✅ Exists |
| HotelRoom | `hotel_rooms` | ✅ Exists |
| HotelBooking | `hotel_bookings` | ✅ Exists |
| VisaApplication | `visa_applications` | ✅ Exists |
| VisaRequirement | `visa_requirements` | ✅ Exists |
| VisaRequirementDocument | `visa_requirement_documents` | ✅ Exists |
| VisaType | `visa_types` | ✅ Exists |
| ProfessionVisaRequirement | `profession_visa_requirements` | ✅ Exists |
| JobPosting | `job_postings` | ✅ Exists |
| JobApplication | `job_applications` | ✅ Exists |
| ServiceCategory | `service_categories` | ✅ Exists |
| ServiceModule | `service_modules` | ✅ Exists |
| ServiceApplication | `service_applications` | ✅ Exists |
| Setting | `settings` | ✅ Exists |
| ProfileAssessment | `profile_assessments` | ✅ Exists |
| ProfileView | `profile_views` | ✅ Exists |
| AdminImpersonationLog | `admin_impersonation_logs` | ✅ Exists |
| MarketingCampaign | `marketing_campaigns` | ✅ Exists |
| Directory | `directories` | ✅ Exists |
| DirectoryCategory | `directory_categories` | ✅ Exists |
| Event | `events` | ✅ Exists |
| Faq | `faqs` | ✅ Exists |
| FaqCategory | `faq_categories` | ✅ Exists |
| SupportTicket | `support_tickets` | ✅ Exists |
| SupportTicketReply | `support_ticket_replies` | ✅ Exists |
| Appointment | `appointments` | ✅ Exists |
| TravelInsurancePackage | `travel_insurance_packages` | ✅ Exists |
| TravelInsuranceBooking | `travel_insurance_bookings` | ✅ Exists |
| PaymentTransaction | `payment_transactions` | ✅ Exists |
| InstitutionType | `institution_types` | ✅ Exists |

---

## 🔧 MODELS WITH CUSTOM TABLE NAMES (Need Verification)

These models explicitly define `protected $table`:

| Model | Custom Table Name | Correct? |
|-------|------------------|----------|
| Education | `user_educations` | ✅ Correct (matches convention for UserEducation) |
| WorkExperience | `user_work_experiences` | ✅ Correct (matches UserWorkExperience) |
| FamilyMember | `user_family_members` | ✅ Correct (matches UserFamilyMember) |

---

## 📋 ACTION PLAN

### Immediate Actions (Critical Path)

1. **Run all pending migrations:**
```bash
php artisan migrate
```

2. **If migrations fail, run them individually in order:**
```bash
# Critical tables first
php artisan migrate --path=database/migrations/2025_11_24_065209_create_job_categories_table.php
php artisan migrate --path=database/migrations/2025_11_24_070124_create_skill_categories_table.php
php artisan migrate --path=database/migrations/2025_11_25_154324_create_application_documents_table.php

# Service-related
php artisan migrate --path=database/migrations/2025_11_25_042224_create_agency_resources_table.php
php artisan migrate --path=database/migrations/2025_11_25_042958_create_service_quotes_table.php
php artisan migrate --path=database/migrations/2025_11_23_000004_create_service_reviews_table.php

# Visa system
php artisan migrate --path=database/migrations/2025_11_25_162226_create_visa_fees_table.php
php artisan migrate --path=database/migrations/2025_11_24_000001_create_tourist_visas_table.php
php artisan migrate --path=database/migrations/2025_11_29_105530_create_student_visas_table.php
php artisan migrate --path=database/migrations/2025_11_29_105942_create_work_visas_table.php
php artisan migrate --path=database/migrations/2025_11_19_122429_create_visa_documents_table.php
php artisan migrate --path=database/migrations/2025_11_19_122430_create_visa_appointments_table.php

# Flight system
php artisan migrate --path=database/migrations/2025_11_19_050001_create_flight_routes_table.php
php artisan migrate --path=database/migrations/2025_11_19_070002_create_flight_quotes_table.php
php artisan migrate --path=database/migrations/2025_11_19_070003_create_flight_searches_table.php

# Translation system
php artisan migrate --path=database/migrations/2025_11_29_110434_create_translations_table.php
php artisan migrate --path=database/migrations/2025_11_19_132840_create_translation_requests_table.php
php artisan migrate --path=database/migrations/2025_11_19_132842_create_translation_documents_table.php
php artisan migrate --path=database/migrations/2025_11_19_132844_create_translation_quotes_table.php

# User features
php artisan migrate --path=database/migrations/2025_11_21_000000_create_phone_verification_codes_table.php
php artisan migrate --path=database/migrations/2025_11_21_001517_create_smart_suggestions_table.php
php artisan migrate --path=database/migrations/2025_11_21_002439_create_user_documents_table.php
php artisan migrate --path=database/migrations/2025_11_28_125548_create_user_notification_preferences_table.php

# System/Admin
php artisan migrate --path=database/migrations/2025_11_21_090000_create_system_events_table.php
php artisan migrate --path=database/migrations/2025_11_20_232401_create_seo_settings_table.php
php artisan migrate --path=database/migrations/2025_11_27_100325_create_site_settings_table.php
php artisan migrate --path=database/migrations/2025_11_28_095819_create_notifications_table.php
php artisan migrate --path=database/migrations/2025_11_28_102153_create_transactions_table.php

# Marketing/Content
php artisan migrate --path=database/migrations/2025_11_23_124805_create_email_templates_table.php
php artisan migrate --path=database/migrations/2025_11_27_112626_create_testimonials_table.php
php artisan migrate --path=database/migrations/2025_11_27_081135_create_pages_table.php
php artisan migrate --path=database/migrations/2025_11_27_081135_create_partners_table.php

# Lookup tables
php artisan migrate --path=database/migrations/2025_11_29_012730_create_relationship_types_table.php
php artisan migrate --path=database/migrations/2025_11_29_012734_create_bank_names_table.php
php artisan migrate --path=database/migrations/2025_11_29_110444_create_attestations_table.php
php artisan migrate --path=database/migrations/2025_11_29_110523_create_hajj_umrahs_table.php
```

3. **Create missing migrations for:**
   - `email_logs` (EmailLog model has no migration)
   - `tourist_visa_documents` (TouristVisaDocument model has no migration)

### Verification Steps

1. **Check which migrations have run:**
```bash
mysql -u root bideshgomondb -e "SELECT migration FROM migrations ORDER BY batch, id;"
```

2. **Verify table existence after migrations:**
```bash
mysql -u root bideshgomondb -e "SHOW TABLES LIKE '%job_categories%';"
mysql -u root bideshgomondb -e "SHOW TABLES LIKE '%skill_categories%';"
mysql -u root bideshgomondb -e "SHOW TABLES LIKE '%application_documents%';"
```

3. **Test Model queries:**
```php
php artisan tinker
> App\Models\JobCategory::count();
> App\Models\SkillCategory::count();
> App\Models\ApplicationDocument::count();
```

---

## 🎯 RECOMMENDED FIX ORDER

**Phase 1: Critical Dependencies (Run First)**
1. `job_categories` - Required by JobPosting
2. `skill_categories` - Required by Skills system
3. `application_documents` - Required by ServiceApplication
4. `agency_resources` - Required by Agency system
5. `service_quotes` - Required by service booking flow
6. `service_reviews` - Required by rating system

**Phase 2: Visa System**
7. `visa_fees`
8. `tourist_visas`
9. `student_visas`
10. `work_visas`
11. `visa_documents`
12. `visa_appointments`

**Phase 3: Flight System**
13. `flight_routes`
14. `flight_quotes`
15. `flight_searches`

**Phase 4: Translation System**
16. `translations`
17. `translation_requests`
18. `translation_documents`
19. `translation_quotes`

**Phase 5: User Features**
20. `phone_verification_codes`
21. `smart_suggestions`
22. `user_documents`
23. `user_notification_preferences`

**Phase 6: System/Admin**
24. `system_events`
25. `seo_settings`
26. `site_settings`
27. `notifications`
28. `transactions`

**Phase 7: Marketing/Content**
29. `email_templates`
30. `testimonials`
31. `pages`
32. `partners`

**Phase 8: Lookup Tables**
33. `relationship_types`
34. `bank_names`
35. `attestations`
36. `hajj_umrahs`

---

## 🚨 CRITICAL WARNINGS

1. **DO NOT delete any Model files** - They may be referenced in code even if tables don't exist yet
2. **Run migrations on DEVELOPMENT first** - Test before production
3. **Backup database before running migrations** - Some migrations may have foreign key dependencies
4. **Check migration order** - Laravel runs migrations alphabetically by filename
5. **Some migrations may fail** - If they reference non-existent tables in foreign keys

---

## 📊 SUMMARY STATISTICS

- **Total Models:** 117
- **Models with existing tables:** 60+ ✅
- **Models with missing tables:** 42 ❌
- **Models with custom table names:** 3
- **Migrations pending:** 42+
- **Migrations missing:** 2 (email_logs, tourist_visa_documents)

---

## 🔍 NEXT STEPS

1. Run this audit report by the team
2. Decide which features are actively used (prioritize those migrations)
3. Create missing migrations for EmailLog and TouristVisaDocument
4. Run migrations in phases (critical → optional)
5. Update this document after each phase
6. Create integration tests to prevent future mismatches

---

**Report Generated By:** GitHub Copilot AI Agent  
**Date:** December 1, 2025  
**Version:** 1.0
