#!/usr/bin/env sh
set -e

# Entrypoint for Docker image. Runs optional migrations and cache commands,
# ensures permissions, then runs the main process.

APP_DIR=/var/www/html

if [ -d "$APP_DIR" ]; then
  echo "Setting ownership of storage and bootstrap/cache to www-data"
  chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" || true
fi

# If the caller has requested migrations (RUN_MIGRATIONS=true) and we're not in testing
if [ "${RUN_MIGRATIONS:-false}" = "true" ] && [ "${APP_ENV:-production}" != "testing" ]; then
  echo "Running migrations..."
  cd "$APP_DIR"
  # Wait for DB to be ready (best effort)
  if [ -n "$DB_HOST" ]; then
    echo "Waiting for database host ${DB_HOST}:${DB_PORT:-3306}"
    if command -v nc >/dev/null 2>&1; then
      for i in $(seq 1 20); do
        nc -z "$DB_HOST" ${DB_PORT:-3306} && break
        echo "Waiting for DB... ($i)"; sleep 2
      done
    fi
  fi
  php artisan migrate --force || echo "Migrations failed or no DB available"
fi

# Cache config and routes for better performance (no-op in dev)
if [ "${APP_ENV:-production}" != "local" ]; then
  cd "$APP_DIR"
  php artisan config:cache || true
  php artisan route:cache || true
fi

echo "Starting process: $@"
exec "$@"
