#!/bin/bash

echo "Installing API Progress Tracker Package..."

# Install composer dependencies
echo "Installing composer dependencies..."
composer install

# Setup database (if MySQL is available)
echo "Setting up database..."
if command -v mysql &> /dev/null; then
    mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS api_progress_tracker;"
    echo "Database created successfully!"
else
    echo "MySQL command not found. Please ensure the database exists:"
    echo "CREATE DATABASE api_progress_tracker;"
fi

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

echo "Installation completed successfully!"
echo "You can now access the dashboard at: /api-progress"
echo "Admin login: admin@apipt.com / password"
