# API Progress Tracker - Deployment Guide

## Quick Installation

### Step 1: Install via Composer

**Option A: One-Command Installation (Recommended)**

```bash
# In your Laravel project directory
composer config repositories.api-progress-tracker vcs https://github.com/gmrakibulhasan/api-progress-tracker
composer require gmrakibulhasan/api-progress-tracker:^1.0
```

**Option B: Manual Repository Configuration**

1. Edit your project's `composer.json` file:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/gmrakibulhasan/api-progress-tracker"
    }
  ],
  "require": {
    "gmrakibulhasan/api-progress-tracker": "^1.0"
  }
}
```

2. Then run:

```bash
composer update
```

**Option C: Development Version**

```bash
composer config repositories.api-progress-tracker vcs https://github.com/gmrakibulhasan/api-progress-tracker
composer require gmrakibulhasan/api-progress-tracker:dev-main
```

**Option D: From Packagist (Coming Soon)**

```bash
composer require gmrakibulhasan/api-progress-tracker
```

### Step 2: Environment Configuration

Add these variables to your `.env` file:

```env
# API Progress Tracker Database (separate from main app)
APIPT_DB_CONNECTION=mysql
APIPT_DB_HOST=127.0.0.1
APIPT_DB_PORT=3306
APIPT_DB_DATABASE=api_progress_tracker
APIPT_DB_USERNAME=root
APIPT_DB_PASSWORD=
```

### Step 3: Database Setup

Create a separate database for the package:

```sql
CREATE DATABASE api_progress_tracker;
```

### Step 4: Run Installation

```bash
# Make install script executable
chmod +x vendor/gmrakibulhasan/api-progress-tracker/install.sh

# Run installation
./vendor/gmrakibulhasan/api-progress-tracker/install.sh
```

OR manually:

```bash
# Publish configuration
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="config"

# Publish views (optional)
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="views"

# Run migrations
php artisan migrate

# Sync API routes
php artisan api-progress:sync-routes
```

## Usage

### Access Dashboard

Visit: `http://yourapp.com/api-progress`

### Default Login

- Email: `admin@apipt.com`
- Password: `password`

### Commands

#### Sync API Routes

```bash
php artisan api-progress:sync-routes
```

#### Sync with filters

```bash
php artisan api-progress:sync-routes --group=users
php artisan api-progress:sync-routes --force
```

## Configuration

### Database Connection

The package uses a separate database connection to prevent data loss during `php artisan migrate:fresh`. Configure in `config/api-progress-tracker.php`:

```php
'database' => [
    'connection' => env('APIPT_DB_CONNECTION', 'mysql'),
    'host' => env('APIPT_DB_HOST', '127.0.0.1'),
    'port' => env('APIPT_DB_PORT', '3306'),
    'database' => env('APIPT_DB_DATABASE', 'api_progress_tracker'),
    'username' => env('APIPT_DB_USERNAME', 'root'),
    'password' => env('APIPT_DB_PASSWORD', ''),
],
```

### Route Configuration

```php
'route' => [
    'prefix' => 'api-progress',
    'middleware' => ['web'],
    'name' => 'apipt.',
],
```

### File Upload Settings

```php
'uploads' => [
    'disk' => 'local',
    'path' => 'api-progress-tracker/attachments',
    'max_size' => 10240, // 10MB in KB
    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'],
],
```

## Features

### 1. API Progress Tracking

- Automatic route discovery and tracking
- Status management (todo, in_progress, issue, not_needed, complete)
- Priority levels (low, normal, high, urgent)
- Group-based organization

### 2. Task Management

- Create and assign tasks to developers
- Track progress and completion
- Priority and status management
- Comment system for collaboration

### 3. Developer Management

- Add team members
- Assign tasks and API endpoints
- Track activity and progress

### 4. Comment System

- Add comments to API endpoints and tasks
- File attachments support
- Mention system with notifications
- Threaded replies

### 5. Dashboard & Analytics

- Visual progress tracking
- Statistics and charts
- Recent activity feed
- Filter and search capabilities

## Data Persistence

The package uses a separate database connection to ensure data persists across:

- `php artisan migrate:fresh`
- `php artisan migrate:rollback`
- Main application database changes

## Customization

### Views

Publish and customize views:

```bash
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="views"
```

Views will be published to: `resources/views/vendor/api-progress-tracker/`

### Models

Extend the package models in your `config/api-progress-tracker.php`:

```php
'models' => [
    'developer' => App\Models\CustomDeveloper::class,
    'api_progress' => App\Models\CustomApiProgress::class,
    'task' => App\Models\CustomTask::class,
    'comment' => App\Models\CustomComment::class,
],
```

## Troubleshooting

### Database Connection Issues

1. Ensure the database exists
2. Check credentials in `.env`
3. Verify database server is running

### Route Conflicts

The package uses prefixed routes (`/api-progress`) to avoid conflicts. If you need to change the prefix:

```php
// config/api-progress-tracker.php
'route' => [
    'prefix' => 'your-custom-prefix',
    // ...
],
```

### Migration Issues

If you get migration errors:

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Run migrations again
php artisan migrate
```

### File Upload Issues

Check storage permissions:

```bash
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

## Security Considerations

1. **Database Separation**: Uses separate DB connection for security
2. **File Uploads**: Validates file types and sizes
3. **CSRF Protection**: All forms include CSRF tokens
4. **Middleware**: Configurable middleware for access control

## Support

For issues and questions:

- GitHub: https://github.com/gmrakibulhasan/api-progress-tracker
- Email: gmrakibul.17@gmail.com

## License

MIT License - feel free to use in your projects!
