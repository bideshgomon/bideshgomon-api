# Data Management CSV Files

This directory contains comprehensive CSV files to populate all data management sections of the Bideshgomon platform.

## 📋 Overview

All CSV files are research-based and contain real-world, accurate data ready for bulk import into the system.

---

## 📁 File Structure

### 1. **countries.csv** (60 countries)
**Columns:** `name`, `name_bn`, `iso_code_2`, `iso_code_3`, `phone_code`, `currency_code`, `flag_emoji`, `region`, `is_active`

**Content:**
- Major countries across all continents
- Popular migration destinations (USA, Canada, UK, Australia, Germany, etc.)
- Middle East countries (UAE, Saudi Arabia, Qatar, Kuwait, etc.)
- Asian countries (Bangladesh, India, China, Japan, Singapore, Malaysia)
- European countries (major EU nations)
- ISO codes (2-letter and 3-letter)
- Phone codes with + prefix
- Currency codes (ISO 4217)
- Flag emojis for visual representation
- Bengali translations for all country names

**Use Case:** Country selection in forms, visa applications, job postings

---

### 2. **currencies.csv** (42 currencies)
**Columns:** `code`, `name`, `symbol`, `exchange_rate_to_bdt`, `is_active`

**Content:**
- All major world currencies
- Real currency symbols (৳, $, €, £, ¥, etc.)
- Exchange rates to BDT (Bangladeshi Taka as base)
- Active currencies used in international transactions
- Covers currencies from all listed countries

**Use Case:** Payment processing, service pricing, wallet transactions

---

### 3. **cities.csv** (70+ cities)
**Columns:** `country_id`, `name`, `name_bn`, `state_province`, `timezone`, `latitude`, `longitude`, `is_capital`, `is_active`

**Content:**
- Major cities from all 60 countries
- Capital cities marked with `is_capital=1`
- Multiple cities per country for popular destinations
- Accurate GPS coordinates (latitude/longitude)
- IANA timezone identifiers (Asia/Dhaka, America/New_York, etc.)
- State/province information
- Bengali translations

**Examples:**
- Bangladesh: Dhaka (capital), Chittagong, Sylhet, Khulna, Cox's Bazar
- USA: New York, Los Angeles, Washington DC (capital), Chicago, San Francisco
- UK: London (capital), Manchester, Birmingham, Edinburgh
- UAE: Dubai, Abu Dhabi (capital)

**Use Case:** City selection in profiles, job locations, travel bookings

---

### 4. **airports.csv** (55+ airports)
**Columns:** `city_id`, `name`, `name_bn`, `iata_code`, `icao_code`, `latitude`, `longitude`, `is_international`, `is_active`

**Content:**
- Major international airports worldwide
- IATA codes (3-letter: DAC, JFK, LHR, DXB, etc.)
- ICAO codes (4-letter: VGHS, KJFK, EGLL, OMDB, etc.)
- Accurate GPS coordinates
- International vs domestic classification
- Bengali names for all airports

**Examples:**
- Hazrat Shahjalal International (DAC) - Dhaka
- JFK International (JFK) - New York
- Heathrow (LHR) - London
- Changi (SIN) - Singapore
- Dubai International (DXB) - Dubai

**Use Case:** Flight bookings, travel services, migration tracking

---

### 5. **languages.csv** (35 languages)
**Columns:** `name`, `name_bn`, `code`, `native_name`, `is_active`

**Content:**
- Major world languages
- ISO 639-1 language codes (en, bn, ar, zh, es, fr, etc.)
- Native names in original script (বাংলা, العربية, 中文, 日本語, etc.)
- Languages spoken in migration destination countries
- Languages required for work/study abroad

**Examples:**
- English, Bengali, Arabic, Mandarin Chinese
- Spanish, French, German, Japanese, Korean
- Hindi, Urdu, Russian, Portuguese, Italian

**Use Case:** Language proficiency in user profiles, language test selection

---

### 6. **language_tests.csv** (25+ tests)
**Columns:** `language_id`, `name`, `name_bn`, `code`, `min_score`, `max_score`, `score_type`, `description`, `is_active`

**Content:**
- Official language proficiency tests
- Score ranges (numeric, band, level)
- Multiple tests per language

**Major Tests:**
- **English:** IELTS (0-9 band), TOEFL iBT (0-120), PTE (10-90), Cambridge, Duolingo (10-160)
- **French:** DELF, DALF, TCF
- **German:** TestDaF, Goethe-Zertifikat, DSH
- **Japanese:** JLPT (N5-N1)
- **Korean:** TOPIK (Level 1-6)
- **Chinese:** HSK (Level 1-6)
- **Bengali:** BCST

**Use Case:** Language test score submission, visa requirements, university admissions

---

### 7. **degrees.csv** (41 degrees)
**Columns:** `name`, `name_bn`, `short_name`, `level`, `typical_duration_years`, `is_active`

**Content:**
- Complete educational qualification hierarchy
- Levels 1-9 (SSC to Postdoctoral)
- Duration in years

**Levels:**
- **Level 1:** SSC (10 years)
- **Level 2:** HSC (12 years)
- **Level 3:** Diploma (3 years)
- **Level 4:** Associate Degree (2 years)
- **Level 5:** Bachelor's degrees (BA, BSc, BBA, BEng, MBBS, LLB, etc.)
- **Level 6:** Master's degrees (MA, MSc, MBA, MEng, LLM, etc.)
- **Level 7:** MPhil (2 years)
- **Level 8:** Doctoral degrees (PhD, MD, DBA, EdD)
- **Level 9:** Postdoctoral Research

**Use Case:** User education profiles, university admission requirements, job qualifications

---

### 8. **skill_categories.csv** (15 categories)
**Columns:** `name`, `name_bn`, `slug`, `description`, `order`, `is_active`

**Content:**
- Broad skill classification
- Ordered by relevance

**Categories:**
1. Information Technology
2. Languages
3. Business & Management
4. Healthcare
5. Engineering
6. Design & Creative
7. Finance & Accounting
8. Marketing & Sales
9. Legal
10. Education & Training
11. Hospitality & Tourism
12. Construction & Trades
13. Transportation & Logistics
14. Agriculture
15. Personal Skills

**Use Case:** Skill organization, job matching, user profile categorization

---

### 9. **skills.csv** (100+ skills)
**Columns:** `skill_category_id`, `name`, `name_bn`, `slug`, `description`, `is_active`

**Content:**
- Specific skills under each category
- Technical and soft skills
- Industry-relevant competencies

**Examples by Category:**
- **IT:** Programming, Web Development, Cloud Computing, Cybersecurity, Data Science, DevOps
- **Languages:** English, Arabic, Chinese, French, German, Spanish, Japanese, Korean
- **Business:** Project Management, Leadership, HR, Supply Chain
- **Healthcare:** Nursing, Medical Assistant, Pharmacy, Physical Therapy
- **Engineering:** Mechanical, Electrical, Civil, Chemical, AutoCAD
- **Design:** Graphic Design, Video Editing, Photography, Adobe Photoshop, Illustrator
- **Finance:** Accounting, Financial Analysis, Taxation, QuickBooks, Excel
- **Marketing:** Digital Marketing, SEO, Social Media, Content Marketing
- **Personal:** Communication, Time Management, Problem Solving, Teamwork, Work Ethic

**Use Case:** User skill profiles, job requirements, skill-based matching

---

### 10. **job_categories.csv** (73 categories)
**Columns:** `parent_id`, `name`, `name_bn`, `slug`, `description`, `order`, `is_active`

**Content:**
- Hierarchical job classification (parent-child structure)
- 15 top-level categories, each with subcategories

**Structure:**
```
Information Technology
├── Software Development
├── Web Development
├── Mobile App Development
├── Database Administration
├── Network & Security
└── Data Science & AI

Healthcare
├── Nursing
├── Medical Practitioners
├── Allied Health
└── Pharmacy

Engineering
├── Civil Engineering
├── Mechanical Engineering
├── Electrical Engineering
└── Chemical Engineering

[...and 12 more top-level categories]
```

**Use Case:** Job posting categorization, job search filters, career pathways

---

### 11. **service_categories.csv** (8 categories)
**Columns:** `name`, `slug`, `icon`, `description`, `color`, `sort_order`, `is_active`

**Content:**
- Platform service offerings
- Heroicons icon names
- Tailwind color schemes

**Services:**
1. Visa Services (passport icon, blue)
2. Education Services (academic-cap icon, green)
3. Job Placement (briefcase icon, purple)
4. Travel Services (airplane icon, orange)
5. Immigration Consulting (globe-alt icon, red)
6. Document Services (document-text icon, yellow)
7. Insurance Services (shield-check icon, indigo)
8. Healthcare Services (heart icon, pink)

**Use Case:** Service module organization, homepage service cards, agency service offerings

---

### 12. **blog_categories.csv** (10 categories)
**Columns:** `name`, `slug`, `description`, `icon`, `color`, `order`, `is_active`

**Content:**
- Blog content categorization
- Ordered by importance

**Categories:**
1. Travel & Tourism
2. Immigration News
3. Career Advice
4. Education
5. Visa Guidelines
6. Success Stories
7. Country Guides
8. Life Abroad
9. News & Updates
10. Technology

**Use Case:** Blog post organization, content filtering, navigation menus

---

### 13. **blog_tags.csv** (44 tags)
**Columns:** `name`, `slug`

**Content:**
- Granular content tagging
- SEO-friendly keywords

**Tag Groups:**
- **General:** visa, immigration, travel, education, job, career, abroad
- **Processes:** application, requirements, documentation, interview
- **Countries:** australia, canada, usa, uk, germany, dubai, singapore
- **Education:** scholarship, university, admission, language-test, ielts, toefl
- **Visa Types:** student-visa, work-permit, business-visa, permanent-residence

**Use Case:** Blog post tagging, related content, search optimization

---

## 🚀 Usage Instructions

### Option 1: Manual Bulk Upload via Admin Dashboard

1. Login as admin
2. Navigate to Data Management section
3. Select the data type (Countries, Cities, Languages, etc.)
4. Click "Bulk Upload" button
5. Choose the corresponding CSV file
6. Review and confirm import

### Option 2: Direct Database Seeding (Recommended)

We've created a comprehensive seeder class `DataManagementSeeder` that automatically reads all CSV files and populates your database.

**Run the seeder:**
```bash
php artisan db:seed --class=DataManagementSeeder
```

This will seed all data in the correct order:
1. ✓ Countries (60 records)
2. ✓ Currencies (42 records)
3. ✓ Cities (70+ records with foreign keys to countries)
4. ✓ Airports (55+ records with foreign keys to cities)
5. ✓ Languages (35 records)
6. ✓ Language Tests (25+ records with foreign keys to languages)
7. ✓ Degrees (40 records)
8. ✓ Skill Categories (15 records)
9. ✓ Skills (100+ records with foreign keys to skill_categories)
10. ✓ Job Categories (73 records with hierarchical parent-child)
11. ✓ Service Categories (8 records)
12. ✓ Blog Categories (10 records)
13. ✓ Blog Tags (44 records)

**Features:**
- ✅ Handles foreign key dependencies automatically
- ✅ Proper data type conversions (int, float, bool)
- ✅ NULL value handling
- ✅ Hierarchical data support (job categories parent-child)
- ✅ Progress feedback with record counts
- ✅ Error handling for missing files

**Fresh Install:**
```bash
# Reset database and seed all data
php artisan migrate:fresh --seed --seeder=DataManagementSeeder
```

---

## 📊 Data Statistics

| File | Records | Purpose |
|------|---------|---------|
| countries.csv | 60 | Global coverage of migration destinations |
| currencies.csv | 42 | All major currencies with exchange rates |
| cities.csv | 70+ | Major cities from all countries |
| airports.csv | 55+ | International airports with IATA/ICAO codes |
| languages.csv | 35 | World languages for communication |
| language_tests.csv | 25+ | Official language proficiency tests |
| degrees.csv | 41 | Complete education qualification hierarchy |
| skill_categories.csv | 15 | Broad skill classification |
| skills.csv | 100+ | Specific professional skills |
| job_categories.csv | 73 | Hierarchical job classification |
| service_categories.csv | 8 | Platform service offerings |
| blog_categories.csv | 10 | Blog content categories |
| blog_tags.csv | 44 | SEO-optimized content tags |

**Total Records:** 500+ comprehensive, production-ready data entries

---

## 🔍 Data Quality Features

✅ **Bengali Translations:** All names translated to Bengali (বাংলা)  
✅ **Real Data:** Based on ISO standards, official sources, and industry standards  
✅ **GPS Coordinates:** Accurate latitude/longitude for cities and airports  
✅ **Code Standards:** ISO 3166 (countries), ISO 639 (languages), ISO 4217 (currencies), IATA/ICAO (airports)  
✅ **Hierarchical Structure:** Parent-child relationships for job categories  
✅ **Relational Integrity:** Proper foreign key references (country_id, city_id, skill_category_id, etc.)  
✅ **Active Status:** All records marked as active and ready to use  
✅ **SEO-Friendly:** URL-safe slugs for all categorical data  

---

## 🛠️ Maintenance

### Adding New Records
- Follow the same column structure
- Maintain consistent formatting
- Add Bengali translations
- Ensure foreign key relationships are valid
- Use proper ISO codes where applicable

### Updating Exchange Rates
Update `currencies.csv` with current rates relative to BDT:
```csv
USD,US Dollar,$,110.500000,1  # Update this value periodically
```

### Data Validation
Before importing:
1. Check for duplicate entries (especially ISO codes, IATA codes)
2. Verify foreign key relationships exist
3. Ensure all required fields are populated
4. Validate date formats and numeric values

---

## 📝 Notes

- All CSV files use UTF-8 encoding to support Bengali and special characters
- Empty values represented by empty string, not NULL keyword
- Boolean fields: 1 = true/active, 0 = false/inactive
- Decimal precision maintained for coordinates (8 places) and exchange rates (6 places)
- Parent-child relationships in job_categories use empty string for root categories (not "null")

---

## 🎯 Production Ready

These CSV files are:
- ✅ Research-based and accurate
- ✅ Production-ready for immediate use
- ✅ Comprehensive coverage of all data sections
- ✅ Bilingual (English + Bengali)
- ✅ Following international standards
- ✅ Structured for relational database import

Import these files to have a fully populated data management system ready for your users!

---

**Last Updated:** November 24, 2025  
**Created By:** GitHub Copilot  
**Data Sources:** ISO standards, official airline/airport databases, language testing organizations, industry classifications
