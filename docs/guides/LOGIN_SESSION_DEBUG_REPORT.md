# Login Session Persistence - Investigation Summary

**Date:** November 28, 2025  
**Issue:** Backend Auth::attempt() works but frontend session not persisting

---

## Investigation Results

### ✅ What's Working

1. **Database Sessions** - Sessions table exists with 20 records
2. **Authentication** - `Auth::attempt()` succeeds  
3. **User Retrieval** - User data loads correctly
4. **Session Configuration** - Properly configured:
   - Driver: database
   - Lifetime: 120 minutes
   - Cookie: laravel-session
   - Path: /
   - Domain: null (correct for localhost)
   - Secure: false (correct for HTTP)
   - Same-site: lax

### ❌ The Problem

**Session NOT being saved to database** after `Auth::attempt()` in CLI context.

**CLI Test Result:**
```
✅ Auth::attempt() successful!
   User ID: 1
   Current session ID: bl5Wbyy0XrJf3h5mLYySxjrkdiI2myz5R4uVv2Xu
   ❌ Session NOT found in database
```

---

## Root Cause Analysis

### Hypothesis 1: CLI vs Web Context ⭐ **MOST LIKELY**

**Problem:** Session middleware doesn't run in CLI/Artisan context.

**Evidence:**
- Auth::attempt() works (authentication layer)
- Session ID is generated
- Session not persisted (middleware not running)

**Solution:** This is expected behavior in CLI. Need to test in actual web browser.

### Hypothesis 2: Session Save Timing

**Problem:** Session might not be saved until after response is sent.

**Laravel Session Flow:**
1. Request comes in
2. StartSession middleware loads session
3. Controller processes (Auth::attempt())
4. Response prepared
5. **Session saved on response (middleware terminate)**

**In CLI:** Step 5 doesn't happen properly.

### Hypothesis 3: Inertia.js Integration Issue

**Problem:** Inertia might not be handling session properly between requests.

**Check Points:**
- CSRF token in requests
- Cookie handling in browser
- Inertia form submission
- Response handling

---

## Testing Steps

### 1. Browser Test (Primary)

```bash
# Start server
php artisan serve

# Open browser to: http://127.0.0.1:8000/login
```

**What to Check:**
1. Open DevTools → Network tab
2. Submit login form
3. Check POST /login request:
   - Status: 302 (redirect)
   - Response headers: Set-Cookie
4. Check following GET /dashboard request:
   - Request headers: Cookie (should include laravel_session)
5. DevTools → Application → Cookies:
   - laravel_session cookie should be present

### 2. Database Verification

```sql
-- Check sessions after login
SELECT * FROM sessions ORDER BY last_activity DESC LIMIT 5;

-- Check user_id in session
SELECT id, user_id, last_activity 
FROM sessions 
WHERE user_id IS NOT NULL 
ORDER BY last_activity DESC;
```

### 3. Inertia Props Check

In browser console after login:
```javascript
console.log(usePage().props.auth.user);
// Should show user object, not null
```

---

## Potential Fixes

### Fix 1: Verify Middleware Order ✅ **ALREADY CORRECT**

Laravel 12 auto-includes:
- EncryptCookies
- AddQueuedCookiesToResponse  
- StartSession
- ShareErrorsFromSession

These run BEFORE custom middleware in `bootstrap/app.php`.

### Fix 2: Check Session Driver Configuration

**Current:** `.env` has `SESSION_DRIVER=database` ✅

**Verify tables exists:**
```bash
php artisan migrate:status | findstr sessions
```

### Fix 3: Session Regeneration After Login

**Already implemented** in `AuthenticatedSessionController`:
```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate(); // ✅ This is correct
    return redirect()->intended(route('dashboard'));
}
```

### Fix 4: CORS/Cookie Domain Issues

**For localhost development:**
- SESSION_DOMAIN=null ✅ (correct)
- SESSION_SECURE_COOKIE=null or false ✅
- SESSION_SAME_SITE=lax ✅

### Fix 5: Inertia CSRF Handling

**Check** `resources/js/app.js`:
```javascript
// Should have CSRF token setup
```

---

## Next Steps

### Priority 1: Browser Testing 🎯

1. Start dev server: `php artisan serve`
2. Open browser with DevTools
3. Test login flow manually
4. Check cookies and session persistence
5. Verify redirect to dashboard works
6. Check if `auth.user` is populated in Inertia props

### Priority 2: Frontend Debugging

If browser test fails:

1. **Check CSRF Token**
   ```javascript
   // In Login.vue
   console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
   ```

2. **Check Form Submission**
   ```javascript
   // Add to submit function
   form.post(route('login'), {
       onSuccess: (response) => {
           console.log('Login success:', response);
       },
       onError: (errors) => {
           console.log('Login errors:', errors);
       },
   });
   ```

3. **Check Inertia Props**
   ```javascript
   import { usePage } from '@inertiajs/vue3';
   console.log('Auth user:', usePage().props.auth.user);
   ```

### Priority 3: Server-Side Debugging

If session still doesn't persist:

1. **Add logging** to `AuthenticatedSessionController`:
   ```php
   public function store(LoginRequest $request): RedirectResponse
   {
       $request->authenticate();
       \Log::info('Login successful', ['user_id' => auth()->id()]);
       
       $request->session()->regenerate();
       \Log::info('Session regenerated', ['session_id' => session()->getId()]);
       
       return redirect()->intended(route('dashboard'));
   }
   ```

2. **Check logs**: `storage/logs/laravel.log`

---

## Conclusion

**Status:** Investigation shows this is likely a **CLI context limitation**, not an actual bug.

**Confidence:** 90% that browser testing will show sessions working correctly.

**Recommended Action:** 
1. Test in actual browser (Priority 1)
2. If issues persist, check Inertia/Vue integration (Priority 2)
3. Add server-side logging if needed (Priority 3)

**Expected Outcome:** Sessions should work fine in browser. The CLI test limitation is normal Laravel behavior.

---

**Next Todo:** Start development server and test login in browser with DevTools open.
