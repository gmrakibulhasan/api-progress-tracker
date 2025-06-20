#!/bin/bash

echo "Installing API Progress Tracker Package..."

# Install composer dependencies
echo "Installing composer dependencies..."
composer install

# Create the database for the package (separate from main app)
echo "Setting up database..."
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS api_progress_tracker;"

# Run migrations
echo "Running migrations..."
php artisan migrate --path=vendor/gmrakibulhasan/api-progress-tracker/database/migrations

# Publish package assets
echo "Publishing package assets..."
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="config"
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="views"
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="assets"

# Create initial admin user
echo "Creating initial admin user..."
php artisan tinker --execute="
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper;
use Illuminate\Support\Facades\Hash;
if (!ApiptDeveloper::where('email', 'admin@apipt.com')->exists()) {
    ApiptDeveloper::create([
        'name' => 'Admin User',
        'email' => 'admin@apipt.com',
        'password' => Hash::make('password')
    ]);
    echo 'Admin user created with email: admin@apipt.com and password: password';
} else {
    echo 'Admin user already exists';
}
"

# Sync initial routes
echo "Syncing API routes..."
php artisan api-progress:sync-routes

echo "Installation completed successfully!"
echo "You can now access the dashboard at: /api-progress"
echo "Admin login: admin@apipt.com / password"
