# Master Branch CI/CD (Production)

This project now has a GitHub Actions workflow that deploys every push to `master` to:

- Server path: `/var/www/mea-cash`
- Branch: `master`
- Workflow file: `.github/workflows/deploy-master.yml`

## Required GitHub Secrets

Set these in your repository settings (`Settings` -> `Secrets and variables` -> `Actions`):

- `PROD_HOST`: server IP or domain
- `PROD_USER`: SSH user (must have access to `/var/www/mea-cash`)
- `PROD_SSH_KEY`: private key for that user
- `PROD_PORT` (optional): SSH port (defaults to `22`)

## What Deployment Runs

The workflow calls `scripts/deploy-production.sh`, which performs:

1. Pull latest `master`
2. `composer install --no-dev --prefer-dist --optimize-autoloader`
3. `npm ci` + `npm run build` (if npm exists)
4. `php artisan down`
5. `php artisan migrate --force`
6. `php artisan optimize:clear` + `php artisan optimize`
7. `php artisan storage:link` (safe, ignored if already linked)
8. `php artisan queue:restart`
9. `php artisan up`

It also uses a lock file (`/tmp/mea-cash-deploy.lock`) to prevent concurrent deployments.

## Manual Trigger

You can manually trigger deployment from:

- GitHub -> `Actions` -> `Deploy Master` -> `Run workflow`
