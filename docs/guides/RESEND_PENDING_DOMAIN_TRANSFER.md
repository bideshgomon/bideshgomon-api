# Resend Integration - Pending Domain Transfer

## Current Status
- **Resend API Key**: Configured in production `.env` (`RESEND_API_KEY=re_Ufby9nT9_2M9qPPmStspGixbhairKe7R5`)
- **Resend PHP SDK**: Installed (`resend/resend-php` v1.0.1)
- **Test Command**: Available (`php artisan mail:test-resend`)
- **Blocker**: Domain `bideshgomon.com` is in transfer mode—cannot add/modify DNS records yet

## Current Mail Configuration (Production)
```
MAIL_MAILER=smtp                          # Using Gmail SMTP as temporary solution
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=bideshgomon@gmail.com
MAIL_PASSWORD=zkswhpnztfohnocs            # App password (rotate quarterly)
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME=BideshGomon

RESEND_API_KEY=re_Ufby9nT9_2M9qPPmStspGixbhairKe7R5  # Ready when domain verified
```

## Steps to Complete After Domain Transfer (When DNS Available)

### 1. Add Domain to Resend Dashboard
- Log in: https://resend.com/domains
- Click "Add Domain" → Enter `bideshgomon.com`
- Resend will provide DNS records (similar to below—use exact values from dashboard):

### 2. Required DNS Records (Examples—Replace with Actual Resend Values)
Add these records in your DNS provider (Cloudflare, cPanel, Hostinger, etc.):

| Type  | Name/Host                | Value/Target                          | TTL  |
|-------|--------------------------|---------------------------------------|------|
| TXT   | @                        | `v=spf1 include:spf.resend.com ~all`  | 3600 |
| CNAME | `selector1._domainkey`   | `selector1.domainkey.resend.com`      | 3600 |
| CNAME | `selector2._domainkey`   | `selector2.domainkey.resend.com`      | 3600 |
| CNAME | `rp._domainkey`          | `rp.domainkey.resend.com`             | 3600 |
| TXT   | `_dmarc`                 | `v=DMARC1; p=none; rua=mailto:dmarc@bideshgomon.com` | 3600 |

**Note**: Resend may provide different selector names or additional records. Always use their exact values.

### 3. Verify Domain in Resend
- Wait 5-30 minutes for DNS propagation (check with `dig TXT bideshgomon.com` or https://mxtoolbox.com)
- Click "Verify" button in Resend dashboard
- All checks should turn green ✅

### 4. Update Production `.env` (After Verification)
```bash
# SSH to VPS
ssh root@148.135.136.95
cd /var/www/bideshgomon

# Edit .env
nano .env

# Change these lines:
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=no-reply@bideshgomon.com
MAIL_FROM_NAME="BideshGomon"

# Keep Gmail SMTP settings commented for emergency fallback:
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=bideshgomon@gmail.com
# MAIL_PASSWORD=zkswhpnztfohnocs

# Save and exit (Ctrl+X, Y, Enter)
```

### 5. Clear Config & Test
```bash
php artisan config:clear
php artisan config:cache
php artisan mail:test-resend
```

Expected output:
```
Dispatched test email to no-reply@bideshgomon.com via mailer: resend
```

### 6. Verify Real Password Reset Flow
- Go to: http://148.135.136.95/forgot-password
- Enter `mahidulislamnakib@gmail.com`
- Check inbox for reset email
- Click link and confirm password reset works

### 7. Monitor Resend Dashboard
- Check delivery stats: https://resend.com/emails
- Review any bounces/complaints
- Set up webhooks (optional) for real-time delivery tracking

## Emergency Rollback to Gmail
If Resend has issues after switching:
```bash
# Edit .env
MAIL_MAILER=smtp  # Switch back to Gmail

# Clear config
php artisan config:clear && php artisan config:cache
```

## Next Steps (Checklist)
- [ ] Wait for domain transfer to complete
- [ ] Gain access to DNS management panel
- [ ] Add domain to Resend dashboard
- [ ] Copy exact DNS records from Resend
- [ ] Add records to DNS provider
- [ ] Wait for DNS propagation (~5-30 min)
- [ ] Click "Verify" in Resend dashboard
- [ ] Update production `.env` (MAIL_MAILER=resend, FROM_ADDRESS)
- [ ] Clear config cache
- [ ] Run `php artisan mail:test-resend`
- [ ] Test forgot password flow end-to-end
- [ ] Archive this document once complete

## Timeline Estimate
- **Domain transfer**: Typically 5-7 days (varies by registrar)
- **DNS propagation after adding records**: 5-30 minutes
- **Total setup time after transfer**: ~30 minutes

## Contact Info
- Resend support: https://resend.com/support
- Domain registrar: (Check your transfer confirmation email for support contacts)

---
**Created**: 21 Nov 2025  
**Status**: ⏸️ Paused (waiting for domain transfer completion)  
**Last Updated**: 21 Nov 2025
