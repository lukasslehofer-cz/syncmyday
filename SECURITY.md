# Security Policy

## 🔒 Reporting Security Issues

If you discover a security vulnerability, please email: **support@syncmyday.cz**

Do NOT create a public GitHub issue for security vulnerabilities.

## ⚠️ Important Security Notes

### Never Commit Real Credentials

**NEVER** commit these types of files with real credentials:
- `.env` files
- Configuration files with real passwords
- API keys or secrets
- Database credentials
- SMTP passwords

### Files to Keep Private

Add to `.gitignore`:
```
.env
.env.local
.env.production
env-configs/*-real.txt
env-configs/*-prod.txt
*.credentials
*.secrets
```

### Safe Documentation Files

These files contain **placeholders only** and are safe to commit:
- `env-configs/*.txt.example`
- `env-configs/mxroute-only.txt` (contains "tvoje_heslo_zde" placeholders)
- `env-configs/MAILGUN_BACKUP.md` (backup documentation)
- `MIGRATION_SUMMARY.md`

### GitGuardian Configuration

We use `.gitguardian.yaml` to prevent false positives on documentation files with placeholder credentials.

## 🚨 If You Accidentally Commit Credentials

1. **Immediately change/revoke the exposed credentials**
2. Remove the file from Git:
   ```bash
   git rm --cached path/to/file
   echo "path/to/file" >> .gitignore
   git commit -m "Security: Remove exposed credentials"
   ```
3. Clean Git history (optional but recommended):
   ```bash
   git filter-repo --path path/to/file --invert-paths
   git push origin --force --all
   ```
4. Report to GitHub Security if needed

## ✅ Best Practices

### Development
- Use `.env.example` with placeholders
- Use Mailtrap or similar for email testing
- Never use production credentials in development

### Production
- Store credentials in environment variables
- Use server-side `.env` file (never in Git)
- Rotate credentials regularly
- Use strong, unique passwords

### CI/CD
- Store secrets in GitHub Secrets or similar
- Never echo/print secrets in logs
- Use secret scanning tools

## 🛡️ Security Features

### Rate Limiting
- Email sending: 300 emails/hour per mailbox
- Prevents abuse and ensures MXroute compliance

### CSRF Protection
- Laravel CSRF tokens on all forms
- API endpoints protected

### Authentication
- OAuth 2.0 for Google/Microsoft
- Secure password hashing (bcrypt)
- Email verification required

## 📋 Regular Security Checklist

- [ ] All `.env` files in `.gitignore`
- [ ] Production credentials rotated quarterly
- [ ] Dependencies updated (`composer update`, `npm update`)
- [ ] GitGuardian alerts reviewed
- [ ] No exposed API keys in code
- [ ] HTTPS enabled on production
- [ ] Security headers configured
- [ ] Database backups encrypted

## 🔍 Tools We Use

- **GitGuardian** - Secret scanning
- **Dependabot** - Dependency updates
- **Laravel Security** - Framework security features

---

Last updated: 2026-01-07

