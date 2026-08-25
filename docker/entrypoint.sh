#!/bin/bash
# Container bootstrap: permissions, app key, migrations, seeders,
# production caches, then exec the image CMD.

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

wait_for_db() {
    log_info "Waiting for database connection..."

    local max_attempts=30
    local attempt=1

    while [ $attempt -le $max_attempts ]; do
        if php -r "
            try {
                \$pdo = new PDO(
                    'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
                    getenv('DB_USERNAME'),
                    getenv('DB_PASSWORD')
                );
                exit(0);
            } catch (Exception \$e) {
                exit(1);
            }
        " 2>/dev/null; then
            log_info "Database is ready!"
            return 0
        fi

        log_info "Attempt $attempt/$max_attempts - Database not ready, waiting..."
        sleep 2
        attempt=$((attempt + 1))
    done

    log_error "Database connection failed after $max_attempts attempts"
    return 1
}

setup_permissions() {
    log_info "Setting up file permissions..."

    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

    # Ensure log files exist and are writable
    touch /var/www/html/storage/logs/laravel.log
    chown www-data:www-data /var/www/html/storage/logs/laravel.log
    chmod 664 /var/www/html/storage/logs/laravel.log

    log_info "Permissions set"
}

generate_app_key() {
    # Real environment variable wins over the mounted .env file
    local current_key="$APP_KEY"

    if [ -z "$current_key" ] && [ -f .env ]; then
        current_key=$(grep -E '^APP_KEY=' .env | head -1 | cut -d= -f2- | tr -d '"' | tr -d "'")
    fi

    if [ -n "$current_key" ] && [ "$current_key" != "yourappkey" ] && [ "$current_key" != "base64:YOUR_GENERATED_KEY_HERE" ]; then
        log_info "Application key already set"
        return 0
    fi

    log_info "No application key found, generating..."
    if [ ! -w .env ] && [ -f .env ]; then
        log_error ".env is read-only but no APP_KEY provided. Set APP_KEY environment variable."
        exit 1
    fi
    php artisan key:generate --force --no-interaction
    log_info "Application key generated"
}

run_migrations() {
    log_info "Running database migrations..."

    if php artisan migrate --force --no-interaction; then
        log_info "Migrations completed"
    else
        log_error "Migration failed"
        return 1
    fi
}

run_seeders() {
    if [ "$APP_ENV" != "local" ] && [ "$APP_ENV" != "development" ] && [ "$RUN_SEEDERS" != "true" ]; then
        log_info "Skipping seeders (production environment)"
        return 0
    fi

    # Guard 1: marker file survives across container recreations
    if [ -f /var/www/html/storage/app/.seeded ]; then
        log_info "Seeders already ran previously, skipping"
        return 0
    fi

    # Guard 2: skip if the database already contains users
    local user_count
    user_count=$(php -r '
        try {
            $pdo = new PDO(
                sprintf("mysql:host=%s;port=%s;dbname=%s", getenv("DB_HOST"), getenv("DB_PORT"), getenv("DB_DATABASE")),
                getenv("DB_USERNAME"),
                getenv("DB_PASSWORD")
            );
            echo $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        } catch (Throwable $e) {
            echo "-1";
        }
    ' 2>/dev/null)

    if [ "$user_count" != "0" ] && [ "$user_count" != "-1" ]; then
        log_info "Database already contains $user_count users, skipping seeders"
        mkdir -p /var/www/html/storage/app
        touch /var/www/html/storage/app/.seeded
        return 0
    fi

    log_info "Running database seeders..."
    php artisan db:seed --force --no-interaction
    mkdir -p /var/www/html/storage/app
    touch /var/www/html/storage/app/.seeded
    log_info "Seeders completed"
}

optimize_laravel() {
    log_info "Optimizing Laravel..."

    php artisan config:clear --no-interaction 2>/dev/null || true
    php artisan route:clear --no-interaction 2>/dev/null || true
    php artisan view:clear --no-interaction 2>/dev/null || true
    php artisan cache:clear --no-interaction 2>/dev/null || true

    php artisan config:cache --no-interaction
    log_info "Config cached"

    php artisan route:cache --no-interaction
    log_info "Routes cached"

    php artisan view:cache --no-interaction
    log_info "Views cached"

    # Ignore failure: storage disk may be read-only in some setups
    php artisan storage:link --no-interaction 2>/dev/null || true
    log_info "Storage link created"
}

main() {
    log_info "Starting Laravel Docker container..."

    cd /var/www/html

    setup_permissions

    wait_for_db

    generate_app_key

    run_migrations

    run_seeders

    if [ "$APP_ENV" = "production" ]; then
        optimize_laravel
    else
        log_info "Development mode - skipping optimization caches"
        php artisan config:clear --no-interaction 2>/dev/null || true
        php artisan route:clear --no-interaction 2>/dev/null || true
        php artisan view:clear --no-interaction 2>/dev/null || true
    fi

    log_info "Container initialization complete. Starting services..."

    exec "$@"
}

main "$@"