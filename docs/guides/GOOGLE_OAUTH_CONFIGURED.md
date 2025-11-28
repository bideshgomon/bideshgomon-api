# ✅ Google OAuth Configuration Complete

## 📋 Your Credentials

**Client ID:**
```
YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com
```

**Client Secret:**
```
YOUR_GOOGLE_CLIENT_SECRET
```

**Project ID:** `bidesh-gomon`

---

## 🔧 Current Configuration Status

### ✅ Local Development (Configured)
- `.env` file created with Google OAuth credentials
- Redirect URI: `http://localhost:8000/auth/google/callback`
- App URL: `http://localhost:8000`

### ⚠️ Production VPS (Needs Update)
- VPS IP: `148.135.136.95`
- Required Redirect URI: `http://148.135.136.95/auth/google/callback`

---

## 🚨 IMPORTANT: Update Google Cloud Console

Your Google OAuth app currently has these redirect URIs configured:
- ✅ `https://bideshgomon.com/auth/google/callback`

**You MUST add these additional redirect URIs:**

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select project: **bidesh-gomon**
3. Go to **APIs & Services** → **Credentials**
4. Click on your OAuth 2.0 Client ID
5. Under **Authorized redirect URIs**, add:
   ```
   http://localhost:8000/auth/google/callback
   http://148.135.136.95/auth/google/callback
   ```
6. Under **Authorized JavaScript origins**, add:
   ```
   http://localhost:8000
   http://148.135.136.95
   ```
7. Click **Save**

---

## 🧪 Test Locally

### Start the development server:
```bash
php artisan serve
```

### Visit and test:
```
http://localhost:8000/login
```

Click "Continue with Google" - it should redirect to Google's login page.

**Note:** You may see "redirect_uri_mismatch" error until you add the redirect URIs above.

---

## 🚀 Deploy to VPS

### 1. Update VPS .env file:
```bash
ssh root@148.135.136.95
cd /var/www/bideshgomon-api
nano .env
```

Add these lines:
```env
GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=YOUR_GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URI=http://148.135.136.95/auth/google/callback
```

### 2. Deploy updated code:
```bash
# From your local machine
cd /Users/sbmac/projects/bideshgomon-api
./deploy.sh
```

### 3. Clear caches on VPS:
```bash
ssh root@148.135.136.95
cd /var/www/bideshgomon-api
php artisan config:clear
php artisan cache:clear
php artisan config:cache
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

### 4. Test on VPS:
```
http://148.135.136.95/login
```

---

## 🎯 Test Accounts

You can test Google OAuth with these existing users OR create new accounts:

**Existing Users (will link Google account):**
- john@test.com / password
- rahim.gulf@test.com / password
- nafisa.student@test.com / password
- tanvir.it@test.com / password

**New Google Users:**
- Sign in with any Google account
- New user will be created automatically
- Email will be auto-verified
- Will be redirected to dashboard

---

## 🐛 Troubleshooting

### Error: "redirect_uri_mismatch"
**Solution:** Add the exact redirect URI to Google Cloud Console as shown above.

### Error: "invalid_client"
**Solution:** Double-check Client ID and Secret in `.env` - ensure no extra spaces.

### Error: "This app hasn't been verified"
**Solutions:**
1. Click "Advanced" → "Go to BideshGomon (unsafe)" - safe for testing
2. OR add your test email as a test user in OAuth consent screen
3. OR submit app for verification (takes time)

### Google login works but doesn't log in
**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan session:table
php artisan migrate
```

---

## 📊 What Happens During Google Login

1. **User clicks "Continue with Google"**
   - Redirects to: `/auth/google`
   - Laravel Socialite redirects to Google

2. **User signs in with Google**
   - Google shows consent screen
   - User approves permissions

3. **Google redirects back**
   - To: `/auth/google/callback`
   - With authorization code

4. **Laravel processes login**
   - Exchanges code for user info
   - Gets: name, email, Google ID, avatar
   - Checks if email exists in database

5. **Creates or updates user**
   - **If email exists:** Links Google account to existing user
   - **If new email:** Creates new user with:
     - Verified email
     - Random password
     - User role
     - Wallet (৳0.00)
     - Referral code

6. **Logs in and redirects**
   - User is logged in
   - Redirected to: `/dashboard`

---

## 🔒 Security Notes

- ✅ Client Secret is stored in `.env` (not committed to Git)
- ✅ Email is automatically verified for Google users
- ✅ Google tokens are stored for future API calls
- ✅ Random password generated (user can reset if needed)
- ⚠️ Currently using HTTP - consider HTTPS for production

---

## 📁 Files Configured

- ✅ `config/services.php` - Google OAuth config
- ✅ `.env` - Google credentials (local)
- ✅ `composer.json` - Laravel Socialite package
- ✅ `app/Http/Controllers/Auth/SocialAuthController.php` - OAuth logic
- ✅ `routes/web.php` - OAuth routes
- ✅ `database/migrations/*_add_oauth_fields_to_users_table.php` - DB schema
- ✅ `app/Models/User.php` - Fillable fields

---

## ✅ Checklist

### Local Development
- [x] Google OAuth credentials obtained
- [x] `.env` file configured
- [x] Database migrated
- [x] Laravel Socialite installed
- [ ] Redirect URIs added to Google Console
- [ ] Test login flow

### Production VPS
- [ ] Code deployed to VPS
- [ ] `.env` updated on VPS with credentials
- [ ] Redirect URIs added to Google Console
- [ ] Caches cleared on VPS
- [ ] Test login on production

---

**Status:** ✅ Configuration Complete - Ready to Add Redirect URIs  
**Next Step:** Add redirect URIs in Google Cloud Console, then test  
**Last Updated:** November 21, 2025
