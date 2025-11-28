# Document Hub System - Usage Guide

## Overview
The Document Hub System is a centralized document management system for visa requirements that follows international standards (ICAO, ISO, WHO, UN). It replaces the old text-based document system with a structured, reusable approach.

---

## System Components

### 1. **Document Categories** (8 categories)
- Identity Documents
- Financial Documents
- Employment Documents
- Business Documents
- Educational Documents
- Travel Documents
- Supporting Documents
- Medical Documents

### 2. **Master Documents** (36 international standard documents)
Each document includes:
- Document name
- Category
- Description and specifications
- International standard reference (ICAO, ISO, WHO, etc.)
- Translation required (Yes/No)
- Notarization required (Yes/No)
- Typical validity period
- Example/reference URL

### 3. **Country Assignments**
Assign documents to countries by:
- Visa type (tourist, business, student, work, medical, transit, family)
- Profession category (Job Holder, Business Person, Student)
- Mandatory/Optional status
- Country-specific notes

---

## How to Use - Step by Step

### **STEP 1: Access the Admin Dashboard**
1. Go to: `http://127.0.0.1:8000/admin/dashboard`
2. Look for the **"Document Hub System"** section at the top (highlighted in indigo)

### **STEP 2: Manage Master Documents**

#### View Documents Library
1. Click **"Master Documents"** from dashboard
2. URL: `http://127.0.0.1:8000/admin/master-documents`
3. You'll see:
   - Search by name
   - Filter by category
   - List of 36 documents with categories, standards, flags
   - Pagination (fixed for null href issue)

#### Create New Document
1. Click **"Create Document"** button
2. URL: `http://127.0.0.1:8000/admin/master-documents/create`
3. Fill in the form:
   - **Document Name**: e.g., "Employment Contract"
   - **Category**: Select from dropdown (Identity, Financial, etc.)
   - **Description**: Brief overview
   - **Specifications**: Detailed requirements (format, size, content)
   - **International Standard**: e.g., "ILO Convention", "ISO 9001"
   - **Translation Required**: Check if needed
   - **Notarization Required**: Check if needed
   - **Typical Validity Days**: e.g., 90, 180, 365
   - **Example URL**: Link to sample or guidelines
   - **Sort Order**: Display order (0-999)
   - **Active**: Check to enable
4. Click **"Create Document"**

#### View Document Details
1. Click on any document name or "View" button
2. URL: `http://127.0.0.1:8000/admin/master-documents/21` (example)
3. See:
   - Full description and specifications
   - International standard badge
   - Requirements (translation, notarization)
   - Validity period
   - List of countries using this document
   - Reference link

#### Edit Document
1. From document details, click **"Edit Document"**
2. Or from list, click pencil icon
3. Update any field
4. Click **"Update Document"**
5. **Delete option** available at bottom

### **STEP 3: Manage Document Categories**

1. Click **"Document Categories"** from dashboard
2. URL: `http://127.0.0.1:8000/admin/document-categories`
3. View 8 categories with document counts
4. Edit category names, descriptions, sort order

### **STEP 4: Assign Documents to Countries**

#### View Country Grid
1. Click **"Country Assignments"** from dashboard
2. URL: `http://127.0.0.1:8000/admin/document-assignments`
3. See grid of countries with document counts

#### Assign Documents to a Country
1. Click on a country (e.g., Malaysia)
2. URL: `http://127.0.0.1:8000/admin/document-assignments/13` (Malaysia example)
3. You'll see:
   - **Tabs** for visa types (Tourist, Business, Student, Work, Medical, Transit, Family)
   - **Sections** for profession categories:
     - Common Documents (all applicants)
     - Job Holder specific
     - Business Person specific
     - Student specific

#### Bulk Assign Documents
1. Click **"Assign Documents"** button (top right)
2. Modal opens with:
   - **Select Visa Type**: Choose from dropdown
   - **Select Profession**: Choose from dropdown
   - **Documents grouped by category**
3. Check documents to assign
4. Documents are organized by:
   - Identity Documents
   - Financial Documents
   - Employment Documents
   - Business Documents
   - Educational Documents
   - Travel Documents
   - Supporting Documents
   - Medical Documents
5. Click **"Assign Selected Documents"**
6. Documents appear in the appropriate section with country-specific notes

#### Remove Document Assignment
1. From country assignment page
2. Click **"Remove"** button next to any document
3. Confirm deletion

---

## Example Use Cases

### **Use Case 1: Adding Malaysia Tourist Visa Requirements**

Already done as example! Check: `http://127.0.0.1:8000/admin/document-assignments/13`

**Documents assigned:**
- **Common (all applicants)**: 6 documents
  - Valid Passport
  - Passport Photos
  - Bank Statements
  - Cover Letter
  - Flight Booking
  - Hotel Booking

- **Job Holder**: 4 documents
  - Employment Letter
  - Pay Slips
  - Employee ID Card
  - TIN Certificate

- **Business Person**: 4 documents
  - Trade License
  - Company Registration
  - Business Bank Statements
  - Tax Returns

- **Student**: 5 documents
  - Student ID Card
  - School NOC
  - Parent's Bank Statements
  - Parent's Employment Letter
  - Birth Certificate

### **Use Case 2: Adding Schengen Tourist Visa**

1. Go to Document Assignments
2. Click on any Schengen country (Germany, France, Italy, Spain)
3. Select **Tourist** visa type
4. Click **"Assign Documents"**
5. Select **Common** profession
6. Check documents:
   - ☑ Valid Passport
   - ☑ Passport Photos
   - ☑ Bank Statements (last 6 months)
   - ☑ Cover Letter
   - ☑ Flight Booking
   - ☑ Hotel Booking
   - ☑ Travel Insurance (Schengen compliant)
7. Click **"Assign Selected Documents"**

### **Use Case 3: Adding USA Business Visa (B1)**

1. Find USA in country list
2. Select **Business** visa type
3. For **Common**:
   - Valid Passport
   - Passport Photos
   - DS-160 Confirmation
   - Visa Fee Receipt
4. For **Business Person**:
   - Invitation Letter from US Company
   - Company Registration
   - Tax Returns (last 3 years)
   - Business Bank Statements

---

## Advantages Over Old System

### **Old System (Legacy)**
- ❌ Text-based (manual typing)
- ❌ No standardization
- ❌ Duplicate entry for similar requirements
- ❌ Hard to update globally
- ❌ No international standards reference
- ❌ Country-specific but not reusable

### **New Document Hub System**
- ✅ **Centralized library** of 36 documents
- ✅ **International standards** (ICAO, ISO, WHO, UN)
- ✅ **Reusable** across countries
- ✅ **Category-based** organization
- ✅ **Standardized specifications**
- ✅ **Easy global updates** (change once, applies everywhere)
- ✅ **Professional** and **consistent**
- ✅ **Scalable** to 100+ countries
- ✅ **Many-to-many** relationship flexibility

---

## Quick Reference - URLs

| Feature | URL |
|---------|-----|
| Admin Dashboard | `http://127.0.0.1:8000/admin/dashboard` |
| Master Documents List | `http://127.0.0.1:8000/admin/master-documents` |
| Create Document | `http://127.0.0.1:8000/admin/master-documents/create` |
| View Document | `http://127.0.0.1:8000/admin/master-documents/{id}` |
| Edit Document | `http://127.0.0.1:8000/admin/master-documents/{id}/edit` |
| Document Categories | `http://127.0.0.1:8000/admin/document-categories` |
| Country Assignments Grid | `http://127.0.0.1:8000/admin/document-assignments` |
| Country Assignment Details | `http://127.0.0.1:8000/admin/document-assignments/{countryId}` |
| Malaysia Example | `http://127.0.0.1:8000/admin/document-assignments/13` |
| Legacy Visa Requirements | `http://127.0.0.1:8000/admin/visa-requirements` |

---

## Migration Strategy

### **For New Countries:**
Use Document Hub System exclusively. Assign documents from the master library.

### **For Existing Countries (Legacy System):**
1. Keep old visa requirements as-is for now
2. Gradually migrate to Document Hub
3. Steps:
   - Create country assignment
   - Assign documents from master library
   - Add country-specific notes
   - Test and verify
   - Switch over
   - Archive old requirement

---

## Tips & Best Practices

1. **Use Existing Documents First**
   - Check if document already exists in master library
   - Don't create duplicates

2. **Follow International Standards**
   - Reference ICAO for passports
   - Reference ISO for ID cards
   - Reference WHO for health documents
   - Reference UN for legal documents

3. **Be Specific with Notes**
   - Add country-specific requirements in notes field
   - Example: "Malaysia requires bank balance of minimum RM 10,000"

4. **Organize by Profession**
   - Common documents for all applicants
   - Profession-specific for Job Holder, Business Person, Student

5. **Keep Specifications Updated**
   - Review specifications regularly
   - Update once in master library
   - Changes reflect everywhere

6. **Use Sort Order**
   - Control display order (0-999)
   - Most important documents first

7. **Active/Inactive Status**
   - Deactivate deprecated documents
   - Don't delete (preserve history)

---

## Troubleshooting

### **Issue: Pagination Error "Cannot read properties of null"**
**Fixed!** The null href issue has been resolved. Pagination now uses conditional rendering.

### **Issue: Document Not Showing in Assignment**
- Check if document is **Active**
- Check if document is in correct **Category**
- Refresh page

### **Issue: Can't Find Country**
- Countries come from `countries` table
- Must have `is_active = true`
- Check database: `SELECT * FROM countries WHERE is_active = 1`

### **Issue: Duplicate Documents**
- Search first before creating
- Use filters by category
- Merge duplicates if needed

---

## Next Steps

1. **Add More Countries**
   - Schengen countries (Germany, France, Italy, Spain, Netherlands, etc.)
   - USA, UK, Canada, Australia, New Zealand
   - Middle East (UAE, Saudi Arabia, Qatar)
   - Asian countries (Thailand, Singapore, Japan, South Korea)

2. **Enhance Features**
   - Document upload capability
   - PDF export for requirements
   - Checklist generation for applicants
   - Document verification workflow

3. **API Integration**
   - Frontend display of requirements
   - User document submission
   - Automated checklist

---

## Support

For questions or issues:
- Check this guide first
- Review code in `app/Http/Controllers/Admin/` directory
- Check models in `app/Models/` directory
- Review Vue pages in `resources/js/Pages/Admin/` directory

**Database Tables:**
- `document_categories` - 8 categories
- `master_documents` - 36 documents
- `country_document_requirements` - Assignments (many-to-many pivot)

**Controllers:**
- `DocumentCategoryController.php`
- `MasterDocumentController.php`
- `CountryDocumentAssignmentController.php`

**Frontend Pages:**
- `Admin/MasterDocuments/Index.vue`
- `Admin/MasterDocuments/Create.vue`
- `Admin/MasterDocuments/Show.vue`
- `Admin/MasterDocuments/Edit.vue`
- `Admin/DocumentCategories/Index.vue`
- `Admin/DocumentAssignments/Index.vue`
- `Admin/DocumentAssignments/Show.vue`

---

**Last Updated:** November 26, 2025
**Version:** 1.0
**Status:** ✅ Production Ready
