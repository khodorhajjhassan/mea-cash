#!/usr/bin/env bash

set -Eeuo pipefail

APP_DIR="${APP_DIR:-/var/www/mea-cash}"
BRANCH="${BRANCH:-master}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
NODE_BUILD="${NODE_BUILD:-true}"
LOCK_FILE="${LOCK_FILE:-/tmp/mea-cash-deploy.lock}"
SKIP_GIT_PULL="${SKIP_GIT_PULL:-false}"

APP_WENT_DOWN=0

if ! command -v "$PHP_BIN" >/dev/null 2>&1; then
  echo "PHP binary not found: $PHP_BIN"
  exit 1
fi

if ! command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
  echo "Composer binary not found: $COMPOSER_BIN"
  exit 1
fi

exec 9>"$LOCK_FILE"
if ! flock -n 9; then
  echo "Another deployment is already running."
  exit 1
fi

cleanup() {
  if [ "$APP_WENT_DOWN" -eq 1 ]; then
    "$PHP_BIN" artisan up || true
  fi
}
trap cleanup EXIT

echo "Starting deployment for branch '$BRANCH' in '$APP_DIR'..."
cd "$APP_DIR"

if [ ! -d .git ]; then
  echo "Directory is not a git repository: $APP_DIR"
  exit 1
fi

if [ "$SKIP_GIT_PULL" != "true" ]; then
  git fetch --prune origin
  git checkout "$BRANCH"
  git pull --ff-only origin "$BRANCH"
fi

"$COMPOSER_BIN" install --no-dev --prefer-dist --optimize-autoloader --no-interaction

if [ "$NODE_BUILD" = "true" ]; then
  if command -v "$NPM_BIN" >/dev/null 2>&1; then
    "$NPM_BIN" ci --no-audit --no-fund
    "$NPM_BIN" run build
  else
    echo "NPM not found; skipping frontend build."
  fi
fi

"$PHP_BIN" artisan down --render="errors::503" || true
APP_WENT_DOWN=1

"$PHP_BIN" artisan migrate --force
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan optimize
"$PHP_BIN" artisan storage:link || true
"$PHP_BIN" artisan queue:restart || true

"$PHP_BIN" artisan up
APP_WENT_DOWN=0

echo "Deployment completed successfully."
