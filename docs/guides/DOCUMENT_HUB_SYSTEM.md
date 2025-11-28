# Document Hub System - International Visa Requirements

## Overview
The Document Hub System is a centralized, standardized approach to managing visa document requirements based on international standards from:
- **Schengen Visa Requirements** (EU)
- **US Department of State** Standards
- **UK Home Office** Requirements
- **Australian Department of Home Affairs**
- **Canadian Immigration** Standards

## System Architecture

### Database Tables

#### 1. `document_categories`
Organizes documents into logical categories.

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| name | varchar(100) | Category name |
| description | text | Category description |
| sort_order | integer | Display order |
| is_active | boolean | Active status |

**Categories:**
1. Identity Documents
2. Financial Documents
3. Employment Documents
4. Business Documents
5. Educational Documents
6. Travel Documents
7. Supporting Documents
8. Medical Documents

#### 2. `master_documents`
Central library of all visa documents with specifications.

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| category_id | bigint | Foreign key to document_categories |
| document_name | varchar | Document name |
| description | text | Document description |
| specifications | text | Format, size, validity requirements |
| translation_required | boolean | Whether translation needed |
| notarization_required | boolean | Whether notarization needed |
| typical_validity_days | integer | How long document stays valid |
| international_standard | varchar(50) | ISO, ICAO, WHO, etc. |
| example_url | varchar(500) | Link to document example |
| sort_order | integer | Display order |
| is_active | boolean | Active status |

**Total Documents:** 42 international standard documents

#### 3. `country_document_requirements`
Many-to-many relationship: assigns documents to countries.

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| country_id | bigint | Foreign key to countries |
| visa_type | varchar(50) | tourist, business, student, work, medical, transit, family |
| profession_category | varchar(50) | Job Holder, Business Person, Student |
| document_id | bigint | Foreign key to master_documents |
| is_mandatory | boolean | Required or optional |
| specific_notes | text | Country-specific variations |
| sort_order | integer | Display order |

## Document Categories & Standards

### 1. Identity Documents (ICAO Standards)
- **Passport** - ICAO Doc 9303 (6+ months validity, 2 blank pages)
- **National ID Card** - ISO/IEC 7810
- **Birth Certificate** - UN Legal Identity
- **Marriage Certificate** - UN Legal Identity
- **Passport-Size Photographs** - ICAO Photo Standards (35mm x 45mm)

### 2. Financial Documents (Banking Standards)
- **Bank Statements** - IBAN/SWIFT (3-6 months, stamped)
- **Bank Solvency Certificate** - Banking Standard
- **Tax Returns (ITR)** - Tax Authority Standard (2-3 years)
- **TIN Certificate** - Tax Authority Standard
- **Sponsorship Letter** - Notarized Affidavit

### 3. Employment Documents
- **Employment Letter / NOC** - Corporate Standard
- **Pay Slips** - Corporate Standard (3-6 months)
- **Employee ID Card** - Corporate Standard
- **Visiting Card** - Corporate Standard

### 4. Business Documents
- **Trade License** - Business Registration (annual renewal)
- **Company Registration Certificate** - Corporate Law
- **Memorandum of Association** - Corporate Law
- **Company Letterhead** - Corporate Standard
- **Business Bank Statements** - Banking Standard (6 months)

### 5. Educational Documents
- **Student ID Card** - Academic Standard
- **School/University NOC** - Academic Standard
- **Parent's Bank Statements** - Banking Standard
- **Parent's Employment Letter** - Corporate Standard
- **Parent's Marriage Certificate** - UN Legal Identity

### 6. Travel Documents
- **Flight Itinerary** - IATA Standard
- **Hotel Reservations** - Hospitality Standard
- **Travel Insurance** - Insurance Standard (€30,000 Schengen, $50,000 US)
- **Tour Itinerary** - Tourism Standard

### 7. Supporting Documents
- **Cover Letter** - Visa Application Standard
- **Invitation Letter** - Notarized Affidavit
- **Previous Visas** - Visa History
- **Property Documents** - Property Law
- **Police Clearance Certificate** - Law Enforcement Standard (6 months validity)

### 8. Medical Documents
- **Medical Certificate** - Medical Standard (3 months validity)
- **Vaccination Certificate** - WHO IHR (Yellow Card)
- **TB Test Certificate** - WHO TB Guidelines (6 months validity)

## Benefits of Document Hub System

### 1. **No Duplication**
- Each document defined once
- Reused across all countries
- Update specifications in one place

### 2. **International Standards**
- Follows ICAO, ISO, WHO, UN standards
- Professional and globally recognized
- Consistent with major embassy requirements

### 3. **Scalability**
- Add new countries quickly by assigning existing documents
- No need to re-enter common documents
- Easy to maintain and update

### 4. **Flexibility**
- Mark documents as mandatory or optional per country
- Add country-specific notes for variations
- Support multiple visa types and professions

### 5. **User Experience**
- Clear document specifications
- Translation/notarization requirements upfront
- Validity period information
- Links to document examples

## Usage Examples

### Example 1: Malaysia Tourist Visa for Job Holder
```php
CountryDocumentRequirement::create([
    'country_id' => $malaysia->id,
    'visa_type' => 'tourist',
    'profession_category' => 'Job Holder',
    'document_id' => $passport->id,
    'is_mandatory' => true,
    'sort_order' => 1,
]);

CountryDocumentRequirement::create([
    'country_id' => $malaysia->id,
    'visa_type' => 'tourist',
    'profession_category' => 'Job Holder',
    'document_id' => $bankStatements->id,
    'is_mandatory' => true,
    'specific_notes' => 'Last 6 months with minimum balance of RM 3,000',
    'sort_order' => 2,
]);

CountryDocumentRequirement::create([
    'country_id' => $malaysia->id,
    'visa_type' => 'tourist',
    'profession_category' => 'Job Holder',
    'document_id' => $employmentLetter->id,
    'is_mandatory' => true,
    'specific_notes' => 'Must include leave approval and return guarantee',
    'sort_order' => 3,
]);
```

### Example 2: Schengen Tourist Visa
All Schengen countries can share same base requirements:
```php
$schengenCountries = ['Germany', 'France', 'Italy', 'Spain', 'Netherlands'];
$commonDocs = [$passport, $travelInsurance, $hotelReservation, $flightItinerary, $bankStatements];

foreach ($schengenCountries as $countryName) {
    $country = Country::where('name', $countryName)->first();
    foreach ($commonDocs as $index => $doc) {
        CountryDocumentRequirement::create([
            'country_id' => $country->id,
            'visa_type' => 'tourist',
            'profession_category' => null, // Common for all
            'document_id' => $doc->id,
            'is_mandatory' => true,
            'sort_order' => $index + 1,
        ]);
    }
}
```

## Next Steps

### Phase 1: Admin Interface (Immediate)
- [ ] Create admin page to manage document categories
- [ ] Create admin page to manage master documents
- [ ] Create document assignment interface for countries
- [ ] Update visa requirements create/edit forms to use document library

### Phase 2: Frontend Display (Week 1)
- [ ] Update visa requirement show page to display from document library
- [ ] Group documents by category
- [ ] Show specifications, translations, notarization requirements
- [ ] Add document validity information

### Phase 3: Public Interface (Week 2)
- [ ] Create public visa requirement checker
- [ ] Interactive document checklist generator
- [ ] Download PDF checklist feature
- [ ] Document upload tracking system

### Phase 4: Analytics & Optimization (Week 3)
- [ ] Track most commonly required documents
- [ ] Analytics on document usage by country
- [ ] User feedback on document clarity
- [ ] Continuous improvement based on embassy updates

## Embassy Fee Priority

The fee structure now prioritizes Embassy/Government fees:

### Updated Fee Display:
```vue
<div class="flex justify-between py-2 border-b-2 border-indigo-200">
    <span class="text-gray-700 font-medium">Embassy Fee:</span>
    <span class="font-bold text-indigo-600">৳{{ government_fee.toLocaleString() }}</span>
</div>
<div class="flex justify-between py-2 border-b border-gray-200">
    <span class="text-gray-600">Service Fee:</span>
    <span class="font-semibold text-gray-900">৳{{ service_fee.toLocaleString() }}</span>
</div>
<div class="flex justify-between py-3 bg-indigo-600 text-white px-3 rounded">
    <span class="font-bold">Total Amount:</span>
    <span class="font-bold text-lg">৳{{ total.toLocaleString() }}</span>
</div>
```

**Visual Hierarchy:**
1. Embassy Fee - Bold indigo color, thicker border (most important)
2. Service Fee - Regular weight, gray text
3. Processing Fee - Regular weight, gray text
4. Total - Bold white text on indigo background

## Migration Plan

### Current System → Document Hub System

**Step 1: Data Migration**
```php
// Extract existing documents from JSON fields
$requirements = VisaRequirement::all();
foreach ($requirements as $req) {
    $docs = json_decode($req->required_documents);
    foreach ($docs as $doc) {
        // Find or create in master_documents
        $masterDoc = MasterDocument::firstOrCreate(['document_name' => $doc]);
        // Create assignment
        CountryDocumentRequirement::create([...]);
    }
}
```

**Step 2: Parallel Running**
- Keep old JSON fields temporarily
- Populate new document hub system
- Test with sample countries

**Step 3: Gradual Rollout**
- Start with new countries using document hub
- Migrate existing countries one by one
- Remove JSON fields after full migration

## Maintenance

### Regular Updates Needed:
1. **Quarterly Review** - Check embassy websites for requirement changes
2. **Document Specifications** - Update formats, validity periods
3. **International Standards** - Track changes in ICAO, WHO, ISO standards
4. **Translation Requirements** - Update based on embassy policies
5. **Fee Updates** - Keep embassy fees current

### Version Control:
Consider adding version tracking to master_documents:
- Track when specifications change
- Notify users of requirement updates
- Historical record of document evolution

## Conclusion

The Document Hub System transforms visa requirement management from manual, country-by-country data entry to a centralized, standards-based approach. This ensures:

✅ **Consistency** - Same documents defined uniformly
✅ **Accuracy** - Based on international standards
✅ **Efficiency** - No duplicate data entry
✅ **Scalability** - Easy to add new countries
✅ **Maintainability** - Update once, reflect everywhere
✅ **Professional** - Industry-standard approach

This is now the international standard for managing visa documentation requirements.
