# VISA DOCUMENT HUB - QUICK START GUIDE

## ✅ What's Been Done

### 1. Database Structure Created
- ✅ `document_categories` table (8 categories)
- ✅ `master_documents` table (42 international standard documents)
- ✅ `country_document_requirements` table (many-to-many assignments)

### 2. International Standard Documents Seeded
- ✅ **Identity Documents** - Passport (ICAO), National ID, Birth Cert, Marriage Cert, Photos
- ✅ **Financial Documents** - Bank Statements, Solvency, Tax Returns, TIN, Sponsorship
- ✅ **Employment Documents** - Employment Letter, Pay Slips, Employee ID, Visiting Card
- ✅ **Business Documents** - Trade License, Registration, MOA, Letterhead, Bank Statements
- ✅ **Educational Documents** - Student ID, School NOC, Parent's Documents
- ✅ **Travel Documents** - Flight Itinerary, Hotel, Insurance, Tour Plan
- ✅ **Supporting Documents** - Cover Letter, Invitation, Previous Visas, Property Docs, Police Clearance
- ✅ **Medical Documents** - Medical Cert, Vaccination, TB Test

### 3. Models Created
- ✅ `DocumentCategory` model with relationships
- ✅ `MasterDocument` model with specifications
- ✅ `CountryDocumentRequirement` model with assignments

### 4. Fee Display Enhanced
- ✅ Embassy Fee now **PRIORITY** (bold, indigo color, thicker border)
- ✅ Total calculation shown in prominent indigo background
- ✅ Service and Processing fees shown as secondary info

## 🎯 Benefits

### Before (Old System)
❌ Manual document entry for each country  
❌ Duplicate documents across countries  
❌ No international standards  
❌ Hard to maintain  
❌ Inconsistent specifications  

### After (Document Hub System)
✅ Define each document once  
✅ Reuse across all countries  
✅ Based on ICAO/ISO/WHO/UN standards  
✅ Easy maintenance (update once, reflects everywhere)  
✅ Professional specifications with translation/notarization requirements  

## 📊 Document Statistics

- **Total Categories:** 8
- **Total Documents:** 42
- **International Standards:** ICAO, ISO, WHO, UN, IATA, Banking, Corporate, Medical
- **Common Documents:** ~80% shared across countries
- **Country-Specific:** ~20% variations handled with notes

## 🔥 How to Assign Documents to Countries

### Example: Malaysia Tourist Visa for Job Holders

```php
use App\Models\MasterDocument;
use App\Models\CountryDocumentRequirement;
use App\Models\Country;

// Get Malaysia
$malaysia = Country::where('name', 'Malaysia')->first();

// Get common documents
$passport = MasterDocument::where('document_name', 'Passport')->first();
$bankStatements = MasterDocument::where('document_name', 'Bank Statements')->first();
$employmentLetter = MasterDocument::where('document_name', 'Employment Letter / NOC')->first();
$photos = MasterDocument::where('document_name', 'Passport-Size Photographs')->first();

// Assign documents
CountryDocumentRequirement::create([
    'country_id' => $malaysia->id,
    'visa_type' => 'tourist',
    'profession_category' => 'Job Holder',
    'document_id' => $passport->id,
    'is_mandatory' => true,
    'specific_notes' => 'Valid for minimum 6 months with 2 blank pages',
    'sort_order' => 1,
]);

CountryDocumentRequirement::create([
    'country_id' => $malaysia->id,
    'visa_type' => 'tourist',
    'profession_category' => 'Job Holder',
    'document_id' => $bankStatements->id,
    'is_mandatory' => true,
    'specific_notes' => 'Last 6 months with minimum balance RM 3,000 equivalent',
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

CountryDocumentRequirement::create([
    'country_id' => $malaysia->id,
    'visa_type' => 'tourist',
    'profession_category' => 'Job Holder',
    'document_id' => $photos->id,
    'is_mandatory' => true,
    'specific_notes' => '2 photos - 35mm x 45mm, white background',
    'sort_order' => 4,
]);
```

## 🌍 Schengen Example (Bulk Assignment)

```php
// Common Schengen requirements
$schengenCountries = ['Germany', 'France', 'Italy', 'Spain', 'Netherlands'];
$commonDocs = [
    'Passport',
    'Passport-Size Photographs',
    'Bank Statements',
    'Travel Insurance',
    'Flight Itinerary',
    'Hotel Reservations',
    'Cover Letter',
];

foreach ($schengenCountries as $countryName) {
    $country = Country::where('name', $countryName)->first();
    
    foreach ($commonDocs as $index => $docName) {
        $document = MasterDocument::where('document_name', $docName)->first();
        
        CountryDocumentRequirement::create([
            'country_id' => $country->id,
            'visa_type' => 'tourist',
            'profession_category' => null, // Common for all professions
            'document_id' => $document->id,
            'is_mandatory' => true,
            'specific_notes' => $docName === 'Travel Insurance' 
                ? 'Minimum coverage: €30,000' 
                : null,
            'sort_order' => $index + 1,
        ]);
    }
}
```

## 📖 Viewing Documents

### Get Documents by Category
```php
use App\Models\DocumentCategory;

$category = DocumentCategory::with('documents')->where('name', 'Identity Documents')->first();

foreach ($category->documents as $doc) {
    echo $doc->document_name . "\n";
    echo "Specs: " . $doc->specifications . "\n";
    echo "Translation Required: " . ($doc->translation_required ? 'Yes' : 'No') . "\n";
    echo "Standard: " . $doc->international_standard . "\n\n";
}
```

### Get Country's Required Documents
```php
use App\Models\Country;

$country = Country::with('documentRequirements.document')->where('name', 'Malaysia')->first();

foreach ($country->documentRequirements as $req) {
    echo $req->document->document_name . "\n";
    echo "Visa Type: " . $req->visa_type . "\n";
    echo "Profession: " . ($req->profession_category ?? 'All') . "\n";
    echo "Mandatory: " . ($req->is_mandatory ? 'Yes' : 'No') . "\n";
    echo "Notes: " . $req->specific_notes . "\n\n";
}
```

## 🔧 Next Steps

### Phase 1: Admin Interface (This Week)
- [ ] Create admin CRUD for Document Categories
- [ ] Create admin CRUD for Master Documents
- [ ] Create document assignment interface (select country, visa type, profession, then assign documents from library)
- [ ] Update Visa Requirements create/edit to use document library instead of textareas

### Phase 2: Display Updates (Next Week)
- [ ] Update Show.vue to display documents from library (grouped by category)
- [ ] Add specifications display (translation required, notarization, validity)
- [ ] Add icons for each category
- [ ] Show international standard badges

### Phase 3: Migration (Week 3)
- [ ] Create seeder to migrate existing Malaysia data to new system
- [ ] Test with 2-3 more countries
- [ ] Gradually migrate all countries
- [ ] Remove old JSON fields after successful migration

### Phase 4: Public Features (Week 4)
- [ ] Public visa requirement checker
- [ ] Interactive document checklist generator
- [ ] PDF download feature
- [ ] Document upload tracking

## 💡 Key Features

### Document Specifications
Each document includes:
- **Name** - Standard name (e.g., "Passport")
- **Description** - What it is
- **Specifications** - Format, size, validity requirements
- **Translation Required** - Boolean flag
- **Notarization Required** - Boolean flag
- **Typical Validity Days** - How long document stays valid
- **International Standard** - ICAO, ISO, WHO, etc.
- **Example URL** - Link to sample (optional)

### Country-Specific Notes
When assigning documents to countries, you can add:
- **Specific Notes** - Country variations (e.g., "Malaysia requires minimum RM 3,000 balance")
- **Is Mandatory** - Required or optional
- **Sort Order** - Display sequence

## 🎨 Fee Display Changes

### Before:
```
Government Fee: ৳9,000 (regular text)
Service Fee: ৳2,000
Processing Fee: ৳1,000
Total: ৳12,000 (indigo text on light background)
```

### After:
```
Embassy Fee: ৳9,000 (BOLD, indigo color, thick border) ← PRIORITY
Service Fee: ৳2,000 (gray text, regular weight)
Processing Fee: ৳1,000 (gray text, regular weight)
Total Amount: ৳12,000 (BOLD white text on indigo background)
```

## 📚 International Standards Reference

- **ICAO Doc 9303** - Machine Readable Travel Documents
- **ISO/IEC 7810** - ID Card Physical Characteristics
- **UN Legal Identity** - Birth/Marriage Certificate Standards
- **WHO IHR** - International Health Regulations (Vaccination)
- **IATA** - International Air Transport Association (Flight Bookings)
- **IBAN/SWIFT** - International Banking Standards
- **Corporate Standard** - Business document best practices
- **Medical Standard** - Healthcare document requirements

## 🚀 Current Status

### ✅ Completed
1. Database tables created and migrated
2. 42 international standard documents seeded
3. 8 document categories created
4. Models with relationships
5. Fee display enhanced (Embassy fee priority)
6. Assets compiled successfully

### 🔄 Ready For
1. Admin interface development
2. Document assignment to countries
3. Migration of existing data
4. Frontend display updates

### 📝 Documentation Created
1. `DOCUMENT_HUB_SYSTEM.md` - Complete system documentation
2. `VISA_DOCUMENT_HUB_QUICK_START.md` - This quick start guide

---

## 🎯 Your Task Now

You now have a **world-class, internationally standardized** visa document management system!

**Immediate action:** Start assigning documents to countries using the examples above.

**No more duplicate entry.** Define once, use everywhere. 🌍
