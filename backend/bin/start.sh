set -e

echo "Running database migrations..."
php bin/migrate.php

echo "Seeding database..."
php bin/seed.php

echo "Starting PHP API..."
exec php -S 0.0.0.0:8080 -t public
