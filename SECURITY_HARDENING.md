# BideshGomon Security Hardening Guide

Last Updated: 21 Nov 2025
Environment: Laravel 12 · Vue 3 · Inertia · MySQL · VPS (Linux)

---
## 1. Critical Principles
- Least Privilege: Separate deploy user, never develop as root.
- Zero Trust for Secrets: Anything pasted in chat is rotated.
- Immutable Builds: Ship built assets, not ad‑hoc edits on production.
- Defense in Depth: Layered controls (network, OS, app, data, monitoring).
- Auditability: Every privileged action leaves a trace.

---
## 2. Immediate High-Impact Actions (Quick Checklist)
| Item | Status | Command / Notes |
|------|--------|-----------------|
| Disable root password SSH | ☐ | In `/etc/ssh/sshd_config`: `PermitRootLogin prohibit-password` |
| Enforce SSH keys only | ☐ | `PasswordAuthentication no` |
| Create non-root deploy user | ☐ | `adduser deploy && usermod -aG sudo deploy` |
| Firewall allow 22,80,443 only | ☐ | `ufw allow 22 && ufw allow 80 && ufw allow 443 && ufw enable` |
| Install fail2ban | ☐ | `apt install fail2ban` (use default jail then tune) |
| Rotate all leaked/suspect secrets | ☐ | TWILIO, MAIL, DB, etc. |
| Force HTTPS (redirect HTTP→HTTPS) | ☐ | Nginx server block 301 redirect |
| Enable automatic security updates | ☐ | `apt install unattended-upgrades` |
| Restrict storage & cache perms | ☐ | `chown -R www-data:www-data storage bootstrap/cache` |
| Remove debug logs with PII | ☐ | Search and purge verbose profile logs |

---
## 3. SSH Hardening
1. Generate key locally (macOS):
```bash
ssh-keygen -t ed25519 -C "deploy@bideshgomon"
```
2. Copy public key:
```bash
ssh-copy-id deploy@SERVER_IP
```
3. Edit `/etc/ssh/sshd_config`:
```
PermitRootLogin prohibit-password
PasswordAuthentication no
ChallengeResponseAuthentication no
UsePAM yes
AllowUsers deploy
```
4. Restart:
```bash
sudo systemctl restart sshd
```
5. Test new session before closing existing shell.

---
## 4. OS & Runtime Hardening
- Packages: `apt update && apt upgrade -y` weekly (or unattended upgrades).
- Remove unused services: `systemctl disable --now apache2` (if only using Nginx).
- Timezone: Set to Asia/Dhaka for log consistency.
- Swap: Avoid excessive swap thrashing for MySQL performance (monitor with `vmstat 5`).

---
## 5. Application Layer
### Environment Secrets
- File: `.env` (never committed). Permissions:
```bash
chmod 640 /var/www/bideshgomon/.env
chown deploy:www-data /var/www/bideshgomon/.env
```
- Rotate if exposed: issue new Twilio tokens, revoke old.

### Laravel Config
- `APP_DEBUG=false` in production.
- Use `LOG_CHANNEL=stack`, avoid logging full request bodies.
- Enable cache optimizations post deploy:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
(Ensure changed routes trigger `php artisan ziggy:generate` first.)

### File Uploads & Storage
- Validate MIME & file size.
- Store under `storage/app/public/*` only; never execute user uploads.
- Use ClamAV (optional) for scanning high-risk files.

### Queue / Scheduler
- Run queue worker as non-root service user.
- Supervisor config restricts environment variables to necessary.

---
## 6. Database Security
- Unique DB user with only required privileges:
```sql
CREATE USER 'bgomon'@'%' IDENTIFIED BY 'STRONG_PASSWORD';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, INDEX, ALTER ON bideshgomon.* TO 'bgomon'@'%';
FLUSH PRIVILEGES;
```
- Enforce strong passwords; consider TLS for remote DB.
- Regular backups:
```bash
mysqldump --single-transaction bideshgomon | gzip > /backups/bideshgomon_$(date +%F).sql.gz
```
- Test restore quarterly.

---
## 7. Network & Firewall
Using ufw:
```bash
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
ufw status verbose
```
Optional: Rate limit SSH `ufw limit 22/tcp`.

---
## 8. Monitoring & Alerting
- Fail2ban jails: sshd, nginx-badbots.
- Log tail script (non-root) for anomalies (`grep -i unauthorized`).
- Set up basic uptime monitoring (Pingdom / Healthchecks.io for cron tasks).
- Consider OSSEC / Wazuh for host intrusion detection if scale grows.

---
## 9. Logging & PII Hygiene
Remove temporary debug lines (names, passport full strings). Pattern to search:
```bash
grep -R "Profile edit returning" -n app/ resources/
```
Redact sensitive error contexts; keep only: timestamp, user_id, action, status.
Retention: Rotate logs weekly; compress old logs.

---
## 10. Dependency Vulnerability Management
- Weekly:
```bash
composer update --dry-run
composer audit
npm audit --production
```
- Patch high severity immediately; document in CHANGELOG.
- Pin versions in `composer.json` and `package.json` to avoid accidental major jumps.

---
## 11. Frontend Build Integrity
- `npm ci` for reproducible builds.
- Build on CI, produce artifact tarball, deploy artifact (not running full npm on prod if possible).
- Verify hash of built `/public/build/manifest.json` after upload.

---
## 12. Incident Response Mini-Runbook
| Scenario | Immediate Action | Follow-Up |
|----------|------------------|-----------|
| Secret leak | Rotate secret, invalidate sessions | Post-mortem & prevention patch |
| Unauthorized login | Lock account, force password reset | Audit access logs |
| Ransomware risk | Restore from clean backup | Harden & scan host |
| Data corruption | Switch to read-only, restore backup | Integrity checks |

Keep a 24h / 7d / 30d backup chain.

---
## 13. Staging Environment Pattern
- Mirror production packages.
- Use anonymized data (strip NID, phone numbers, names).
- Performance tests & debug logs only in staging.

---
## 14. Optional Advanced Hardening
- Application WAF (Cloudflare / Nginx ModSecurity) for common attack pattern filtering.
- mTLS between services if you introduce microservices later.
- Secrets manager (HashiCorp Vault or AWS Secrets Manager) if moving to multi-host.

---
## 15. Ongoing Tasks (Suggested Cadence)
| Frequency | Task |
|-----------|------|
| Daily | Review logs for anomalies |
| Weekly | Dependency audit & minor updates |
| Monthly | Backup restore test |
| Quarterly | Full security review & threat model refresh |
| After Deploy | Clear caches only if config changed; verify health endpoints |

---
## 16. Removal Checklist (After Debugging)
- Delete verbose `Log::info` lines with profile data.
- Remove console.log lines in Vue components.
- Confirm no secrets or tokens ever appeared in repo history (`git log -p | grep -i TWILIO`).

---
## 17. Quick Verification Script (Optional)
Create `scripts/security-scan.sh` (future) to automate top checks: permissions, firewall, env flags.

---
## 18. Summary
Implement SSH key-only access, rotate any exposed secrets, lock down services, sanitize logs, and institutionalize regular auditing. This guide lives with the codebase—update it whenever architecture or threat landscape changes.

---
*End of Document*
