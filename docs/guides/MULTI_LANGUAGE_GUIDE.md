# Multi-Language Support Guide

## Overview
BideshGomon now supports English and Bengali (বাংলা) with automatic language detection and persistent user preferences.

## ✅ Features Implemented

### 1. Language System
- **Available Languages:** English (en), Bengali (bn)
- **Automatic Detection:** Browser language, session, user preference
- **Persistent Storage:** Database + localStorage + session
- **Fallback System:** Graceful degradation to English

### 2. Components
- **LanguageSwitcher.vue** - Dropdown with country flags
- **SetLocale Middleware** - Automatic language detection
- **useTranslations Composable** - Helper for accessing translations

### 3. Translation Files
```
lang/
├── en/ (English - default)
│   ├── auth.php
│   ├── pagination.php
│   ├── passwords.php
│   ├── validation.php
│   └── ui.php
└── bn/ (Bengali - বাংলা)
    ├── auth.php
    ├── pagination.php
    ├── passwords.php
    ├── validation.php
    └── ui.php
```

## 🎯 Using Translations

### In Blade Templates

```php
<!-- Simple translation -->
{{ __('ui.dashboard') }}

<!-- With replacements -->
{{ __('validation.required', ['attribute' => 'email']) }}

<!-- Choice (pluralization) -->
{{ trans_choice('messages.notifications', $count) }}
```

### In Vue Components

```vue
<script setup>
import { useTranslations } from '@/Composables/useTranslations';

const { trans, locale } = useTranslations();
</script>

<template>
  <div>
    <!-- Using trans function -->
    <h1>{{ trans('ui.dashboard') }}</h1>
    
    <!-- With replacements -->
    <p>{{ trans('ui.welcome', { name: user.name }) }}</p>
    
    <!-- Current locale -->
    <span>Current: {{ locale }}</span>
  </div>
</template>
```

### In Controllers

```php
use Illuminate\Support\Facades\App;

public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
    ], [
        'name.required' => __('validation.required', ['attribute' => 'name'])
    ]);
    
    return back()->with('success', __('ui.success'));
}
```

### In JavaScript/API

```javascript
// Available in Inertia page props
const { locale, translations } = usePage().props;

// Access translations
translations.ui.dashboard    // "Dashboard"
translations.auth.failed     // "These credentials..."

// Or use the composable
import { useTranslations } from '@/Composables/useTranslations';
const { trans } = useTranslations();
trans('ui.dashboard');
```

## 🌐 Language Switcher

The LanguageSwitcher component is available in:
- **Admin Panel** - Top right header (before notifications)
- **User Area** - Top right (before user menu)

### Features:
- 🇬🇧 English flag for English
- 🇧🇩 Bangladesh flag for Bengali
- Dropdown with native names (English, বাংলা)
- Current language highlighted
- Smooth transitions
- Persistent across sessions

## 🔄 Language Detection Priority

The system detects language in this order:

1. **URL Parameter** - `?lang=bn`
2. **Session Storage** - Previously selected language
3. **User Database** - Authenticated user's preference
4. **Browser Header** - Accept-Language
5. **Default** - `en` (English)

### Example Flow:

```
User visits site
    ↓
Check URL parameter (?lang=bn) → Found? Set Bengali
    ↓ (if not found)
Check session → Found? Use stored language
    ↓ (if not found)
Check user database → Logged in? Use user.language
    ↓ (if not found)
Check browser header → Match available? Use that
    ↓ (if not found)
Use default (English)
```

## 📝 Adding New Translations

### Step 1: Add to Translation Files

**lang/en/ui.php:**
```php
return [
    'new_feature' => 'New Feature',
    'welcome_message' => 'Welcome, :name!',
];
```

**lang/bn/ui.php:**
```php
return [
    'new_feature' => 'নতুন ফিচার',
    'welcome_message' => 'স্বাগতম, :name!',
];
```

### Step 2: Use in Code

```vue
<template>
  <h1>{{ trans('ui.new_feature') }}</h1>
  <p>{{ trans('ui.welcome_message', { name: 'Ahmed' }) }}</p>
</template>
```

## 🛠️ Configuration

### Available Locales

Edit `config/app.php`:
```php
'available_locales' => ['en', 'bn'],
```

### Default Locale

```env
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

### Middleware

SetLocale middleware is automatically applied to all web routes:
```php
// bootstrap/app.php
$middleware->web(append: [
    \App\Http\Middleware\SetLocale::class,
]);
```

## 🎨 Styling Bengali Text

Bengali text uses specific fonts for proper rendering:

```css
/* Already included in app.css */
.bn-text {
    font-family: 'Kalpurush', 'SolaimanLipi', 'Noto Sans Bengali', sans-serif;
}
```

## 🔧 Testing Language Switching

### Manual Testing:

```bash
# 1. Open browser
http://localhost/login

# 2. Click language switcher (top right)
# 3. Select "বাংলা" (Bengali)
# 4. Verify:
#    - UI text changes to Bengali
#    - Language persists on page refresh
#    - User preference saved to database
```

### Testing with cURL:

```bash
# English (default)
curl http://localhost/api/user

# Force Bengali
curl http://localhost/api/user?lang=bn

# With Accept-Language header
curl -H "Accept-Language: bn-BD,bn;q=0.9" http://localhost/api/user
```

### Testing in Code:

```php
// Force specific locale for testing
App::setLocale('bn');
echo __('ui.dashboard'); // Output: ড্যাশবোর্ড

App::setLocale('en');
echo __('ui.dashboard'); // Output: Dashboard
```

## 📊 Translation Coverage

### Current Coverage:

| Category | English | Bengali | Coverage |
|----------|---------|---------|----------|
| **Authentication** | ✅ | ✅ | 100% |
| **Validation** | ✅ | ✅ | 100% |
| **Pagination** | ✅ | ✅ | 100% |
| **Passwords** | ✅ | ✅ | 100% |
| **UI Common** | ✅ | ✅ | 100% |
| **Admin Panel** | ⏳ | ⏳ | 40% |
| **User Area** | ⏳ | ⏳ | 40% |

### Priority for Next Phase:
1. Service-specific translations
2. Email templates
3. Notification messages
4. Error messages
5. Form labels

## 🌍 Adding More Languages

To add a new language (e.g., Hindi):

### Step 1: Add to Config
```php
// config/app.php
'available_locales' => ['en', 'bn', 'hi'],
```

### Step 2: Create Translation Files
```bash
mkdir lang/hi
cp lang/en/*.php lang/hi/
# Then translate each file
```

### Step 3: Update LanguageSwitcher
```vue
// resources/js/Components/LanguageSwitcher.vue
const languages = [
    { code: 'en', name: 'English', flag: '🇬🇧', nativeName: 'English' },
    { code: 'bn', name: 'Bengali', flag: '🇧🇩', nativeName: 'বাংলা' },
    { code: 'hi', name: 'Hindi', flag: '🇮🇳', nativeName: 'हिन्दी' }
];
```

## 🐛 Troubleshooting

### Issue: Language not changing

**Solution:**
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild assets
npm run build
```

### Issue: Translations not found

**Check:**
1. File exists in `lang/{locale}/` directory
2. Key matches exactly (case-sensitive)
3. Cache cleared after adding translations
4. Locale is in `available_locales` config

**Debug:**
```php
// Check current locale
dd(app()->getLocale());

// Check translation exists
dd(trans('ui.dashboard'));

// Check all translations
dd(trans('ui'));
```

### Issue: Bengali text showing boxes

**Solution:** Ensure proper font support:
```html
<!-- Add to app.blade.php -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali&display=swap" rel="stylesheet">
```

## 📱 Mobile Considerations

The LanguageSwitcher is responsive:
- **Desktop:** Shows flag + text
- **Mobile:** Shows flag only (text hidden)
- **Touch-friendly:** Larger tap targets

## ♿ Accessibility

- **Keyboard Navigation:** Full keyboard support
- **Screen Readers:** Proper ARIA labels
- **Language Tags:** HTML lang attribute updates automatically

## 🔐 Security

- **Validation:** Only allowed locales accepted
- **XSS Protection:** All translations escaped
- **SQL Injection:** Uses prepared statements for user.language

## 📈 Performance

- **Caching:** Translations cached automatically
- **Lazy Loading:** Only loads current locale
- **Minimal Bundle:** ~2KB per language file
- **CDN Ready:** Translation files can be cached

## 🎯 Best Practices

### DO:
✅ Use translation keys (`ui.dashboard`) not hardcoded text
✅ Keep translations organized by feature
✅ Test both languages regularly
✅ Use placeholders for dynamic content
✅ Provide context in comments

### DON'T:
❌ Hardcode text strings in components
❌ Mix multiple languages in one file
❌ Use translation keys as sentences
❌ Forget to translate error messages
❌ Assume RTL support (Bengali is LTR)

## 📞 Quick Reference

### Common Translation Keys:

```php
// Navigation
trans('ui.dashboard')        // Dashboard / ড্যাশবোর্ড
trans('ui.users')           // Users / ব্যবহারকারীগণ
trans('ui.services')        // Services / সেবা
trans('ui.settings')        // Settings / সেটিংস

// Actions
trans('ui.create')          // Create / তৈরি করুন
trans('ui.edit')            // Edit / সম্পাদনা
trans('ui.delete')          // Delete / মুছে ফেলুন
trans('ui.save')            // Save / সংরক্ষণ

// Status
trans('ui.active')          // Active / সক্রিয়
trans('ui.pending')         // Pending / অপেক্ষমান
trans('ui.completed')       // Completed / সম্পন্ন

// Messages
trans('ui.success')         // Success / সফল
trans('ui.error')           // Error / ত্রুটি
trans('ui.loading')         // Loading... / লোড হচ্ছে...
```

## 🚀 Future Enhancements

Planned improvements:
1. **RTL Support** - For Arabic/Urdu
2. **Date Formatting** - Locale-specific dates
3. **Number Formatting** - Locale-specific numbers
4. **Currency** - BDT, USD formatting
5. **Time Zones** - Automatic timezone detection
6. **Translation Management** - Admin UI for translations

---

**System Status:** ✅ Active and Operational
**Languages:** English, Bengali (বাংলা)
**Last Updated:** November 27, 2025
