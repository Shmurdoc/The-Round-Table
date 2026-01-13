# Deployment Issues — The Round Table

This document summarises why the repository is not ready for production deployment and lists recommended remediation steps. It is written in professional, senior-developer style for handoff to operations or a release engineer.

## High-level summary
- Project is a PHP/Laravel application (runtime required). GitHub Pages is a static-hosting service and cannot run PHP. Attempting to serve `The Table/public/` on GitHub Pages returns 404 because the Pages source does not contain static `index.html` files and PHP cannot execute.
- Several runtime, security, and operational gaps must be addressed before a safe production rollout.

## Immediate blockers
1. Static vs dynamic hosting mismatch
   - The application requires a PHP runtime, database, and background workers. GitHub Pages only serves static files; it is not suitable for the Laravel app in `The Table`.
   - Attempting to point Pages at `The Table/public/` will fail (404) unless a static site is produced and placed in `docs/` or a `gh-pages` branch.

2. Sensitive user-uploaded content in repository history
   - The repository contains `The Table/public/storage/` and KYC/uploaded files in history. Even if removed by `.gitignore`, those files may remain in Git history and pose legal/security risk.
   - Action: remove sensitive files from history using `git filter-repo` or BFG, rotate any keys that may have been leaked, and audit repository contributors/commits.

3. Secrets & environment management
   - App depends on `.env` values (APP_KEY, DB_*, NOWPAYMENTS keys, MAIL credentials, etc.). Ensure secrets are not committed and are provided to production via secure secret storage (host provider secrets, Vault, GitHub Actions secrets for CI/CD).
   - The `BROWSER-TESTING-GUIDE.md` and other docs reference `NOWPAYMENTS_API_KEY` and `NOWPAYMENTS_IPN_SECRET` — ensure these are never committed.

4. No production build/deployment pipeline for PHP app
   - There is no documented CI/CD for building the Laravel app, running migrations, seeding, or warming caches on deploy (only an Argon dashboard workflow found under argon-dashboard-master).
   - Action: add a deployment pipeline (GitHub Actions, Render/DO/App Platform integration, or other host-specific pipeline) that performs `composer install --no-dev`, `php artisan migrate --force`, `php artisan config:cache`, `php artisan route:cache`, `php artisan storage:link`, and health checks.

5. Missing production runtime configuration
   - `APP_KEY` must be generated and present in production `.env` (otherwise encrypted cookies and sessions break).
   - Database, mail, queue, and cache backends must be configured (MySQL/Postgres, Redis, SMTP provider). These are not configured by default.

6. Background jobs and scheduler
   - Laravel requires cron or a scheduler process (`php artisan schedule:run`) for scheduled tasks and a queue worker for asynchronous jobs. These are not documented for production deployment.

7. File permissions and storage
   - `storage/` and `bootstrap/cache` need correct ownership/permissions for the web user. Confirm on target host and document required user/group and chmod instructions.

8. No documented runbook for migrations, rollbacks, and DB backups
   - There is no documented migration/rollback/runbook or automated backups for the production database and uploaded assets.

9. Legal and compliance considerations
   - The repo includes KYC data and payment integrations. Confirm compliance with data protection regulations (GDPR/PDPA) and ensure personally identifiable information (PII) is handled and stored off-repo and encrypted at rest.

10. Lack of monitoring, alerts, and error reporting
    - No configuration for Sentry/monitoring, logging into a centralized store, or uptime checks.

## Specific findings (examples)
- `The Table` is a Laravel app: `artisan`, `composer.json`, `resources/views/*`, `routes/web.php`.
- Payment integration references: `app/Services/NOWPaymentsService.php` and docs require `NOWPAYMENTS_API_KEY` and `NOWPAYMENTS_IPN_SECRET`.
- `composer.json` contains a script that copies `.env.example` to `.env` if missing — make sure production `.env` is managed securely.
- `The Table/public/index.php` is a PHP entrypoint; GitHub Pages will not execute it.
- `argon-dashboard-master/pages/` contains static HTML and *can* be published by GitHub Pages if desired — but that is a separate, static frontend snapshot only.

## Recommended remediation checklist (ordered)
1. Decide hosting target
   - If you need dynamic, server-side functions (Laravel): choose a PHP-capable host (Render, DigitalOcean App Platform, AWS Elastic Beanstalk, Hetzner, VPS with deploy pipeline, or Laravel Vapor for serverless).
   - If you only need to publish static docs or a frontend snapshot, publish `argon-dashboard-master/pages` or a generated static export to `docs/` or `gh-pages` branch.

2. Remove sensitive files from Git history
   - Use `git filter-repo` or BFG to remove `The Table/public/storage/*` and any committed `.env` or key files from history.
   - Rotate any keys that were potentially exposed.

3. Secure secrets and environment
   - Store secrets in host-provided secret manager or HashiCorp Vault. Never commit `.env` to the repo.
   - Add documentation on required env variables and example values in `The Table/.env.example` (already present) — do not include secrets.

4. Implement CI/CD deployment workflow for chosen host
   - Example steps for GitHub Actions → server deploy: run unit tests, run `composer install --no-dev --optimize-autoloader`, build assets (`npm ci && npm run build`) if needed, run migrations, cache config/routes, and run restart commands.
   - Use `secrets` to supply SSH keys, API tokens, or provider credentials.

5. Prepare production configuration tasks
   - Generate `APP_KEY` (via `php artisan key:generate --show`) and set it in production secrets.
   - Configure DB, cache, mail, queues, and storage credentials.
   - Configure `APP_ENV=production`, `APP_DEBUG=false`, and strong log levels.

6. Set up persistent storage and backups
   - Uploaded files must live on persistent storage (S3-compatible or host volume) with backups and lifecycle rules.
   - Implement DB backups (daily snapshots) and test restore procedures.

7. Add operational runbook and monitoring
   - Document deploy rollback, migrations, and emergency steps.
   - Configure application monitoring (Sentry), alerts (PagerDuty, or similar), and uptime checks.

8. Add security hardening
   - Validate input handling, enable rate limiting on sensitive endpoints, enforce HTTPS, HSTS, secure cookies, and strong CSP headers.

9. Test (staging) and load tests
   - Deploy to a staging environment and run automated test suites and basic load tests before production.

## Quick fix for the 404 on GitHub Pages
- If you only want a quick public landing page while deciding production hosting, you can publish the static Argon demo at `argon-dashboard-master/pages/` to GitHub Pages. Create a GitHub Actions workflow that copies `argon-dashboard-master/pages` into the Pages artifact (or `docs/`) and deploys using `actions/configure-pages` / `actions/upload-pages-artifact` / `actions/deploy-pages`.
- Do NOT attempt to publish `The Table/public` directly to Pages unless you first export a static HTML version of the frontend.

## Example minimal checklist to get a safe Laravel production deployment
- [ ] Remove sensitive files from git history.
- [ ] Choose a PHP-capable host and create an infra plan (DB, storage, DNS, HTTPS).
- [ ] Add CI/CD workflow that runs tests and deploys built artifact to host.
- [ ] Add secrets to host secret store; ensure `.env` is only created on the server from secrets.
- [ ] Run migrations and verify data integrity in staging.
- [ ] Configure backups, monitoring, and alerting.

---

If you want, I will:
- create this report file in the repo (done),
- commit and push it (I will do that now), and
- optionally create a GitHub Actions workflow to publish `argon-dashboard-master/pages` to GitHub Pages so the repository has a working static site while you plan full deployment.

If you want me to proceed with the Pages workflow or prepare a production deploy pipeline for Laravel (e.g., using Render or DigitalOcean), tell me which option to implement next.
