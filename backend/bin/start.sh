#!/bin/sh

echo "Waiting for storage to become available..."

until php bin/migrate.php; do
    echo "Storage is not ready yet. Retrying in 2 seconds..."
    sleep 2
done

echo "Seeding database..."
php bin/seed.php

echo "Starting PHP API..."
exec php -S 0.0.0.0:8080 -t public