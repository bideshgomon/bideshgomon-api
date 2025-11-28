# 🌐 How Translations Work - Complete Guide

## Quick Start Demo

**Access the live demo:**
```
http://localhost/admin/translation-demo
```

This page shows all translations in action with **live switching** between English and Bengali.

---

## 📚 How It Works (Step by Step)

### **1. When User Visits the Site**

```
User opens http://localhost/admin/dashboard
         ↓
SetLocale Middleware checks (in order):
         ↓
1. URL parameter?        ?lang=bn  → Use Bengali
2. Session stored?       $_SESSION['locale'] → Use stored
3. User database?        $user->language → Use user preference
4. Browser header?       Accept-Language: bn → Use Bengali
5. Default              config('app.locale') → Use English
         ↓
App::setLocale('bn')  ← Sets language for entire request
         ↓
All translations now return Bengali text
```

### **2. Translation Files Structure**

```
lang/
├── en/                          ← English (default)
│   ├── auth.php                ← Login, logout messages
│   ├── pagination.php          ← Previous, next labels
│   ├── passwords.php           ← Password reset text
│   ├── validation.php          ← Form validation errors
│   └── ui.php                  ← Common UI labels
│
└── bn/                          ← Bengali (বাংলা)
    ├── auth.php                ← প্রমাণীকরণ বার্তা
    ├── pagination.php          ← পূর্ববর্তী, পরবর্তী
    ├── passwords.php           ← পাসওয়ার্ড রিসেট
    ├── validation.php          ← ফর্ম যাচাইকরণ
    └── ui.php                  ← সাধারণ UI লেবেল
```

### **3. Translation File Example**

**lang/en/ui.php:**
```php
<?php
return [
    'dashboard' => 'Dashboard',
    'users' => 'Users',
    'save' => 'Save',
    'welcome' => 'Welcome, :name!',  // ← :name is a placeholder
];
```

**lang/bn/ui.php:**
```php
<?php
return [
    'dashboard' => 'ড্যাশবোর্ড',
    'users' => 'ব্যবহারকারীগণ',
    'save' => 'সংরক্ষণ',
    'welcome' => 'স্বাগতম, :name!',  // ← :name replaced with actual name
];
```

---

## 💻 Using Translations in Code

### **Method 1: In Vue Components** ✨ (Recommended)

```vue
<script setup>
import { useTranslations } from '@/Composables/useTranslations';

const { trans, locale } = useTranslations();

// Example data
const userName = 'Ahmed Khan';
</script>

<template>
  <div>
    <!-- Simple translation -->
    <h1>{{ trans('ui.dashboard') }}</h1>
    <!-- Output: "Dashboard" or "ড্যাশবোর্ড" -->
    
    <!-- With placeholders -->
    <p>{{ trans('ui.welcome', { name: userName }) }}</p>
    <!-- Output: "Welcome, Ahmed Khan!" or "স্বাগতম, Ahmed Khan!" -->
    
    <!-- Current language -->
    <span>{{ locale }}</span>
    <!-- Output: "en" or "bn" -->
    
    <!-- Buttons -->
    <button>{{ trans('ui.save') }}</button>
    <button>{{ trans('ui.cancel') }}</button>
    <button>{{ trans('ui.delete') }}</button>
  </div>
</template>
```

### **Method 2: In Blade Templates**

```php
<h1>{{ __('ui.dashboard') }}</h1>

<!-- With placeholders -->
<p>{{ __('ui.welcome', ['name' => $user->name]) }}</p>

<!-- Form validation -->
@error('email')
    <span>{{ __('validation.required', ['attribute' => 'email']) }}</span>
@enderror

<!-- Buttons -->
<button>{{ __('ui.save') }}</button>
```

### **Method 3: In Controllers**

```php
<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Validation with translated messages
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ], [
            'name.required' => __('validation.required', ['attribute' => 'name']),
            'email.required' => __('validation.required', ['attribute' => 'email']),
        ]);
        
        // Success message
        return back()->with('success', __('ui.success'));
        
        // Error message
        return back()->with('error', __('ui.error'));
    }
    
    public function destroy($id)
    {
        // Confirmation message
        return response()->json([
            'message' => __('ui.delete_confirmation')
        ]);
    }
}
```

### **Method 4: In JavaScript/API**

```javascript
// Access via Inertia page props
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const translations = page.props.translations;

// Direct access
console.log(translations.ui.dashboard);    // "Dashboard" or "ড্যাশবোর্ড"
console.log(translations.ui.save);         // "Save" or "সংরক্ষণ"

// Current locale
console.log(page.props.locale);  // "en" or "bn"
```

---

## 🔄 Language Switching

### **Automatic Switching**

The LanguageSwitcher component automatically:
1. Shows current language with flag (🇬🇧 or 🇧🇩)
2. Provides dropdown to select language
3. Reloads page with new language
4. Saves preference to database + localStorage

### **Manual Switching via URL**

```
# Switch to Bengali
http://localhost/admin/dashboard?lang=bn

# Switch to English
http://localhost/admin/dashboard?lang=en
```

### **Programmatic Switching**

```javascript
// In Vue component
import { router } from '@inertiajs/vue3';

const switchLanguage = (langCode) => {
    router.visit(window.location.pathname, {
        data: { lang: langCode },
        preserveState: true,
        preserveScroll: true,
    });
};

// Usage
switchLanguage('bn');  // Switch to Bengali
switchLanguage('en');  // Switch to English
```

---

## 🎯 Real-World Examples

### **Example 1: Login Page**

```vue
<!-- resources/js/Pages/Auth/Login.vue -->
<script setup>
import { useTranslations } from '@/Composables/useTranslations';
const { trans } = useTranslations();
</script>

<template>
  <form>
    <h1>{{ trans('auth.login') }}</h1>
    
    <label>{{ trans('ui.email') }}</label>
    <input type="email" :placeholder="trans('ui.email')" />
    
    <label>{{ trans('ui.password') }}</label>
    <input type="password" :placeholder="trans('ui.password')" />
    
    <button>{{ trans('auth.login') }}</button>
  </form>
</template>
```

**Output in English:**
```
Login
Email: [input field]
Password: [input field]
[Login Button]
```

**Output in Bengali:**
```
লগইন
ইমেইল: [input field]
পাসওয়ার্ড: [input field]
[লগইন Button]
```

### **Example 2: Data Table**

```vue
<script setup>
import { useTranslations } from '@/Composables/useTranslations';
const { trans } = useTranslations();

const users = [
    { name: 'Ahmed', status: 'active' },
    { name: 'Fatima', status: 'pending' }
];
</script>

<template>
  <table>
    <thead>
      <tr>
        <th>{{ trans('ui.name') }}</th>
        <th>{{ trans('ui.status') }}</th>
        <th>{{ trans('ui.actions') }}</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="user in users" :key="user.name">
        <td>{{ user.name }}</td>
        <td>{{ trans('ui.' + user.status) }}</td>
        <td>
          <button>{{ trans('ui.edit') }}</button>
          <button>{{ trans('ui.delete') }}</button>
        </td>
      </tr>
    </tbody>
  </table>
</template>
```

**Output in English:**
```
| Name   | Status  | Actions         |
|--------|---------|-----------------|
| Ahmed  | Active  | Edit   Delete   |
| Fatima | Pending | Edit   Delete   |
```

**Output in Bengali:**
```
| নাম    | অবস্থা    | কর্ম                     |
|--------|----------|------------------------|
| Ahmed  | সক্রিয়   | সম্পাদনা   মুছে ফেলুন   |
| Fatima | অপেক্ষমান | সম্পাদনা   মুছে ফেলুন   |
```

### **Example 3: Validation Errors**

```php
// Controller
$request->validate([
    'name' => 'required|min:3',
    'email' => 'required|email|unique:users',
]);

// If validation fails:
```

**English Error:**
```
The name field is required.
The name must be at least 3 characters.
The email has already been taken.
```

**Bengali Error:**
```
নাম ফিল্ড আবশ্যক।
নাম অন্তত 3 অক্ষর হতে হবে।
ইমেইল ইতিমধ্যে নেওয়া হয়েছে।
```

---

## 🔍 Available Translation Keys

### **Navigation (ui.php)**
```php
trans('ui.dashboard')    // Dashboard / ড্যাশবোর্ড
trans('ui.users')        // Users / ব্যবহারকারীগণ
trans('ui.services')     // Services / সেবা
trans('ui.bookings')     // Bookings / বুকিং
trans('ui.settings')     // Settings / সেটিংস
trans('ui.profile')      // Profile / প্রোফাইল
trans('ui.logout')       // Logout / লগআউট
```

### **Actions (ui.php)**
```php
trans('ui.create')       // Create / তৈরি করুন
trans('ui.edit')         // Edit / সম্পাদনা
trans('ui.delete')       // Delete / মুছে ফেলুন
trans('ui.save')         // Save / সংরক্ষণ
trans('ui.cancel')       // Cancel / বাতিল
trans('ui.submit')       // Submit / জমা দিন
trans('ui.search')       // Search / খুঁজুন
trans('ui.filter')       // Filter / ফিল্টার
```

### **Status (ui.php)**
```php
trans('ui.active')       // Active / সক্রিয়
trans('ui.inactive')     // Inactive / নিষ্ক্রিয়
trans('ui.pending')      // Pending / অপেক্ষমান
trans('ui.approved')     // Approved / অনুমোদিত
trans('ui.rejected')     // Rejected / প্রত্যাখ্যাত
trans('ui.completed')    // Completed / সম্পন্ন
```

### **Messages (ui.php)**
```php
trans('ui.success')      // Success / সফল
trans('ui.error')        // Error / ত্রুটি
trans('ui.warning')      // Warning / সতর্কতা
trans('ui.info')         // Info / তথ্য
trans('ui.loading')      // Loading... / লোড হচ্ছে...
```

---

## 🛠️ Advanced Usage

### **Conditional Translation**

```vue
<script setup>
import { useTranslations } from '@/Composables/useTranslations';
const { trans, locale } = useTranslations();

const getGreeting = () => {
    const hour = new Date().getHours();
    if (locale.value === 'bn') {
        return hour < 12 ? 'সুপ্রভাত' : hour < 18 ? 'শুভ অপরাহ্ন' : 'শুভ সন্ধ্যা';
    }
    return hour < 12 ? 'Good morning' : hour < 18 ? 'Good afternoon' : 'Good evening';
};
</script>

<template>
  <h1>{{ getGreeting() }}, {{ user.name }}!</h1>
</template>
```

### **Pluralization**

```vue
<script setup>
const { transChoice } = useTranslations();
const count = 5;
</script>

<template>
  <!-- English: "5 users" -->
  <!-- Bengali: "৫ ব্যবহারকারী" -->
  <p>{{ count }} {{ trans('ui.users') }}</p>
</template>
```

### **Date Formatting**

```vue
<script setup>
import { useTranslations } from '@/Composables/useTranslations';
const { locale } = useTranslations();

const formatDate = (date) => {
    return new Intl.DateTimeFormat(
        locale.value === 'bn' ? 'bn-BD' : 'en-US'
    ).format(new Date(date));
};
</script>

<template>
  <!-- English: "11/27/2025" -->
  <!-- Bengali: "২৭/১১/২০২৫" -->
  <span>{{ formatDate('2025-11-27') }}</span>
</template>
```

---

## 🎯 Testing Translations

### **1. Test in Browser**
```
1. Open: http://localhost/admin/translation-demo
2. Click language switcher (top right)
3. Select "বাংলা" (Bengali)
4. Watch all text change instantly!
```

### **2. Test with URL**
```bash
# English
curl http://localhost/admin/dashboard

# Bengali
curl http://localhost/admin/dashboard?lang=bn
```

### **3. Test in Code**
```php
// In tinker
php artisan tinker

App::setLocale('en');
echo __('ui.dashboard');  // Output: Dashboard

App::setLocale('bn');
echo __('ui.dashboard');  // Output: ড্যাশবোর্ড
```

---

## 📊 Translation Coverage

Current implementation includes:

- ✅ **100+ UI labels** (buttons, navigation, forms)
- ✅ **50+ validation messages** (required, email, unique, etc.)
- ✅ **Authentication messages** (login, logout, failed)
- ✅ **Password reset** (email sent, token invalid, etc.)
- ✅ **Pagination** (previous, next)

---

## 🚀 Quick Reference

| What you want | How to do it |
|---------------|--------------|
| Translate text | `trans('ui.dashboard')` |
| With placeholder | `trans('ui.welcome', { name: 'Ahmed' })` |
| Get current language | `locale` |
| Switch language | Click switcher or `?lang=bn` |
| Add new translation | Edit `lang/en/ui.php` and `lang/bn/ui.php` |
| Test translations | Visit `/admin/translation-demo` |

---

**Now try it yourself!**
👉 **http://localhost/admin/translation-demo**

Switch between 🇬🇧 English and 🇧🇩 বাংলা to see translations in action!
