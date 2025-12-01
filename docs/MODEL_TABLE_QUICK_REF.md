# Quick Reference: Models vs Tables

## ❌ MISSING TABLES (42 Critical Issues)

| # | Model Class | Expected Table | Migration File | Status |
|---|-------------|---------------|----------------|--------|
| 1 | JobCategory | `job_categories` | ✅ 2025_11_24_065209 | **NOT RUN** |
| 2 | SkillCategory | `skill_categories` | ✅ 2025_11_24_070124 | **NOT RUN** |
| 3 | ApplicationDocument | `application_documents` | ✅ 2025_11_25_154324 | **NOT RUN** |
| 4 | AgencyResource | `agency_resources` | ✅ 2025_11_25_042224 | **NOT RUN** |
| 5 | ServiceQuote | `service_quotes` | ✅ 2025_11_25_042958 | **NOT RUN** |
| 6 | ServiceReview | `service_reviews` | ✅ 2025_11_23_000004 | **NOT RUN** |
| 7 | VisaFee | `visa_fees` | ✅ 2025_11_25_162226 | **NOT RUN** |
| 8 | TouristVisa | `tourist_visas` | ✅ 2025_11_24_000001 | **NOT RUN** |
| 9 | StudentVisa | `student_visas` | ✅ 2025_11_29_105530 | **NOT RUN** |
| 10 | WorkVisa | `work_visas` | ✅ 2025_11_29_105942 | **NOT RUN** |
| 11 | VisaDocument | `visa_documents` | ✅ 2025_11_19_122429 | **NOT RUN** |
| 12 | VisaAppointment | `visa_appointments` | ✅ 2025_11_19_122430 | **NOT RUN** |
| 13 | FlightRoute | `flight_routes` | ✅ 2025_11_19_050001 | **NOT RUN** |
| 14 | FlightQuote | `flight_quotes` | ✅ 2025_11_19_070002 | **NOT RUN** |
| 15 | FlightSearch | `flight_searches` | ✅ 2025_11_19_070003 | **NOT RUN** |
| 16 | Translation | `translations` | ✅ 2025_11_29_110434 | **NOT RUN** |
| 17 | TranslationRequest | `translation_requests` | ✅ 2025_11_19_132840 | **NOT RUN** |
| 18 | TranslationDocument | `translation_documents` | ✅ 2025_11_19_132842 | **NOT RUN** |
| 19 | TranslationQuote | `translation_quotes` | ✅ 2025_11_19_132844 | **NOT RUN** |
| 20 | PhoneVerificationCode | `phone_verification_codes` | ✅ 2025_11_21_000000 | **NOT RUN** |
| 21 | SmartSuggestion | `smart_suggestions` | ✅ 2025_11_21_001517 | **NOT RUN** |
| 22 | UserDocument | `user_documents` | ✅ 2025_11_21_002439 | **NOT RUN** |
| 23 | UserNotificationPreference | `user_notification_preferences` | ✅ 2025_11_28_125548 | **NOT RUN** |
| 24 | SystemEvent | `system_events` | ✅ 2025_11_21_090000 | **NOT RUN** |
| 25 | SeoSetting | `seo_settings` | ✅ 2025_11_20_232401 | **NOT RUN** |
| 26 | SiteSetting | `site_settings` | ✅ 2025_11_27_100325 | **NOT RUN** |
| 27 | Notification | `notifications` | ✅ 2025_11_28_095819 | **NOT RUN** |
| 28 | Transaction | `transactions` | ✅ 2025_11_28_102153 | **NOT RUN** |
| 29 | EmailTemplate | `email_templates` | ✅ 2025_11_23_124805 | **NOT RUN** |
| 30 | Testimonial | `testimonials` | ✅ 2025_11_27_112626 | **NOT RUN** |
| 31 | Page | `pages` | ✅ 2025_11_27_081135 | **NOT RUN** |
| 32 | Partner | `partners` | ✅ 2025_11_27_081135 | **NOT RUN** |
| 33 | RelationshipType | `relationship_types` | ✅ 2025_11_29_012730 | **NOT RUN** |
| 34 | BankName | `bank_names` | ✅ 2025_11_29_012734 | **NOT RUN** |
| 35 | Attestation | `attestations` | ✅ 2025_11_29_110444 | **NOT RUN** |
| 36 | HajjUmrah | `hajj_umrahs` | ✅ 2025_11_29_110523 | **NOT RUN** |
| 37 | MasterDocument | `master_documents` | ✅ 2025_11_26_145818 (hub) | **VERIFY** |
| 38 | CountryDocumentRequirement | `country_document_requirements` | ✅ 2025_11_26_145818 (hub) | **VERIFY** |
| 39 | EmailLog | `email_logs` | ❌ **MISSING** | **CREATE** |
| 40 | TouristVisaDocument | `tourist_visa_documents` | ❌ **MISSING** | **CREATE** |

---

## ✅ WORKING TABLES (60+ Models)

These models have correct table mappings and tables exist:

### Core System
- User → `users`
- Role → `roles`
- Wallet → `wallets`
- WalletTransaction → `wallet_transactions`
- Referral → `referrals`
- Reward → `rewards`

### Profile System
- UserProfile → `user_profiles`
- UserEducation → `user_educations`
- UserWorkExperience → `user_work_experiences`
- UserLanguage → `user_languages`
- UserPassport → `user_passports`
- UserVisaHistory → `user_visa_history`
- UserTravelHistory → `user_travel_history`
- UserFamilyMember → `user_family_members`
- UserFinancialInformation → `user_financial_information`
- UserSecurityInformation → `user_security_information`
- UserPhoneNumber → `user_phone_numbers`
- UserCv → `user_cvs`
- UserNotification → `user_notifications`

### Location Data
- Country → `countries`
- City → `cities`
- Currency → `currencies`
- Airport → `airports`

### Education & Skills
- Degree → `degrees`
- Language → `languages`
- LanguageTest → `language_tests`
- Skill → `skills`

### Agency System
- Agency → `agencies`
- AgencyType → `agency_types`
- AgencyTeamMember → `agency_team_members`
- AgencyReview → `agency_reviews`
- AgencyVerificationDocument → `agency_verification_documents`
- AgencyVerificationRequest → `agency_verification_requests`
- AgencyCountryAssignment → `agency_country_assignments`

### Services
- ServiceCategory → `service_categories`
- ServiceModule → `service_modules`
- ServiceApplication → `service_applications`

### Travel Services
- FlightBooking → `flight_bookings`
- FlightRequest → `flight_requests`
- Hotel → `hotels`
- HotelRoom → `hotel_rooms`
- HotelBooking → `hotel_bookings`
- TravelInsurancePackage → `travel_insurance_packages`
- TravelInsuranceBooking → `travel_insurance_bookings`

### Visa System
- VisaApplication → `visa_applications`
- VisaRequirement → `visa_requirements`
- VisaRequirementDocument → `visa_requirement_documents`
- VisaType → `visa_types`
- ProfessionVisaRequirement → `profession_visa_requirements`

### Jobs
- JobPosting → `job_postings`
- JobApplication → `job_applications`

### Content/Marketing
- BlogCategory → `blog_categories`
- BlogPost → `blog_posts`
- BlogTag → `blog_tags`
- MarketingCampaign → `marketing_campaigns`
- Directory → `directories`
- DirectoryCategory → `directory_categories`
- Event → `events`
- Faq → `faqs`
- FaqCategory → `faq_categories`

### Support
- SupportTicket → `support_tickets`
- SupportTicketReply → `support_ticket_replies`
- Appointment → `appointments`

### Documents
- DocumentScan → `document_scans`
- DocumentType → `document_types`
- DocumentCategory → `document_categories`
- CvTemplate → `cv_templates`

### System/Admin
- Setting → `settings`
- ProfileAssessment → `profile_assessments`
- ProfileView → `profile_views`
- AdminImpersonationLog → `admin_impersonation_logs`
- PaymentTransaction → `payment_transactions`
- InstitutionType → `institution_types`

---

## 🔧 MODELS WITH EXPLICIT $table PROPERTY

These models override Laravel's default table naming:

| Model | Explicit Table | Reason |
|-------|---------------|---------|
| Education | `user_educations` | Prevent confusion with "educations" |
| WorkExperience | `user_work_experiences` | Prevent confusion with "work_experiences" |
| FamilyMember | `user_family_members` | Prevent confusion with "family_members" |
| UserEducation | `user_educations` | Match Education model |
| UserWorkExperience | `user_work_experiences` | Match WorkExperience model |
| UserVisaHistory | `user_visa_history` | Singular "history" not "histories" |
| UserTravelHistory | `user_travel_history` | Singular "history" not "histories" |
| UserFinancialInformation | `user_financial_information` | Singular "information" |
| UserSecurityInformation | `user_security_information` | Singular "information" |

---

## 🎯 MIGRATION EXECUTION ORDER

**Priority 1 - Critical for Core Features:**
1. job_categories (JobPosting dependency)
2. skill_categories (Skills system)
3. application_documents (ServiceApplication)

**Priority 2 - Service Features:**
4. agency_resources
5. service_quotes  
6. service_reviews

**Priority 3 - Visa System:**
7-12. All visa-related tables

**Priority 4 - Supporting Systems:**
13+. Translation, Flight, User Features, etc.

---

## 📝 QUICK FIX COMMAND

Run ALL pending migrations at once (⚠️ risky, test in dev first):

```bash
php artisan migrate --force
```

Or use the phased approach:

```bash
# Windows
.\scripts\migrate-missing-tables.ps1

# Linux/Mac
bash scripts/migrate-missing-tables.sh
```

---

## 🚨 CRITICAL NOTES

1. **job_categories** is likely causing most "table not found" errors if JobPosting is actively used
2. Two models have NO migrations: EmailLog, TouristVisaDocument (need to be created)
3. Document hub tables (master_documents, country_document_requirements) exist but verify with: `SHOW TABLES LIKE '%document%';`
4. Some migrations may have duplicate table creation logic (check 2025_11_30_* files)

---

## ✅ VERIFICATION COMMANDS

After running migrations:

```bash
# Count tables
mysql -u root bideshgomondb -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='bideshgomondb';"

# List all tables
mysql -u root bideshgomondb -e "SHOW TABLES;"

# Test critical models
php artisan tinker
> App\Models\JobCategory::count();
> App\Models\SkillCategory::count();
> App\Models\ApplicationDocument::count();
```
