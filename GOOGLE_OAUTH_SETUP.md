# Google OAuth Setup Guide

## 🔧 Configuration Required

The "Login with Google" feature requires Google OAuth credentials. Follow these steps to set it up:

---

## 1. Create Google OAuth Credentials

### Step 1: Go to Google Cloud Console
Visit: https://console.cloud.google.com/

### Step 2: Create a New Project (or use existing)
1. Click on the project dropdown at the top
2. Click "New Project"
3. Name it: **BideshGomon** (or your preferred name)
4. Click "Create"

### Step 3: Enable Google+ API
1. Go to **APIs & Services** → **Library**
2. Search for "Google+ API"
3. Click on it and press **Enable**

### Step 4: Create OAuth Credentials
1. Go to **APIs & Services** → **Credentials**
2. Click **Create Credentials** → **OAuth client ID**
3. If prompted, configure the **OAuth consent screen** first:
   - User Type: **External**
   - App name: **BideshGomon**
   - User support email: Your email
   - Developer contact: Your email
   - Scopes: Add `email` and `profile`
   - Add test users if still in testing

4. Create OAuth Client ID:
   - Application type: **Web application**
   - Name: **BideshGomon Web Client**
   
5. Add **Authorized JavaScript origins**:
   ```
   http://localhost:8000
   http://148.135.136.95
   https://yourdomain.com (if you have one)
   ```

6. Add **Authorized redirect URIs**:
   ```
   http://localhost:8000/auth/google/callback
   http://148.135.136.95/auth/google/callback
   https://yourdomain.com/auth/google/callback (if you have one)
   ```

7. Click **Create**

8. Copy the **Client ID** and **Client Secret**

---

## 2. Configure Your Application

### Local Development (.env)
Add these lines to your `.env` file:

```env
GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### Production (.env on VPS)
SSH into your VPS and update the `.env` file:

```bash
ssh root@148.135.136.95
cd /var/www/bideshgomon-api
nano .env
```

Add/update these lines:
```env
GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://148.135.136.95/auth/google/callback
```

Save and exit (Ctrl+X, Y, Enter)

---

## 3. Clear Cache & Restart

### Local Development
```bash
php artisan config:clear
php artisan cache:clear
```

### Production (VPS)
```bash
ssh root@148.135.136.95
cd /var/www/bideshgomon-api
php artisan config:clear
php artisan cache:clear
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

---

## 4. Test Google Login

### Test Flow:
1. Visit: http://148.135.136.95/login
2. Click **"Continue with Google"**
3. You should be redirected to Google's login page
4. Sign in with your Google account
5. Grant permissions
6. You'll be redirected back to the app and logged in

### If it still doesn't work:
1. Check Laravel logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Verify the route is registered:
   ```bash
   php artisan route:list | grep google
   ```

3. Check if Socialite is installed:
   ```bash
   composer show | grep socialite
   ```
   
   If not installed:
   ```bash
   composer require laravel/socialite
   ```

---

## 5. Database Migration (if needed)

The users table needs Google OAuth columns. Check if they exist:

```bash
php artisan tinker
>>> Schema::hasColumn('users', 'google_id')
```

If `false`, create a migration:

```bash
php artisan make:migration add_google_oauth_to_users_table
```

Edit the migration file:
```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('google_id')->nullable()->unique()->after('email');
        $table->text('google_token')->nullable()->after('google_id');
        $table->text('google_refresh_token')->nullable()->after('google_token');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['google_id', 'google_token', 'google_refresh_token']);
    });
}
```

Run the migration:
```bash
php artisan migrate
```

---

## 6. Security Best Practices

### ✅ DO:
- Keep your `GOOGLE_CLIENT_SECRET` private
- Use HTTPS in production (recommended)
- Add only necessary redirect URIs
- Review OAuth consent screen regularly

### ❌ DON'T:
- Commit `.env` file to Git
- Share credentials publicly
- Use production credentials in development

---

## 7. Troubleshooting

### Error: "redirect_uri_mismatch"
- **Cause**: The redirect URI doesn't match what's configured in Google Cloud Console
- **Fix**: Ensure the redirect URI in `.env` exactly matches one in Google Console (including protocol and trailing slash)

### Error: "invalid_client"
- **Cause**: Wrong Client ID or Client Secret
- **Fix**: Double-check credentials in `.env` file

### Error: "access_denied"
- **Cause**: User denied permission or app not verified
- **Fix**: Add user as a test user in OAuth consent screen

### Google login redirects but doesn't log in
- **Cause**: Session/cookie issues
- **Fix**: 
  ```bash
  php artisan config:clear
  php artisan session:clear (if available)
  ```
  Or clear browser cookies

---

## 8. Current Implementation

### Routes (already configured):
- `GET /auth/google` → Redirects to Google
- `GET /auth/google/callback` → Handles Google response

### Controller (already created):
- `app/Http/Controllers/Auth/SocialAuthController.php`

### Features:
✅ Creates new user if email doesn't exist  
✅ Links existing user if email matches  
✅ Auto-verifies email  
✅ Redirects to dashboard after login  
✅ Stores Google tokens for future API calls  

---

## Quick Setup Checklist

- [ ] Create Google OAuth app in Cloud Console
- [ ] Add authorized origins and redirect URIs
- [ ] Copy Client ID and Client Secret
- [ ] Add to `.env` file (local and production)
- [ ] Clear config cache
- [ ] Ensure `laravel/socialite` is installed
- [ ] Test login flow
- [ ] Monitor logs for errors

---

**Need Help?**  
Check Laravel Socialite documentation: https://laravel.com/docs/socialite

**Last Updated:** November 20, 2025
