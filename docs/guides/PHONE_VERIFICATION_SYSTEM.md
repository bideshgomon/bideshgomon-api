# Phone Number Verification System

## Overview
Users can now verify their phone numbers using a 6-digit OTP (One-Time Password) system. This adds an extra layer of trust to user profiles and helps prevent fake accounts.

## Features
- ✅ 6-digit verification codes
- ✅ 10-minute code expiration
- ✅ Rate limiting (max 3 codes per hour)
- ✅ Brute force protection (max 5 attempts per code)
- ✅ Resend code functionality
- ✅ Bangladesh-focused UI with country code support
- ✅ Mobile-responsive verification modal

## User Flow
1. User adds a phone number to their profile
2. Click "Verify" button next to unverified number
3. System sends 6-digit code via SMS
4. User enters code in verification modal
5. Upon successful verification, phone number is marked as verified ✓

## Technical Architecture

### Database
**Table:** `phone_verification_codes`
- Stores verification codes with expiration
- Tracks usage status and attempts
- Links to `user_phone_numbers` table

### Backend Components
1. **PhoneVerificationCode Model** (`app/Models/PhoneVerificationCode.php`)
   - Manages code generation and validation
   - `generateCode()`: Creates random 6-digit code
   - `isValid()`: Checks expiration and usage status
   - `markAsUsed()`: Prevents code reuse

2. **SmsService** (`app/Services/SmsService.php`)
   - Handles SMS delivery
   - Currently logs codes for testing (see logs in `storage/logs/laravel.log`)
   - Ready for integration with:
     - SSL Wireless (Bangladesh)
     - Bulk SMS BD (Bangladesh)
     - Twilio (International)

3. **PhoneNumberController** (`app/Http/Controllers/PhoneNumberController.php`)
   - `sendVerificationCode()`: Generates and sends code
   - `verifyCode()`: Validates user input
   - `resendVerificationCode()`: Resends expired/lost codes

### Frontend Components
**PhoneNumbersSection.vue** (`resources/js/Pages/Profile/Partials/PhoneNumbersSection.vue`)
- Verification badge display (Verified/Unverified/Pending)
- Verify button for unverified numbers
- Verification modal with:
  - 6-digit input field
  - Resend code button
  - Countdown timer (10 minutes)
  - Error handling

### API Routes
```
POST /api/profile/phone-numbers/{id}/send-verification
POST /api/profile/phone-numbers/{id}/verify
POST /api/profile/phone-numbers/{id}/resend-verification
```

## Security Features

### Rate Limiting
- Max 3 verification codes per phone number per hour
- Prevents SMS spam and abuse
- Returns 429 error if limit exceeded

### Brute Force Protection
- Max 5 verification attempts per code
- Codes automatically invalidated after 5 failed attempts
- Forces user to request new code

### Code Expiration
- Codes expire after 10 minutes
- Expired codes cannot be used
- Old codes auto-invalidated when new code requested

### IP Tracking
- Records IP address for each verification attempt
- Helps detect suspicious activity
- Can be used for future fraud prevention

## SMS Gateway Integration

### Current Status (Development)
For testing, codes are logged to `storage/logs/laravel.log`:
```bash
# View verification codes in logs
tail -f storage/logs/laravel.log | grep "SMS Verification Code"
```

### Production Setup (Choose One)

#### Option 1: SSL Wireless (Recommended for Bangladesh)
1. Sign up at https://sslwireless.com/
2. Get API token and SID
3. Add to `.env`:
   ```
   SMS_SSL_WIRELESS_TOKEN=your_token_here
   SMS_SSL_WIRELESS_SID=your_sid_here
   ```
4. Uncomment `sendViaSslWireless()` in `SmsService.php`
5. Update `sendVerificationCode()` to use SSL Wireless method

#### Option 2: Twilio (International)
1. Sign up at https://www.twilio.com/
2. Get Account SID, Auth Token, and phone number
3. Install Twilio SDK:
   ```bash
   composer require twilio/sdk
   ```
4. Add to `.env`:
   ```
   TWILIO_ACCOUNT_SID=your_sid
   TWILIO_AUTH_TOKEN=your_token
   TWILIO_FROM=+1234567890
   ```
5. Uncomment `sendViaTwilio()` in `SmsService.php`

## Testing the Feature

### Local Testing
1. Add a phone number in profile
2. Click "Verify" button
3. Check `storage/logs/laravel.log` for the code:
   ```
   SMS Verification Code {"phone":"+8801712345678","code":"123456",...}
   ```
4. Enter the code in the verification modal
5. Number should show green "Verified" badge ✓

### Production Testing
Once SMS gateway is configured:
1. Add real phone number
2. Click "Verify"
3. Check actual SMS on phone
4. Enter code within 10 minutes
5. Verify success

## UI States

### Phone Number Badges
- **Verified** (Green): ✓ Verified with CheckBadgeIcon
- **Unverified** (Gray): Unverified with XCircleIcon
- **Pending** (Yellow): Verification in progress with ClockIcon (future feature)

### Verification Modal
- Clean, centered modal with gradient header
- Large 6-digit input field (optimized for mobile)
- Resend code link
- Cancel and Verify buttons
- Real-time validation feedback

## Future Enhancements
- [ ] SMS delivery status webhooks
- [ ] Phone number change notification to old number
- [ ] Two-factor authentication (2FA) using verified numbers
- [ ] Voice call verification as fallback
- [ ] Automated fraud detection patterns
- [ ] Bulk verification for agencies
- [ ] WhatsApp verification alternative

## Troubleshooting

### Code Not Received
1. Check `storage/logs/laravel.log` for SMS gateway errors
2. Verify SMS gateway credentials in `.env`
3. Check rate limiting (max 3 per hour)
4. Ensure phone number format is correct

### Verification Fails
1. Check code hasn't expired (10 minutes)
2. Verify max attempts not exceeded (5)
3. Ensure code is exactly 6 digits
4. Check network connectivity

### SMS Costs
- SSL Wireless BD: ~৳0.25-0.50 per SMS
- Twilio International: ~$0.05-0.10 per SMS
- Budget estimate: 1000 verifications = ৳250-500 or $50-100

## Database Cleanup (Optional)
Old verification codes can be cleaned up periodically:
```bash
# Delete codes older than 7 days
php artisan tinker
>>> PhoneVerificationCode::where('created_at', '<', now()->subDays(7))->delete();
```

Or create a scheduled job in `app/Console/Kernel.php`:
```php
$schedule->call(function () {
    PhoneVerificationCode::where('created_at', '<', now()->subDays(7))->delete();
})->daily();
```

---
**Created**: 21 Nov 2025  
**Status**: ✅ Fully Implemented (SMS integration pending)  
**Last Updated**: 21 Nov 2025
