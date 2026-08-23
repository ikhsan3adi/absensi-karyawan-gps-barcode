#!/bin/bash
# =============================================================================
# Docker Entrypoint for Laravel Application
# =============================================================================
# Handles: permissions, migrations, cache optimization, key generation
# =============================================================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# =============================================================================
# Wait for database to be ready
# =============================================================================
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

# =============================================================================
# Set up file permissions
# =============================================================================
setup_permissions() {
    log_info "Setting up file permissions..."

    # Storage and cache directories need write access
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

    # Ensure log files exist and are writable
    touch /var/www/html/storage/logs/laravel.log
    chown www-data:www-data /var/www/html/storage/logs/laravel.log
    chmod 664 /var/www/html/storage/logs/laravel.log

    log_info "Permissions set"
}

# =============================================================================
# Generate application key if not set
# =============================================================================
generate_app_key() {
    if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "yourappkey" ] || [ "$APP_KEY" = "" ]; then
        log_info "Generating application key..."
        php artisan key:generate --force --no-interaction
        log_info "Application key generated"
    else
        log_info "Application key already set"
    fi
}

# =============================================================================
# Run database migrations
# =============================================================================
run_migrations() {
    log_info "Running database migrations..."

    if php artisan migrate --force --no-interaction; then
        log_info "Migrations completed"
    else
        log_error "Migration failed"
        return 1
    fi
}

# =============================================================================
# Run seeders (only in non-production or if forced)
# =============================================================================
run_seeders() {
    if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ] || [ "$RUN_SEEDERS" = "true" ]; then
        log_info "Running database seeders..."
        php artisan db:seed --force --no-interaction
        log_info "Seeders completed"
    else
        log_info "Skipping seeders (production environment)"
    fi
}

# =============================================================================
# Optimize Laravel for production
# =============================================================================
optimize_laravel() {
    log_info "Optimizing Laravel..."

    # Clear existing caches first
    php artisan config:clear --no-interaction 2>/dev/null || true
    php artisan route:clear --no-interaction 2>/dev/null || true
    php artisan view:clear --no-interaction 2>/dev/null || true
    php artisan cache:clear --no-interaction 2>/dev/null || true

    # Cache configuration
    php artisan config:cache --no-interaction
    log_info "Config cached"

    # Cache routes
    php artisan route:cache --no-interaction
    log_info "Routes cached"

    # Cache views
    php artisan view:cache --no-interaction
    log_info "Views cached"

    # Optimize autoloader
    composer dump-autoload --optimize --classmap-authoritative --no-dev --no-interaction 2>/dev/null || true
    log_info "Autoloader optimized"

    # Create storage link if not exists
    php artisan storage:link --no-interaction 2>/dev/null || true
    log_info "Storage link created"
}

# =============================================================================
# Main execution
# =============================================================================
main() {
    log_info "Starting Laravel Docker container..."

    # Change to working directory
    cd /var/www/html

    # Setup permissions first
    setup_permissions

    # Wait for database
    wait_for_db

    # Generate app key
    generate_app_key

    # Run migrations
    run_migrations

    # Run seeders (conditional)
    run_seeders

    # Optimize for production
    if [ "$APP_ENV" = "production" ]; then
        optimize_laravel
    else
        log_info "Development mode - skipping optimization caches"
        php artisan config:clear --no-interaction 2>/dev/null || true
        php artisan route:clear --no-interaction 2>/dev/null || true
        php artisan view:clear --no-interaction 2>/dev/null || true
    fi

    log_info "Container initialization complete. Starting services..."

    # Execute the main command (supervisord)
    exec "$@"
}

# Run main with all arguments
main "$@"