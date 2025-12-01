# Architectural Audit Report

**Generated:** 2025-12-01 05:35:06
**Models Scanned:** 116
**Database Tables:** 124
**Migrations:** 138

## Summary

| Severity | Count |
|----------|-------|
| 🔴 CRITICAL | 0 |
| 🟠 HIGH | 1 |
| 🟡 MEDIUM | 3 |
| 🟢 LOW | 6 |

## Detailed Findings

### App\Models\Education

- **Table:** `user_educations`
- **File:** `Education.php`

**Issues:**

- [LOW] Table name 'user_educations' doesn't match expected 'education'

### App\Models\FamilyMember

- **Table:** `user_family_members`
- **File:** `FamilyMember.php`

**Issues:**

- [LOW] Table name 'user_family_members' doesn't match expected 'family_members'

### App\Models\SupportTicketReply

- **Table:** `support_ticket_replies`
- **File:** `SupportTicketReply.php`

**Issues:**

- [HIGH] No migration found for table 'support_ticket_replies'

### App\Models\UserEducation

- **Table:** `user_educations`
- **File:** `UserEducation.php`

**Issues:**

- [LOW] Table name 'user_educations' doesn't match expected 'user_education'

### App\Models\UserTravelHistory

- **Table:** `user_travel_history`
- **File:** `UserTravelHistory.php`

**Issues:**

- [LOW] Table name 'user_travel_history' doesn't match expected 'user_travel_histories'

### App\Models\UserVisaHistory

- **Table:** `user_visa_history`
- **File:** `UserVisaHistory.php`

**Issues:**

- [LOW] Table name 'user_visa_history' doesn't match expected 'user_visa_histories'

### App\Models\WorkExperience

- **Table:** `user_work_experiences`
- **File:** `WorkExperience.php`

**Issues:**

- [LOW] Table name 'user_work_experiences' doesn't match expected 'work_experiences'

### ORPHANED TABLE

- **Table:** `blog_post_tag`

**Issues:**

- [MEDIUM] Table 'blog_post_tag' exists but has no corresponding Model

### ORPHANED TABLE

- **Table:** `event_registrations`

**Issues:**

- [MEDIUM] Table 'event_registrations' exists but has no corresponding Model

### ORPHANED TABLE

- **Table:** `user_skill`

**Issues:**

- [MEDIUM] Table 'user_skill' exists but has no corresponding Model

