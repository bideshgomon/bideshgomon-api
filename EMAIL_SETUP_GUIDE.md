# Email Configuration Guide for BideshGomon

## 🔧 Current Status
**Email Mode:** LOG (emails are written to log file, not sent)

To make forgot password and other emails actually send, you need to configure SMTP.

---

## 📧 Option 1: Gmail SMTP (Recommended for Testing)

### Step 1: Create App Password in Gmail
1. Go to your Google Account: https://myaccount.google.com/
2. Go to **Security** → **2-Step Verification** (enable if not enabled)
3. Scroll down and click **App passwords**
4. Select **Mail** and **Other (Custom name)** → Enter "BideshGomon"
5. Click **Generate**
6. Copy the 16-character password (e.g., `abcd efgh ijkl mnop`)

### Step 2: Update VPS .env
```bash
ssh root@148.135.136.95
cd /var/www/bideshgomon
nano .env
```

Update these lines:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bideshgomon.com
MAIL_FROM_NAME="Bidesh Gomon"
```

### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan config:cache
```

### Step 4: Test
Visit: http://148.135.136.95/forgot-password
Enter an email and check if you receive it!

---

## 📧 Option 2: Resend (Free, Professional)

**Best for production** - 3,000 free emails/month, no credit card required.

### Step 1: Create Account
1. Go to: https://resend.com/signup
2. Sign up with your email
3. Verify your email

### Step 2: Get API Key
1. Go to **API Keys** in dashboard
2. Click **Create API Key**
3. Name it "BideshGomon Production"
4. Copy the key (starts with `re_`)

### Step 3: Add Domain (Optional but recommended)
1. Go to **Domains** → **Add Domain**
2. Enter: `bideshgomon.com`
3. Add DNS records provided by Resend
4. Wait for verification

### Step 4: Update VPS .env
```bash
ssh root@148.135.136.95
cd /var/www/bideshgomon
nano .env
```

Update:
```env
MAIL_MAILER=resend
RESEND_API_KEY=re_your_api_key_here
MAIL_FROM_ADDRESS=noreply@bideshgomon.com
MAIL_FROM_NAME="Bidesh Gomon"
```

### Step 5: Clear Cache
```bash
php artisan config:clear
php artisan config:cache
```

---

## 📧 Option 3: Mailtrap (Testing Only)

**Best for development/testing** - emails won't go to real users.

### Step 1: Create Account
1. Go to: https://mailtrap.io/
2. Sign up free
3. Go to **Email Testing** → **Inboxes** → **My Inbox**

### Step 2: Get SMTP Credentials
Copy the credentials shown in the inbox

### Step 3: Update VPS .env
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bideshgomon.com
MAIL_FROM_NAME="Bidesh Gomon"
```

---

## 📧 Option 4: SendGrid (Scalable, Free Tier)

**For serious production** - 100 free emails/day forever.

### Step 1: Create Account
1. Go to: https://signup.sendgrid.com/
2. Sign up
3. Verify email and complete account setup

### Step 2: Create API Key
1. Go to **Settings** → **API Keys**
2. Click **Create API Key**
3. Name: "BideshGomon Production"
4. Permissions: **Full Access**
5. Copy the API key (starts with `SG.`)

### Step 3: Verify Sender Identity
1. Go to **Settings** → **Sender Authentication**
2. Click **Verify a Single Sender**
3. Fill in your details:
   - From Email: noreply@bideshgomon.com
   - From Name: Bidesh Gomon
4. Verify your email

### Step 4: Update VPS .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bideshgomon.com
MAIL_FROM_NAME="Bidesh Gomon"
```

---

## 📧 Option 5: Mailgun (Professional)

Similar to SendGrid with free tier.

1. Sign up at: https://www.mailgun.com/
2. Get API credentials
3. Configure in .env

---

## 🧪 Testing Email Configuration

### Test Command
```bash
ssh root@148.135.136.95
cd /var/www/bideshgomon
php artisan tinker
```

Then run:
```php
Mail::raw('Test email from BideshGomon', function($message) {
    $message->to('your-email@gmail.com')
            ->subject('Test Email');
});
```

If successful, you'll see: `true`

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

---

## 📝 Email Templates

The system uses these email notifications:

1. **Password Reset** (Forgot Password)
   - Template: Laravel's built-in `ResetPassword` notification
   - Customizable in: `app/Notifications/ResetPasswordNotification.php` (if you create it)

2. **Email Verification** (when users register)
   - Automatic with Laravel Breeze

3. **Future emails** (you can add):
   - Welcome email
   - Booking confirmation
   - Visa status update
   - Payment receipts

---

## 🎯 Recommended Setup for Production

**For BideshGomon, I recommend:**

1. **Start with Gmail** (quick setup, works immediately)
2. **Move to Resend** (professional, free 3k emails/month, better deliverability)
3. **Scale to SendGrid/Mailgun** (when you need more than 3k emails/month)

---

## 🚨 Current Setup Script

I can configure Gmail for you right now if you provide:
1. Your Gmail address
2. Generate an App Password (see Gmail steps above)

Or run this automated setup:

```bash
# Quick setup with Gmail
ssh root@148.135.136.95 "cd /var/www/bideshgomon && cat >> .env << 'EOF'

# Gmail SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bideshgomon.com
MAIL_FROM_NAME=\"Bidesh Gomon\"
EOF
"

# Clear cache
ssh root@148.135.136.95 "cd /var/www/bideshgomon && php artisan config:clear && php artisan config:cache"
```

---

## ✅ Quick Test Checklist

- [ ] Get Gmail App Password or other SMTP credentials
- [ ] Update .env on VPS
- [ ] Clear config cache
- [ ] Test with forgot password page
- [ ] Check if email arrives in inbox
- [ ] Monitor logs for errors

---

**Need help?** Let me know your email service preference and I can configure it for you!

**Last Updated:** November 21, 2025
