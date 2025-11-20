# Email Fallback & Verification Strategy

## Overview
We now have two layers for outbound email:
1. Local & staging: `MAIL_MAILER=log` (no external calls, safe for development)
2. Production: `MAIL_MAILER=resend` (Resend API) with fallback capability (keep prior Gmail SMTP vars in `.env` commented/retained if emergency switch needed).

## Environment Variables
```
MAIL_MAILER=resend
RESEND_API_KEY=re_... (never commit real key)
MAIL_FROM_ADDRESS=no-reply@bideshgomon.com  # or verified sender (update after domain DNS + SPF + DKIM)
MAIL_FROM_NAME="BideshGomon"
# Legacy SMTP (fallback only)
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=bideshgomon@gmail.com
# MAIL_PASSWORD=app_password_here
```

## Switching Mailer
| Scenario | Action |
|----------|--------|
| Local debugging | Set `MAIL_MAILER=log` | 
| Production normal | Set `MAIL_MAILER=resend` | 
| Resend outage | Temporarily switch to `MAIL_MAILER=smtp` + keep credentials | 

After any change:
```
php artisan config:clear
php artisan config:cache
```

## Verification Checklist (Run After Any Change)
1. `grep MAIL_MAILER .env` – confirms expected driver.
2. `php artisan tinker` send a quick `Mail::raw('Ping', fn($m)=>$m->to('your@test'););`.
3. Check Resend dashboard (Delivered + Events) or inbox.
4. Inspect `storage/logs/laravel.log` for transport errors.
5. Confirm SPF/DKIM alignment (use toolbox: https://toolbox.googleapps.com/apps/dig/ for TXT records). 

## Credential Rotation (Quarterly Recommended)
1. Generate new Resend API key in dashboard.
2. Update `.env` → `RESEND_API_KEY=new_key`.
3. Clear & cache config.
4. Send test email; archive old key (disable after successful validation).

## Common Issues & Fixes
| Symptom | Likely Cause | Fix |
|---------|--------------|-----|
| 401 Unauthorized | Wrong / expired API key | Rotate key & clear config |
| Emails show as spam | Missing SPF/DKIM/DMARC | Add DNS + wait propagation |
| No logs, silent fail | Mail queued & horizon not running | Start queue worker or use `sync` driver |
| Attachments missing | Resend API call not handling multipart | Implement attachment mapping in custom transport |

## Future Enhancements
- Branded Blade/Inertia email templates (Bangladesh-styled header/footer)
- Automated weekly health ping email using Resend
- Metrics dashboard (success rate, latency)

## Test Command Reference
We added an artisan command `mail:test-resend` for quick verification:
```
php artisan mail:test-resend
```
Outputs a success or failure message; errors logged to `storage/logs/laravel.log`.

---
Last Updated: 21 Nov 2025
