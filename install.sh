#!/bin/bash

echo "Installing API Progress Tracker Package..."

# Install composer dependencies
echo "Installing composer dependencies..."
composer install

# Run migrations on separate database
echo "Running migrations on separate database..."
php artisan api-progress:migrate --fresh --seed

# Publish package assets
echo "Publishing package assets..."
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="config"
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="views"
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="assets"

# Sync initial routes (after migrations complete)
echo "Syncing API routes..."
php artisan api-progress:sync-routes

# Validate installation
echo "Validating installation..."
php artisan api-progress:validate

echo "Installation completed successfully!"
echo "You can now access the dashboard at: /api-progress"
echo "Admin login: admin@apipt.com / password"
