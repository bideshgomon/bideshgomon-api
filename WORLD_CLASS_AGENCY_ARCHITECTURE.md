# 🌍 World-Class Multi-Service Agency Architecture

**Date:** November 24, 2025  
**Vision:** Make local life easy through intelligent service-to-agency matching

---

## 🎯 SERVICE ASSIGNMENT MODELS

### Model 1: **Country-Based Multi-Agency** (Competitive)
**Services:** Tourist Visa, Student Visa, Work Visa, Translation, Document Attestation
**Logic:** Multiple agencies can serve the same country, users choose based on ratings/price
**Example:** 
- 3 agencies handle Thailand Tourist Visa
- User sees all 3 quotes, picks best one
- Agencies compete on price, speed, quality

### Model 2: **Exclusive Resource Assignment** (Protected Territory)
**Services:** University Applications, School Admissions
**Logic:** First agency to add a university "owns" it, others need admin approval
**Example:**
- Agency A adds "University of Tokyo" → exclusively handles applications
- Agency B wants to handle it too → requires admin approval
- Prevents conflicts, ensures quality control

### Model 3: **Global Single Agency** (Monopoly)
**Services:** Air Tickets, Travel Insurance, Global Shipping
**Logic:** One agency handles worldwide, typically API-integrated services
**Example:**
- FlightHub Agency handles ALL air ticket bookings globally
- InsureCo handles ALL travel insurance via their API
- No competition, optimized efficiency

### Model 4: **Multi-Country Multi-Agency** (Regional Specialists)
**Services:** Job Postings, Recruitment, Real Estate
**Logic:** Agencies can handle multiple countries, multiple agencies per country
**Example:**
- RecruitBD handles jobs in BD, India, Malaysia
- TalentGlobal handles jobs in USA, Canada, UK
- Users post job, relevant agencies see it based on location

### Model 5: **Hybrid API + Agency** (Intelligent Routing)
**Services:** Hotel Booking, Car Rental, Event Tickets
**Logic:** Try API first, fallback to agency if API fails or for complex requests
**Example:**
- Simple hotel booking → Booking.com API
- Special requests (wheelchair access, pet-friendly) → Agency handles

### Model 6: **Peer-to-Peer Marketplace** (No Agency)
**Services:** CV Builder, Job Board, Freelance Services
**Logic:** Built-in tools, no agency mediation needed
**Example:**
- User builds CV using templates
- User posts to job board directly
- Platform earns from premium features

---

## 📊 SERVICE CATALOG WITH ASSIGNMENT RULES

### 🛂 VISA SERVICES

#### 1. Tourist Visa
- **Model:** Country-Based Multi-Agency (Competitive)
- **Assignment:** Multiple agencies per country
- **Selection:** User chooses from quotes
- **Example:**
  - Thailand Tourist Visa: 5 agencies
  - User submits, all 5 can quote
  - User picks based on price/reviews

#### 2. Student Visa
- **Model:** Country-Based Multi-Agency
- **Assignment:** Multiple agencies per country
- **Special:** Linked to university assignments

#### 3. Work Visa
- **Model:** Country-Based Multi-Agency
- **Assignment:** Multiple agencies per country
- **Special:** May require employer sponsorship docs

#### 4. Business Visa
- **Model:** Country-Based Multi-Agency
- **Assignment:** Multiple agencies per country

#### 5. Medical Visa
- **Model:** Country-Based Multi-Agency
- **Assignment:** Specialized medical visa agencies

#### 6. Family Reunion Visa
- **Model:** Country-Based Multi-Agency
- **Assignment:** Multiple agencies per country

---

### 🎓 EDUCATION SERVICES

#### 7. University Application
- **Model:** Exclusive Resource Assignment (Protected)
- **Assignment:** First agency to add university owns it
- **Database Structure:**
  ```
  universities:
    - id
    - name, country_id
    - managed_by_agency_id (exclusive owner)
    - other_agencies[] (approved by admin)
  
  agency_university_assignments:
    - agency_id
    - university_id
    - is_primary (true for owner)
    - approved_by_admin
    - commission_rate
  ```
- **Process:**
  1. Agency A adds "Harvard University"
  2. Agency A becomes primary handler
  3. Agency B requests access → needs admin approval
  4. Admin can approve with custom commission split
- **Revenue Split:** Primary agency 70%, Secondary 30%, Platform 20%

#### 8. School Application
- **Model:** Exclusive Resource Assignment
- **Same logic as universities**

#### 9. Language Course Application
- **Model:** Country-Based Multi-Agency
- **Assignment:** Multiple agencies per country

#### 10. Scholarship Application
- **Model:** Country-Based Multi-Agency
- **Assignment:** Multiple agencies per country

---

### ✈️ TRAVEL SERVICES

#### 11. Air Ticket Booking
- **Model:** Global Single Agency (Monopoly)
- **Assignment:** One agency worldwide (API-integrated)
- **Platform Partner:** FlightHub or similar
- **Process:**
  1. User searches flights
  2. Platform API shows results
  3. User books
  4. Agency fulfills via API
  5. Platform earns commission (5-10%)
- **Backup:** If API fails, manual processing by agency

#### 12. Hotel Booking
- **Model:** Hybrid API + Agency
- **Assignment:** Try Booking.com API first, fallback to agencies
- **Process:**
  1. Standard booking → API
  2. Special requests → Route to agency
  3. Group bookings → Route to agency
- **Agencies:** Multiple agencies for complex bookings

#### 13. Travel Insurance
- **Model:** Global Single Agency (API-integrated)
- **Assignment:** One insurance partner (e.g., Allianz, AXA)
- **Process:** Fully automated via API

#### 14. Tour Packages
- **Model:** Multi-Country Multi-Agency
- **Assignment:** Agencies specialize in destinations
- **Example:**
  - Agency A: Thailand, Malaysia, Singapore tours
  - Agency B: Europe tours
  - Agency C: Middle East tours

---

### 💼 EMPLOYMENT SERVICES

#### 15. Job Posting
- **Model:** Multi-Country Multi-Agency (Regional Specialists)
- **Database Structure:**
  ```
  job_postings:
    - id, title, company, location_country_id
    - visible_to_agencies[] (based on country assignments)
  
  agency_job_assignments:
    - agency_id
    - countries[] (multiple countries)
    - job_categories[] (IT, Healthcare, Construction, etc.)
    - can_post_jobs (boolean)
    - can_recruit (boolean)
  ```
- **Process:**
  1. User posts job in "Bangladesh"
  2. All agencies assigned to Bangladesh see it
  3. Agencies can apply to fulfill
  4. Admin or user selects agency
- **Revenue:** Per successful placement (10-20% of first month salary)

#### 16. Job Application Assistance
- **Model:** Multi-Country Multi-Agency
- **Assignment:** User chooses agency to help apply

#### 17. CV Building
- **Model:** Peer-to-Peer (No Agency)
- **Assignment:** Built-in platform tool
- **Revenue:** Premium templates, professional review service

#### 18. Interview Preparation
- **Model:** Multi-Agency Service
- **Assignment:** Specialized coaches/agencies

---

### 📄 DOCUMENT SERVICES

#### 19. Translation Services
- **Model:** Country-Based Multi-Agency
- **Assignment:** Multiple agencies per language pair
- **Example:**
  - English → Bengali: 10 agencies
  - User uploads document, gets quotes
  - Agencies bid on translation jobs
- **Revenue:** Platform commission 15-20%

#### 20. Document Attestation
- **Model:** Country-Based Multi-Agency
- **Assignment:** Agencies specialize by country requirements
- **Example:**
  - UAE attestation: 3 specialized agencies
  - Saudi attestation: 2 agencies

#### 21. Certificate Verification
- **Model:** Multi-Agency Service
- **Assignment:** Agencies with government connections

#### 22. Notary Services
- **Model:** Location-Based Multi-Agency
- **Assignment:** By city/region, not country

---

### 🏠 LIFESTYLE SERVICES

#### 23. Accommodation Search
- **Model:** Hybrid API + Agency
- **Assignment:** API for listings, agencies for negotiation

#### 24. Relocation Services
- **Model:** Multi-Country Multi-Agency
- **Assignment:** Destination country specialists

#### 25. Airport Pickup
- **Model:** Multi-Agency Service
- **Assignment:** Local transport agencies per city

#### 26. SIM Card / Bank Account Setup
- **Model:** Multi-Agency Service
- **Assignment:** Local agencies per country

---

### 🎫 SPECIAL SERVICES

#### 27. Hajj/Umrah Packages
- **Model:** Multi-Agency Service (Regulated)
- **Assignment:** Government-licensed agencies only
- **Special:** Requires ATAB/TOAB license verification

#### 28. Medical Tourism
- **Model:** Multi-Country Multi-Agency
- **Assignment:** Hospital partnerships + agency coordination

#### 29. Real Estate
- **Model:** Multi-Country Multi-Agency
- **Assignment:** Property agencies by location

#### 30. Legal Consultation
- **Model:** Multi-Country Multi-Agency
- **Assignment:** Licensed lawyers/firms per country

---

## 🏗️ DATABASE ARCHITECTURE

### Core Tables

#### `service_modules`
```sql
id, name, slug
service_type: competitive | exclusive | global_single | hybrid | p2p
assignment_model: multi_agency | exclusive_resource | single_agency
allows_multiple_agencies: boolean
requires_admin_approval: boolean
resource_locking: boolean (for universities, schools)
```

#### `agency_service_assignments`
```sql
id
agency_id
service_module_id
assignment_scope: global | multi_country | country | region | city
assigned_resources: JSON (for countries, cities, universities)
is_primary: boolean (for exclusive resources)
is_active: boolean
approved_by_admin: boolean
platform_commission_rate: decimal
agency_commission_rate: decimal (for revenue split)
priority_level: integer (for ranking in competitive services)
```

#### `service_applications`
```sql
id
user_id
service_module_id
assigned_agency_id (nullable - assigned later for competitive)
assigned_agencies: JSON[] (multiple agencies for competitive quotes)
assignment_type: auto | competitive | admin_assigned
status: submitted | quoted | assigned | processing | completed
quotes: JSON[] (for competitive services)
selected_quote_id: integer
```

#### `agency_resources` (For Exclusive Models)
```sql
id
agency_id
service_module_id
resource_type: university | school | institution | property
resource_id (FK to universities, schools, etc.)
is_primary_owner: boolean
approved_secondary_agencies: JSON[]
added_at
approved_by
```

#### `universities`
```sql
id, name, country_id, city
primary_agency_id (exclusive owner)
allows_secondary_agents: boolean
admin_notes
```

---

## 🎮 WORKFLOW EXAMPLES

### Example 1: Tourist Visa (Competitive)
```
User submits Thailand Tourist Visa
  ↓
System finds all agencies with Thailand + Tourist Visa assignment
  ↓
Creates ServiceApplication with status: quoted
  ↓
Notifies all assigned agencies (5 agencies)
  ↓
Agencies submit quotes within 24 hours
  ↓
User sees 5 quotes, reviews ratings, prices
  ↓
User selects Agency B (best rating + fair price)
  ↓
ServiceApplication assigned to Agency B
  ↓
User pays → Agency processes → Platform earns commission
```

### Example 2: University Application (Exclusive)
```
User wants to apply to "University of Tokyo"
  ↓
System checks: Is this university already in database?
  ↓
IF YES:
  - Check primary_agency_id
  - Route application to that agency exclusively
  - User cannot choose different agency
  ↓
IF NO:
  - User enters university details
  - System asks: "Contact an agency to help?"
  - User selects agency OR admin assigns later
  - Selected agency becomes primary owner
  - Agency adds university details, verifies info
  - Agency processes application
```

### Example 3: Air Tickets (Global Single)
```
User searches "Dhaka → London"
  ↓
Platform API calls FlightHub Agency API
  ↓
Shows real-time flight options
  ↓
User selects flight + pays
  ↓
Platform auto-routes to FlightHub Agency
  ↓
Agency issues ticket via their system
  ↓
Platform earns 8% commission
  ↓
Agency earns 92%
```

### Example 4: Job Posting (Multi-Country Multi-Agency)
```
Company posts job: "Software Engineer in Bangladesh"
  ↓
System finds agencies assigned to:
  - Country: Bangladesh
  - Category: IT/Software
  ↓
5 agencies see the job posting
  ↓
Agencies can:
  - Promote job to their candidate pool
  - Submit candidates
  - Earn commission per placement
  ↓
Company reviews candidates from all agencies
  ↓
Hires candidate submitted by Agency C
  ↓
Agency C earns 15% of first-year salary
  ↓
Platform earns 5% of agency earnings
```

---

## 🚀 IMPLEMENTATION PRIORITY

### Phase 1: Foundation (Week 1-2)
✅ Core architecture (DONE - service_module_id added)
- ✅ Multi-service assignments
- ✅ Scope-based assignments (global, country, visa-specific)
- [ ] Competitive quote system
- [ ] Agency dashboard

### Phase 2: Competitive Services (Week 3-4)
- [ ] Tourist Visa with multi-agency quotes
- [ ] Translation services with bidding
- [ ] Document attestation with quotes
- [ ] User quote comparison UI

### Phase 3: Exclusive Services (Week 5-6)
- [ ] University database + ownership system
- [ ] Primary/secondary agency logic
- [ ] Admin approval workflow
- [ ] Resource locking mechanism

### Phase 4: Global Single Services (Week 7-8)
- [ ] Air ticket API integration
- [ ] Travel insurance API
- [ ] Single-agency routing
- [ ] API fallback to manual

### Phase 5: Multi-Country Services (Week 9-10)
- [ ] Job posting system
- [ ] Multi-country agency assignments
- [ ] Regional specialist matching
- [ ] Recruitment workflow

### Phase 6: Hybrid & P2P Services (Week 11-12)
- [ ] Hotel booking hybrid system
- [ ] CV builder (no agency)
- [ ] Job board (no agency)
- [ ] Hybrid API + agency routing

---

## 📏 BUSINESS RULES

### Commission Structures

| Service Type | Platform Commission | Agency Earnings | Notes |
|-------------|-------------------|----------------|-------|
| Competitive Visa | 15% | 85% | From selected agency |
| Exclusive University | 20% | 80% (70% primary, 10% secondary) | If secondary involved |
| Global Air Ticket | 8% | 92% | Low margin, high volume |
| Job Placement | 5% | 95% | From agency's recruitment fee |
| Translation | 20% | 80% | High platform value-add |
| CV Builder (Premium) | 100% | 0% | Platform direct service |

### Quality Controls

1. **Agency Rating System**
   - User reviews after completion
   - Automatic ranking in competitive services
   - Low-rated agencies deprioritized

2. **Response Time SLA**
   - Competitive quotes: 24 hours to respond
   - Exclusive assignments: 48 hours to acknowledge
   - Global services: Real-time API response

3. **Dispute Resolution**
   - User can escalate to admin
   - Admin can reassign to different agency
   - Refund policy per service type

4. **Resource Verification**
   - Universities must be verified by admin
   - Agencies must prove partnerships
   - False claims = account suspension

---

## 🎨 USER EXPERIENCE

### For Users (Simple & Clear)

**Browsing Services:**
```
Services → Tourist Visa → Select Country (Thailand)
  ↓
"5 agencies handle Thailand Tourist Visa"
  ↓
[Get Quotes] button
  ↓
Submit application → Receive 5 quotes within 24 hours
  ↓
Compare prices, ratings, processing time
  ↓
Select best agency → Pay → Track progress
```

**University Application:**
```
Services → University Application → Select Country (Japan)
  ↓
Search: "University of Tokyo"
  ↓
Found! Handled by: StudyJapan Agency ★★★★★
  ↓
[Apply Now] button → Routed directly to StudyJapan Agency
  ↓
No other choice (exclusive partner)
  ↓
Pay → Agency processes → Updates visible
```

**Air Tickets:**
```
Services → Air Tickets
  ↓
Search flights (Dhaka → London)
  ↓
Real-time results from FlightHub API
  ↓
Select flight → Pay → Instant confirmation
  ↓
E-ticket sent to email
  ↓
(User never sees "agency" - seamless experience)
```

### For Agencies (Clear Territories)

**Dashboard shows:**
- Your assigned services
- Your assigned countries/resources
- Incoming applications
- Competitive quotes pending
- Revenue earned
- Performance metrics

**Agency A (Visa Specialist):**
- Tourist Visa: Thailand, Malaysia, Singapore
- Student Visa: USA, UK, Canada
- Gets ALL applications for these combinations
- Competes with other agencies on quotes

**Agency B (Education Specialist):**
- Owns 50 universities in USA, 30 in UK
- Gets EXCLUSIVE applications for those universities
- Can request access to competitor universities (admin approval)
- No competition on owned universities

**Agency C (Global Services):**
- Air Tickets: Worldwide exclusive
- Travel Insurance: Worldwide exclusive
- High volume, low margin
- Fully automated via API

---

## 🔐 ADMIN CONTROLS

### Assignment Management
- Assign agencies to services
- Approve secondary agency access to exclusive resources
- Override assignments in disputes
- Set custom commission rates per agency
- Monitor agency performance
- Suspend low-performing agencies

### Service Configuration
- Set service type (competitive, exclusive, global, hybrid)
- Define assignment rules per service
- Configure quote timeout periods
- Set minimum number of quotes required
- Enable/disable services
- Add new services with assignment models

### Resource Management (Universities, Schools)
- Verify university authenticity
- Approve primary agency ownership
- Approve secondary agency requests
- Resolve ownership disputes
- Remove fake/duplicate resources

---

## 🌟 MAKING LIFE EASY

### For Bangladeshi Users Going Abroad
✅ **Tourist Visa:** Get best quotes from 5 agencies, pick cheapest/fastest  
✅ **Student Visa:** Trust verified university partners, no scams  
✅ **Air Tickets:** Book directly, best prices, instant confirmation  
✅ **Accommodation:** API + agency help for special needs  
✅ **Translation:** Birth certificate translated in 24 hours  
✅ **Attestation:** Embassy attestation handled professionally  

### For Foreign Users Coming to Bangladesh
✅ **Work Visa:** Specialized agencies handle employer sponsorship  
✅ **Medical Visa:** Hospital partnerships ensure smooth process  
✅ **Business Visa:** Fast-track for investors  
✅ **Relocation:** End-to-end support (house, bank, SIM card)  

### For Agencies (Clear Business Model)
✅ **Competitive Services:** Market your strengths, win on quality  
✅ **Exclusive Partnerships:** Build university relationships, own the market  
✅ **Global Services:** High volume automation, steady income  
✅ **Multi-Country:** Leverage regional expertise  

### For Platform (Sustainable Revenue)
✅ **Commission from every transaction**  
✅ **Premium features for users (CV builder, job alerts)**  
✅ **Premium agency memberships (higher visibility)**  
✅ **API partnerships (air tickets, insurance)**  
✅ **Advertising (universities, employers)**  

---

## 🎯 SUCCESS METRICS

### User Satisfaction
- Average quote response time < 24 hours
- 4+ star rating for 80% of completed services
- 70% users return for second service
- < 5% dispute rate

### Agency Performance
- 90%+ quote submission rate (competitive services)
- 48-hour response time for exclusive services
- 85%+ completion rate
- User rating > 4.0 stars

### Platform Growth
- 50+ services active
- 200+ agency partners
- 10,000+ monthly applications
- $100,000+ monthly GMV (Gross Merchandise Value)
- 15% average platform commission

---

## 🔮 FUTURE ENHANCEMENTS

1. **AI-Powered Agency Matching**
   - ML algorithm suggests best agencies based on user profile
   - Predicts approval chances for visa applications
   - Smart pricing recommendations for agencies

2. **Blockchain Verification**
   - Document authenticity verification
   - Tamper-proof university certificates
   - Smart contracts for escrow payments

3. **Mobile App**
   - Push notifications for quotes
   - Document scanning via camera
   - Real-time application tracking

4. **Agency Marketplace**
   - Agencies can "bid" on high-value applications
   - Dynamic commission rates based on demand
   - Seasonal pricing (peak travel seasons)

5. **White-Label Solutions**
   - Universities can embed application forms
   - Employers can integrate job posting API
   - Travel agencies can use platform API

---

**This architecture makes life easy by:**
✅ Giving users CHOICE (competitive services)  
✅ Ensuring QUALITY (exclusive partnerships)  
✅ Providing CONVENIENCE (global services)  
✅ Building TRUST (verified agencies & resources)  
✅ Fair PRICING (competitive quotes)  
✅ Clear PROCESS (status tracking)  

**World-class = Choice + Quality + Convenience + Trust**
