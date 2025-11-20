# Mobile Testing Guide - bideshgomon-saas

## Server Information
- **Development Server**: http://localhost:5174/
- **Laravel App**: http://localhost:8000
- **Status**: ✅ Running (Vite v7.2.2)

## Testing URLs

### Profile Pages
1. **Profile Edit** (Forms): http://localhost:8000/profile/edit
2. **Profile Show** (View): http://localhost:8000/profile

### Individual Sections to Test
All sections are within the Profile Edit page at `/profile/edit`:
- Basic Information Form
- Password Update Form
- Phone Numbers Section
- Family Members Section
- Financial Information Section
- Security & Background Section
- Education Section
- Work Experience Section
- Skills Section
- Languages Section
- Travel History Section

---

## Device Testing Checklist

### 📱 Mobile Viewports

#### iPhone SE (375px) - Smallest Modern Viewport
**Chrome DevTools Setup**:
1. Open http://localhost:8000/profile/edit
2. Press F12 → Toggle Device Toolbar (Ctrl+Shift+M)
3. Select "iPhone SE" or set dimensions: 375 × 667
4. Refresh page

**Test Checklist**:
- [ ] Header fits properly without horizontal scroll
- [ ] Profile completion card displays correctly
- [ ] All input fields have 16px font size (no zoom on tap)
- [ ] Buttons are full-width on mobile
- [ ] Touch targets ≥ 44px (easily tappable)
- [ ] Cards don't overflow viewport width
- [ ] Text is readable without zooming
- [ ] Modal forms fit in viewport
- [ ] Gradient headers display properly
- [ ] Icons render at correct size

**Critical Tests**:
```
✓ Tap on any input field → Should NOT auto-zoom
✓ Tap on buttons → Should feel responsive (44px height)
✓ Open modal → Should fit within screen
✓ Fill form → Inputs should be easy to tap
✓ Toggle password visibility → Icon should be tappable
```

---

#### iPhone 12/13 (390px) - Standard iOS
**Setup**: Chrome DevTools → "iPhone 12 Pro" (390 × 844)

**Test Checklist**:
- [ ] Same as iPhone SE plus:
- [ ] Better spacing visibility
- [ ] Multi-column grids start appearing (sm: breakpoint)
- [ ] Password strength indicator displays well
- [ ] Phone number cards fit nicely
- [ ] Family member cards have proper spacing

---

#### iPhone 14 Pro Max (414px) - Large iOS
**Setup**: Chrome DevTools → "iPhone 14 Pro Max" (414 × 896)

**Test Checklist**:
- [ ] Grid layouts utilize extra width
- [ ] Two-column forms display properly (if sm: is reached)
- [ ] All touch targets still ≥ 44px
- [ ] No excessive white space
- [ ] Cards scale appropriately

---

### 📱 Tablet Viewports

#### iPad Mini (768px) - Tablet Portrait
**Setup**: Chrome DevTools → "iPad Mini" (768 × 1024)

**Test Checklist**:
- [ ] Two-column layouts activate (sm: and md: breakpoints)
- [ ] Cards use horizontal space efficiently
- [ ] Form fields are not too wide
- [ ] Navigation fits comfortably
- [ ] Touch targets remain ≥ 44px
- [ ] Profile show page displays nicely

---

### 💻 Desktop Viewports

#### Desktop (1024px+)
**Setup**: Chrome DevTools → Responsive mode → 1024 × 768

**Test Checklist**:
- [ ] Three-column grids appear where designed
- [ ] Max-width container (max-w-7xl) centers content
- [ ] Buttons return to auto-width
- [ ] Hover states work properly
- [ ] Forms are not too wide (comfortable reading)
- [ ] Profile show page uses columns efficiently

---

## Functional Testing

### Form Submissions
**Test Each Form**:

#### 1. Update Profile Information Form
```
URL: /profile/edit
Location: Top section

Tests:
✓ Enter first, middle, last name
✓ Check auto-generated passport name updates
✓ All inputs have 16px font
✓ Verify email field works
✓ Click Save → Check success message
✓ Reload page → Verify data persisted
```

#### 2. Update Password Form
```
URL: /profile/edit
Location: Password section

Tests:
✓ Enter current password
✓ Click eye icon → Password shows/hides
✓ Enter new password
✓ Watch strength indicator update (Weak → Fair → Good → Strong)
✓ Confirm password matches
✓ Submit → Verify success
✓ Try logging in with new password
```

#### 3. Phone Numbers Section
```
URL: /profile/edit
Location: Phone Numbers section

Tests:
✓ Click "Add Phone Number" button (44px height)
✓ Modal opens and fits viewport
✓ Select country code from dropdown
✓ Enter phone number (16px font)
✓ Set as primary/verified
✓ Save → Phone appears in list
✓ Click phone number → Should open dialer on mobile (tel: link)
✓ Edit phone → Modal prefills data
✓ Delete phone → Confirmation modal works
```

#### 4. Family Section
```
URL: /profile/edit
Location: Family Members section

Tests:
✓ Click "Add Family Member"
✓ Fill all fields (16px inputs)
✓ Select relationship
✓ Add date of birth → Age calculates automatically
✓ Check "Will Accompany" → Badge shows
✓ Save → Member card appears with pink gradient
✓ Click email → Opens mail client
✓ Click phone → Opens dialer
✓ Edit member → Data prefills
✓ Delete → Confirmation works
```

#### 5. Financial Section
```
URL: /profile/edit
Location: Financial Information section

Tests:
✓ Enter employment details
✓ Enter bank account → Check masking (****1234)
✓ Toggle "Show Bank Details" → Data reveals/hides
✓ Enter income → Check amount masking (৳ ****)
✓ Toggle "Show Income" → Works properly
✓ Add property details
✓ Add vehicle details
✓ Check net worth calculation displays
✓ Save → All data persists
✓ Reload → Masking still works
```

#### 6. Security Section
```
URL: /profile/edit
Location: Security & Background section

Tests:
✓ Click Edit Security Information
✓ Modal opens (red/orange gradient)
✓ Check each security toggle:
  - Criminal Record (5×5 checkbox)
  - Police Clearance (with date fields)
  - Court Cases
  - Immigration Violations
  - Visa Refusal
  - Travel Ban
  - Military Service
  - Watchlist Status
✓ Toggle checkbox → Detail fields appear/hide
✓ Fill conditional fields (16px inputs)
✓ Read blue warning banner
✓ Save → Status card updates
✓ Green shield if clean, red if issues
```

---

## Visual Testing

### Gradient Headers
**Check on each section**:
- [ ] Pink/Rose - Family Section
- [ ] Green/Emerald - Financial Section
- [ ] Sky/Blue - Phone Numbers Section
- [ ] Red/Orange - Security Section
- [ ] Purple/Indigo - Education Section
- [ ] Indigo/Purple - Profile Header (Show page)

**Verify**:
- 1px height gradient stripe at top of card
- Colors transition smoothly
- Visible on all devices

### Icons
**Test icon rendering**:
- [ ] HeroIcons display properly
- [ ] Icons are 5×5 (w-5 h-5) or 6×6 (w-6 h-6)
- [ ] Password eye icons toggle correctly
- [ ] Shield icons show in security section
- [ ] Phone, email, calendar icons visible

### Empty States
**Test when no data exists**:
- [ ] Centered message with emoji
- [ ] Gray background (bg-gray-50)
- [ ] Friendly message text
- [ ] Proper spacing (py-8)

**Check on**:
- Education section (if no education added)
- Work experience (if no jobs added)
- Family members (if no family added)
- Skills (if no skills added)

---

## Touch Target Verification

### Minimum Size Test (44px)
**Use browser inspector to measure**:

1. **Buttons**:
   - [ ] Save buttons
   - [ ] Edit buttons
   - [ ] Add buttons
   - [ ] Delete buttons
   - [ ] Cancel buttons

2. **Interactive Elements**:
   - [ ] Password visibility toggles
   - [ ] Show/Hide toggles (financial section)
   - [ ] Checkboxes (5×5 = 25px, but parent label should be 44px)
   - [ ] Modal close buttons
   - [ ] Dropdown selectors

**How to measure**:
```
1. Right-click element → Inspect
2. Check computed styles
3. Look for: min-height: 44px
4. Or measure: height + padding ≥ 44px
```

---

## Input Font Size Verification (16px)

### iOS Zoom Prevention Test
**Critical for iOS devices**:

1. **Open on actual iOS device** (if available)
2. Or use Chrome DevTools → iPhone simulation
3. Tap each input field
4. **Should NOT zoom** if font-size = 16px

**Check these inputs**:
- [ ] Text inputs (name, email, etc.)
- [ ] Password inputs
- [ ] Textarea fields
- [ ] Number inputs
- [ ] Date inputs
- [ ] Select dropdowns

**Verify in inspector**:
```
Element should have: style="font-size: 16px"
Or computed style: font-size: 16px
```

---

## Data Persistence Testing

### Test Data Flow
**For each section**:

1. **Add Data**:
   - Fill form completely
   - Click Save
   - Check success message

2. **Verify Display**:
   - Data appears in section card
   - Proper formatting applied
   - Icons/badges display correctly

3. **Reload Test**:
   - Refresh browser (F5)
   - Data should still be there
   - Masking still works (financial)

4. **Edit Test**:
   - Click Edit button
   - Modal/form prefills with saved data
   - Make changes
   - Save → Changes persist

5. **Delete Test**:
   - Click Delete
   - Confirmation modal appears
   - Confirm → Data removed
   - Refresh → Data still gone

### Test Profile Show Page
**URL**: http://localhost:8000/profile

**Verify all sections display**:
- [ ] Sticky profile header (mobile)
- [ ] Profile completion percentage
- [ ] Basic information card
- [ ] Phone numbers with click-to-call
- [ ] Education cards with badges
- [ ] Work experience timeline
- [ ] Skills with proficiency
- [ ] Travel history cards
- [ ] Address information
- [ ] Documents (NID, Passport)
- [ ] Family members
- [ ] Financial summary (with masking)
- [ ] Language proficiency
- [ ] Security & background

---

## Performance Testing

### Page Load Speed
**Test on 3G simulation**:
1. Chrome DevTools → Network tab
2. Select "Slow 3G"
3. Reload page
4. Measure First Contentful Paint

**Target Metrics**:
- First Paint: < 1.8s
- Interactive: < 3.8s
- Page fully loaded: < 5s

### Lighthouse Audit
**Run mobile audit**:
```
1. Open /profile/edit or /profile
2. F12 → Lighthouse tab
3. Select "Mobile" mode
4. Categories: Performance, Accessibility, Best Practices
5. Click "Analyze page load"
```

**Target Scores**:
- Performance: > 90
- Accessibility: > 90
- Best Practices: > 90

---

## Browser Compatibility

### Desktop Browsers
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Edge (latest)
- [ ] Safari macOS (if available)

### Mobile Browsers
- [ ] Safari iOS 14+
- [ ] Chrome Android
- [ ] Samsung Internet
- [ ] Firefox Mobile

**Test in each browser**:
1. Forms submit correctly
2. Modals open/close
3. Gradients render
4. Icons display
5. Touch works
6. No layout shifts

---

## Accessibility Testing

### Keyboard Navigation
**Test without mouse**:
- [ ] Tab through all form fields
- [ ] Tab order is logical
- [ ] Focus indicators visible
- [ ] Enter submits forms
- [ ] Escape closes modals
- [ ] Space toggles checkboxes

### Screen Reader Testing (Optional)
**If available**:
- [ ] NVDA (Windows)
- [ ] JAWS (Windows)
- [ ] VoiceOver (macOS/iOS)

**Basic test**:
- Turn on screen reader
- Navigate profile edit page
- Verify labels are read
- Buttons announced correctly
- Errors are announced

---

## Bug Reporting Template

**If you find issues, document them**:

```markdown
### Bug: [Short description]

**Device**: iPhone SE / iPad / Desktop
**Viewport**: 375px / 768px / 1024px
**Browser**: Chrome 120
**URL**: /profile/edit

**Steps to Reproduce**:
1. Step 1
2. Step 2
3. Step 3

**Expected Result**:
What should happen

**Actual Result**:
What actually happened

**Screenshot**: [Attach if possible]

**Severity**: Critical / High / Medium / Low
```

---

## Quick Test Scenarios

### 🚀 5-Minute Smoke Test
**Verify nothing is broken**:
1. Open /profile/edit at 375px
2. Fill name fields → Save
3. Change password → Save
4. Add phone number → Save
5. Add family member → Save
6. Check /profile page displays data
7. No errors in console (F12)

### 🔥 15-Minute Core Test
**Test main functionality**:
1. Test all forms at 375px
2. Test all forms at 768px
3. Check data persistence
4. Verify touch targets
5. Test modals
6. Check gradients
7. Verify responsive layout

### ⚡ 30-Minute Full Test
**Comprehensive testing**:
1. Test all viewports (375, 390, 414, 768, 1024)
2. Test all forms and sections
3. Verify data persistence
4. Test edit/delete flows
5. Check Profile Show page
6. Run Lighthouse audit
7. Test on actual mobile device (if possible)
8. Document any issues

---

## Success Criteria

### ✅ Testing Complete When:
- [ ] All viewports tested (375px, 768px, 1024px minimum)
- [ ] All forms submit successfully
- [ ] Data persists after reload
- [ ] No input zoom on iOS (16px fonts verified)
- [ ] Touch targets ≥ 44px confirmed
- [ ] Modals work on all devices
- [ ] Profile Show page displays all data
- [ ] No console errors
- [ ] Gradients display correctly
- [ ] Icons render properly
- [ ] Password strength indicator works
- [ ] Data masking works (financial section)
- [ ] Click-to-call works (phone numbers)
- [ ] Verification badges display
- [ ] Empty states show correctly

### 📊 Quality Metrics
- **Mobile Lighthouse Score**: > 90
- **Touch Target Compliance**: 100%
- **Input Font Size**: 16px (100%)
- **Forms Working**: 100%
- **Data Persistence**: 100%

---

## Testing Tools

### Chrome DevTools
- **Device Toolbar**: Ctrl+Shift+M
- **Inspect Element**: F12 or Right-click → Inspect
- **Console**: Check for errors
- **Network**: Monitor API calls
- **Lighthouse**: Performance audits

### Firefox DevTools
- **Responsive Design Mode**: Ctrl+Shift+M
- **Inspector**: Similar to Chrome

### Browser Extensions (Optional)
- **Pesticide**: Visualize CSS boxes
- **WhatFont**: Check font sizes
- **ColorZilla**: Verify gradient colors

---

## Next Steps After Testing

1. **Document Issues**: Use bug template above
2. **Prioritize Fixes**: Critical → High → Medium → Low
3. **Retest After Fixes**: Verify issues resolved
4. **Sign Off**: Mark testing complete
5. **Deploy to Staging**: Test on real server
6. **User Acceptance Testing**: Get feedback from actual users

---

**Happy Testing! 🎉**

Last Updated: November 20, 2025  
Test Server: http://localhost:8000  
Dev Server: http://localhost:5174
