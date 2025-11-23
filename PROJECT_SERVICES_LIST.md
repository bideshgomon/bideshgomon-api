# BideshGomon Platform - Complete Services List

## Overview
This document provides a comprehensive inventory of all services available in the BideshGomon platform, organized by category with their associated models, controllers, routes, and key features.

---

## 1. VISA PROCESSING SERVICES

### Models
- `VisaApplication` - Core visa application data
- `VisaAppointment` - Appointment scheduling
- `VisaDocument` - Document management
- `VisaTracking` - Application tracking

### Controllers
- `VisaApplicationController` (User)
- `Admin\VisaController` (Admin)

### Routes
**User Routes** (`/services/visa/`)
- `GET /` - View available visa services
- `GET /apply` - Application form
- `POST /` - Submit application
- `GET /my-applications` - User's visa applications
- `GET /{application}` - View specific application
- `POST /{application}/documents` - Upload documents
- `GET /{application}/payment` - Payment page
- `POST /{application}/payment` - Process payment
- `POST /{application}/cancel` - Cancel application

**Admin Routes** (`/admin/visa-applications/`)
- `GET /` - List all applications
- `GET /{application}` - View application details
- `POST /{application}/status` - Update status
- `POST /{application}/assign` - Assign to agent
- `POST /{application}/approve` - Approve application
- `POST /{application}/reject` - Reject application
- `POST /{application}/request-documents` - Request additional documents
- `POST /{application}/schedule-appointment` - Schedule appointment
- `POST /{application}/priority` - Update priority level
- `POST /{application}/notes` - Add notes
- `POST /documents/{document}/verify` - Verify documents

### Features
- Multi-step application process
- Document upload & verification
- Appointment scheduling
- Payment integration
- Status tracking
- Admin approval workflow
- Priority handling

---

## 2. TRAVEL INSURANCE SERVICES

### Models
- `TravelInsuranceBooking` - Insurance bookings
- `TravelInsurancePackage` - Available packages

### Controllers
- `TravelInsuranceController` (User)

### Routes
**User Routes** (`/services/travel-insurance/`)
- `GET /` - View insurance packages
- `GET /quote` - Get quote
- `POST /calculate` - Calculate premium
- `GET /{package}/buy` - Purchase form
- `POST /buy` - Complete purchase
- `GET /my-policies` - User's policies
- `GET /policy/{id}` - View policy details

### Features
- Multiple insurance packages
- Quote calculator
- Coverage comparison
- Policy management
- Digital policy documents

---

## 3. FLIGHT BOOKING SERVICES

### Models
- `FlightBooking` - Direct bookings
- `FlightRoute` - Available routes
- `FlightRequest` - Request-based bookings
- `FlightQuote` - Agency quotes

### Controllers
- `FlightBookingController` (User - Direct booking)
- `FlightRequestController` (User - Request system)
- `Admin\FlightRequestController` (Admin)
- `Agency\FlightRequestController` (Agency)

### Routes
**User Routes - Direct Booking** (`/services/flights/`)
- `GET /` - Search flights
- `POST /search` - Search flights
- `GET /{routeId}/book` - Booking form
- `POST /book` - Complete booking
- `GET /my-bookings` - User's bookings
- `GET /booking/{id}` - Booking details
- `POST /booking/{id}/cancel` - Cancel booking
- `GET /booking/{id}/ticket` - Download ticket

**User Routes - Request System** (`/services/flight-requests/`)
- `GET /create` - Create flight request
- `POST /` - Submit request
- `GET /` - View user's requests
- `GET /{id}` - Request details
- `POST /{requestId}/quotes/{quoteId}/accept` - Accept quote
- `POST /{id}/cancel` - Cancel request

**Admin Routes** (`/admin/flight-requests/`)
- `GET /` - List all requests
- `GET /{id}` - Request details
- `POST /{id}/assign` - Assign to agency
- `POST /{id}/notes` - Update notes
- `POST /{id}/cancel` - Cancel request
- `POST /bulk-assign` - Bulk assignment

**Agency Routes** (`/agency/flight-requests/`)
- `GET /` - Assigned requests
- `GET /{id}` - Request details
- `GET /{id}/quote/create` - Create quote form
- `POST /{id}/quote` - Submit quote
- `PUT /{id}/quote/{quoteId}` - Update quote

### Features
- Dual booking system (direct + request-based)
- Route management
- Multi-agency quote system
- Ticket generation
- Booking management
- Agency assignment workflow

---

## 4. HOTEL BOOKING SERVICES

### Models
- `Hotel` - Hotel information
- `HotelRoom` - Room types & availability
- `HotelBooking` - Bookings
- `HotelAmenity` - Hotel amenities

### Controllers
- `HotelBookingController` (User)
- `Admin\HotelController` (Admin)

### Routes
**User Routes** (`/services/hotels/`)
- `GET /` - Browse hotels
- `GET /{hotel}` - Hotel details
- `GET /{hotel}/rooms/{room}/book` - Booking form
- `POST /bookings` - Create booking
- `GET /bookings/my-bookings` - User's bookings
- `GET /bookings/{booking}` - Booking details
- `GET /bookings/{booking}/payment` - Payment page
- `POST /bookings/{booking}/payment` - Process payment
- `GET /bookings/{booking}/confirmation` - Confirmation
- `POST /bookings/{booking}/cancel` - Cancel booking

**Admin Routes** (`/admin/hotels/`)
- `GET /` - List hotels
- `GET /create` - Create hotel form
- `POST /` - Store hotel
- `GET /{hotel}` - Hotel details
- `GET /{hotel}/edit` - Edit form
- `PUT /{hotel}` - Update hotel
- `DELETE /{hotel}` - Delete hotel
- `POST /{hotel}/toggle-status` - Toggle status
- `GET /{hotel}/rooms` - Manage rooms
- `POST /{hotel}/rooms` - Add room
- `PUT /{hotel}/rooms/{room}` - Update room
- `DELETE /{hotel}/rooms/{room}` - Delete room

**Admin Booking Routes** (`/admin/hotel-bookings/`)
- `GET /` - All bookings
- `GET /{booking}` - Booking details
- `POST /{booking}/status` - Update status

**Admin Analytics** (`/admin/hotels-analytics`)
- Hotel performance metrics

### Features
- Hotel directory
- Room management
- Availability tracking
- Online booking
- Payment processing
- Booking confirmation
- Admin hotel management
- Analytics dashboard

---

## 5. JOB POSTING & APPLICATION SERVICES

### Models
- `JobPosting` - Job listings
- `JobApplication` - Applications

### Controllers
- `JobController` (User)
- `Admin\AdminJobPostingController` (Admin)
- `Admin\AdminJobApplicationController` (Admin)

### Routes
**User Routes** (`/services/jobs/`)
- `GET /` - Browse jobs
- `GET /{job}` - Job details
- `GET /{job}/apply` - Application form
- `POST /{job}/apply` - Submit application

**Admin Job Routes** (`/admin/jobs/`)
- `GET /` - List all jobs
- `GET /create` - Create job form
- `POST /` - Store job
- `GET /{id}` - Job details
- `GET /{id}/edit` - Edit form
- `PUT /{id}` - Update job
- `DELETE /{id}` - Delete job
- `POST /{id}/toggle-featured` - Toggle featured
- `POST /{id}/toggle-active` - Toggle active
- `POST /bulk-delete` - Bulk delete
- `POST /bulk-update-status` - Bulk status update

**Admin Application Routes** (`/admin/applications/`, `/admin/job-applications/`)
- `GET /` - List applications
- `GET /{id}` - Application details
- `POST /{id}/update-status` - Update status
- `POST /bulk-update-status` - Bulk status update
- `GET /export` - Export applications

### Features
- Job listing management
- Featured jobs
- Application submission
- Application tracking
- Status management
- Bulk operations
- Export functionality

---

## 6. CV BUILDER SERVICES

### Models
- `UserCv` - User CV data
- `CvTemplate` - Available templates

### Controllers
- `CvBuilderController` (User)

### Routes
**User Routes** (`/services/cv-builder/`)
- `GET /` - CV dashboard
- `GET /create` - Create new CV
- `GET /{cv}` - View CV
- `GET /{cv}/edit` - Edit CV
- `PUT /{cv}` - Update CV
- `DELETE /{cv}` - Delete CV
- `GET /{cv}/preview` - Preview CV
- `GET /{cv}/download` - Download as PDF
- `POST /{cv}/duplicate` - Duplicate CV
- `GET /templates` - Browse templates

### Features
- Multiple CV templates
- CV builder interface
- PDF generation
- CV preview
- Template selection
- CV duplication

---

## 7. WALLET & FINANCIAL SERVICES

### Models
- `Wallet` - User wallets
- `WalletTransaction` - Transaction history

### Controllers
- `WalletController` (User)
- `Admin\WalletController` (Admin)

### Services
- `WalletService` - Core wallet business logic

### Routes
**User Routes** (`/wallet/`)
- `GET /` - Wallet dashboard
- `GET /transactions` - Transaction history
- `POST /add-funds` - Add money
- `POST /withdraw` - Withdraw funds

**Admin Routes** (`/admin/wallets/`)
- `GET /` - List all wallets
- `GET /{wallet}` - Wallet details
- `POST /{wallet}/credit` - Credit wallet
- `POST /{wallet}/debit` - Debit wallet
- `POST /{wallet}/toggle-status` - Toggle wallet status
- `POST /transactions/{transaction}/reverse` - Reverse transaction

### Features
- Digital wallet
- Fund management
- Transaction history
- Admin wallet management
- Transaction reversal

---

## 8. REFERRAL & REWARDS SERVICES

### Models
- `Referral` - Referral tracking
- `Reward` - Reward points & redemptions

### Controllers
- `ReferralController` (User)
- `Admin\RewardController` (Admin)

### Services
- `ReferralService` - Referral business logic

### Routes
**User Routes** (`/referrals/`)
- `GET /` - Referral dashboard
- `GET /referrals` - View referrals
- `GET /rewards` - View rewards

**Admin Routes** (`/admin/rewards/`)
- `GET /` - List all rewards
- `POST /{reward}/approve` - Approve reward
- `POST /{reward}/reject` - Reject reward

### Features
- Referral link generation
- Referral tracking
- Reward points system
- Reward redemption
- Admin approval workflow

---

## 9. PROFILE ASSESSMENT SERVICES

### Models
- `ProfileAssessment` - Assessment data

### Services
- `ProfileAssessmentService` - Assessment logic

### Routes
**User Routes** (`/profile/assessment/`)
- Assessment questionnaire
- Results & recommendations

### Features
- Profile completeness scoring
- Eligibility assessment
- Personalized recommendations

---

## 10. TRANSLATION SERVICES

### Models
- `TranslationRequest` - Translation requests
- `TranslationDocument` - Documents

### Controllers
- `TranslationRequestController` (User)
- `Admin\TranslationController` (Admin)

### Routes
**User Routes** (`/services/translation/`)
- `GET /` - Translation services
- `GET /create` - Request form
- `POST /` - Submit request
- `GET /my-requests` - User's requests
- `GET /{translation}` - Request details
- `POST /{translation}/cancel` - Cancel request

### Features
- Document translation requests
- Multiple language support
- Translation tracking
- Document upload

---

## 11. DOCUMENT MANAGEMENT SERVICES

### Models
- `Document` - User documents
- `VisaDocument` - Visa-specific documents
- `TranslationDocument` - Translation documents

### Controllers
- `DocumentController` (User)
- `Admin\AdminDocumentVerificationController` (Admin)

### Services
- `DocumentVerificationService` - Document verification logic

### Routes
**User Routes** (`/documents/`)
- `GET /` - User's documents
- `POST /` - Upload document
- `DELETE /{document}` - Delete document

**Admin Routes** (`/admin/documents/verify/`)
- `GET /` - Documents needing verification
- `POST /{document}/approve` - Approve document
- `POST /{document}/reject` - Reject document

### Features
- Document upload
- Document storage
- Verification workflow
- Admin approval/rejection

---

## 12. NOTIFICATION SERVICES

### Models
- `Notification` - System notifications

### Controllers
- `NotificationController` (User)
- `Admin\AdminNotificationController` (Admin)

### Services
- `NotificationService` - Notification dispatch
- `SmsService` - SMS notifications

### Routes
**User Routes** (`/notifications/`)
- `GET /` - User notifications
- `POST /{notification}/read` - Mark as read
- `POST /read-all` - Mark all as read

**Admin Routes** (`/admin/notifications/`)
- `GET /` - All notifications
- `POST /broadcast` - Broadcast notification

### Features
- In-app notifications
- SMS notifications
- Email notifications
- Broadcast messaging
- Read/unread tracking

---

## 13. BLOG & CONTENT SERVICES

### Models
- `BlogPost` - Blog articles
- `BlogCategory` - Post categories
- `BlogTag` - Post tags

### Controllers
- `BlogController` (Public)
- `Admin\BlogPostController` (Admin)
- `Admin\BlogCategoryController` (Admin)
- `Admin\BlogTagController` (Admin)

### Routes
**Public Routes** (`/blog/`)
- `GET /` - Blog listing
- `GET /{slug}` - Blog post

**Admin Routes** (`/admin/blog/`)
- Resource routes for posts, categories, tags

### Features
- Blog post management
- Category & tag system
- SEO-friendly URLs
- Content publishing

---

## 14. USER PROFILE SERVICES

### Models
- `User` - User accounts
- `EducationBackground` - Education history
- `WorkExperience` - Work history
- `Passport` - Passport information
- `TravelHistory` - Travel records
- `VisaHistory` - Visa records
- `Language` - Language proficiency

### Controllers
- `ProfileController` (User)
- `PublicProfileController` (Public)

### Routes
**User Routes** (`/profile/`)
- `GET /edit` - Edit profile
- `PATCH /` - Update profile
- `DELETE /` - Delete account
- `GET /financial` - Financial information
- `POST /financial` - Update financial info
- `GET /languages` - Language management
- `POST /languages` - Add language
- `PUT /languages/{id}` - Update language
- `DELETE /languages/{id}` - Delete language
- `GET /education` - Education history
- `POST /education` - Add education
- `PUT /education/{id}` - Update education
- `DELETE /education/{id}` - Delete education
- `GET /work-experience` - Work history
- `POST /work-experience` - Add experience
- `PUT /work-experience/{id}` - Update experience
- `DELETE /work-experience/{id}` - Delete experience
- `GET /passports` - Passport management
- `POST /passports` - Add passport
- `PUT /passports/{id}` - Update passport
- `DELETE /passports/{id}` - Delete passport
- `GET /travel-history` - Travel history
- `POST /travel-history` - Add travel
- `PUT /travel-history/{id}` - Update travel
- `DELETE /travel-history/{id}` - Delete travel
- `GET /visa-history` - Visa history
- `POST /visa-history` - Add visa
- `PUT /visa-history/{id}` - Update visa
- `DELETE /visa-history/{id}` - Delete visa

**Public Routes**
- `GET /profile/{slug}` - Public profile view

### Features
- Comprehensive profile management
- Multi-section profile (personal, financial, education, work, travel)
- Public profile pages
- Document management
- Profile assessment integration

---

## 15. ADMIN MANAGEMENT SERVICES

### Models
- Various (User, Settings, Analytics data)

### Controllers
- `Admin\AdminDashboardController`
- `Admin\AdminUserController`
- `Admin\AdminSettingsController`
- `Admin\AdminAnalyticsController`
- `Admin\AdminImpersonationController`
- `Admin\AdminImpersonationLogController`
- `Admin\SeoSettingsController`
- `Admin\ServiceManagementController`

### Routes
**Dashboard** (`/admin/`)
- `GET /dashboard` - Main dashboard

**User Management** (`/admin/users/`)
- `GET /` - List users
- `GET /create` - Create user form
- `POST /` - Store user
- `GET /{id}` - User details
- `GET /{id}/edit` - Edit form
- `PUT /{id}` - Update user
- `POST /{id}/suspend` - Suspend user
- `POST /{id}/unsuspend` - Unsuspend user
- `POST /{id}/update-role` - Update role
- `POST /{id}/impersonate` - Impersonate user
- `DELETE /{id}` - Delete user
- `POST /bulk-suspend` - Bulk suspend
- `POST /bulk-unsuspend` - Bulk unsuspend
- `GET /export` - Export users

**Impersonation** (`/admin/`)
- `POST /users/{id}/impersonate` - Start impersonation
- `POST /impersonation/leave` - Stop impersonation
- `GET /impersonations` - Impersonation logs
- `GET /impersonations/export` - Export logs

**Analytics** (`/admin/analytics/`)
- `GET /` - Analytics dashboard
- `GET /export` - Export analytics

**Settings** (`/admin/settings/`)
- `GET /` - Settings page
- `POST /` - Update settings
- `POST /seed` - Seed settings

**SEO Settings** (`/admin/seo-settings/`)
- `GET /` - SEO management (11 page types)
- `PUT /{pageType}` - Update SEO
- `DELETE /{pageType}` - Reset SEO
- `POST /generate-sitemap` - Generate sitemap

**Service Management** (`/admin/services/`)
- `GET /` - Service dashboard

### Features
- Comprehensive admin dashboard
- User management & impersonation
- Role-based access control
- Analytics & reporting
- System settings
- SEO management for all pages
- Audit logging
- Data export capabilities

---

## 16. AUTHENTICATION SERVICES

### Controllers
- Various Auth controllers (built-in Laravel + Breeze)
- `SocialAuthController` (OAuth)

### Routes
- Standard Laravel authentication routes
- `GET /auth/google` - Google OAuth
- `GET /auth/google/callback` - OAuth callback

### Features
- Email/password authentication
- Google OAuth integration
- Session management
- Password reset
- Email verification

---

## 17. SMART SUGGESTION SERVICES

### Services
- `SmartSuggestionsService` - AI-powered recommendations

### Features
- Personalized service recommendations
- Profile-based suggestions
- Intelligent matching

---

## SUMMARY STATISTICS

### Total Services Categories: 17

### Service Breakdown:
1. **Core Travel Services**: 4 (Visa, Insurance, Flights, Hotels)
2. **Employment Services**: 2 (Jobs, CV Builder)
3. **Financial Services**: 2 (Wallet, Referrals)
4. **Support Services**: 3 (Translation, Documents, Notifications)
5. **Profile Services**: 2 (Profile Management, Assessment)
6. **Content Services**: 1 (Blog)
7. **Admin Services**: 2 (Management, Analytics)
8. **Authentication**: 1 (Auth + OAuth)

### Total Models: 59
### Total Controllers: 40+
### Total Routes: 200+
### Total Service Classes: 49

---

## TECHNOLOGY STACK

### Backend
- Laravel 12.38.1
- PHP 8.2.12
- SQLite Database

### Frontend
- Vue 3
- Inertia.js
- Vite 7.2.2

### Features
- RESTful API architecture
- Role-based access (User, Admin, Agency)
- Service layer architecture
- Payment integration
- Document management
- Notification system
- Analytics & reporting
- SEO management
- Multi-language support

---

*Last Updated: 2025*
*Project: BideshGomon - Immigration & Travel Services Platform*
