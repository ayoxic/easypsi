# EasyPsi Deployment Checklist

## 1. Environment

- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Set `APP_URL` to your real domain
- Run `php artisan key:generate` if `APP_KEY` is empty

## 2. Database

- Create the production database
- Fill `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Run:

```bash
php artisan migrate --force
```

## 3. Email

- Fill `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- Set `MAIL_MAILER=smtp`
- Set a valid `MAIL_FROM_ADDRESS`
- Test:

```bash
php artisan tinker
```

Then send a test notification or register a test account.

## 4. Storage

- Run:

```bash
php artisan storage:link
```

- Make sure `storage/` and `bootstrap/cache/` are writable

## 5. Sessions and Security

- Use:
  - `SESSION_DRIVER=database`
  - `SESSION_SECURE_COOKIE=true`
  - `SESSION_HTTP_ONLY=true`
  - `SESSION_SAME_SITE=lax`
- Enable HTTPS on the domain
- Keep `APP_DEBUG=false`
- Use a strong database password
- Use a strong SMTP password

## 6. Cache and Optimization

Run after environment setup:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 7. Queue and Cron

If you use queued emails or jobs later:

- Set a real `QUEUE_CONNECTION`
- Start a worker:

```bash
php artisan queue:work
```

If scheduled tasks are added later, configure cron for:

```bash
php artisan schedule:run
```

## 8. reCAPTCHA

- Fill:
  - `RECAPTCHA_SITE_KEY`
  - `RECAPTCHA_SECRET_KEY`
- Test register page in production

## 9. Final Manual Tests

- Home page loads
- Login works
- Register works
- Verification email is received
- Student can open `/fr/teachers`
- Teacher can open `/fr/teacher-space`
- Admin can open `/fr/admin`
- Premium redirect goes to payment page
- YouTube videos open correctly
- Support files download correctly
- Arabic, French, and English pages load without broken text

## 10. Recommended First Deploy Order

1. Upload project
2. Configure `.env`
3. Import or create database
4. Run migrations
5. Run `storage:link`
6. Run cache commands
7. Test email
8. Test student, teacher, and admin flows
