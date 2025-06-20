# API Progress Tracker

A professional Laravel package for tracking API development progress with task management and team collaboration features.

## 🚀 Features

- **API Route Discovery**: Automatically discover and track API endpoints
- **Progress Tracking**: Monitor development status with customizable priorities
- **Task Management**: Create, assign, and track development tasks
- **Team Collaboration**: Developer management with comment system
- **Data Persistence**: Uses separate database to survive migrations
- **Beautiful UI**: Modern, responsive interface built with Tailwind CSS and Alpine.js
- **File Attachments**: Support for file uploads in comments
- **Mention System**: Tag team members in comments with notifications
- **Dashboard Analytics**: Visual progress tracking with charts and statistics

## 📋 Requirements

- PHP 8.2+
- Laravel 12.0+
- Livewire 3.0+
- MySQL/PostgreSQL

## 🔧 Installation

### 1. Install via Composer

```bash
composer require gmrakibulhasan/api-progress-tracker
```

### 2. Environment Setup

Add to your `.env` file:

```env
# API Progress Tracker Database (separate from main app)
APIPT_DB_CONNECTION=mysql
APIPT_DB_HOST=127.0.0.1
APIPT_DB_PORT=3306
APIPT_DB_DATABASE=api_progress_tracker
APIPT_DB_USERNAME=root
APIPT_DB_PASSWORD=
```

### 3. Database Creation

```sql
CREATE DATABASE api_progress_tracker;
```

### 4. Run Installation

**Option A: Quick Install**
```bash
chmod +x vendor/gmrakibulhasan/api-progress-tracker/install.sh
./vendor/gmrakibulhasan/api-progress-tracker/install.sh
```

**Option B: Manual Install**
```bash
# Publish configuration
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider"

# Run migrations
php artisan migrate

# Sync API routes
php artisan api-progress:sync-routes

# Seed sample data (optional)
php artisan db:seed --class="Gmrakibulhasan\ApiProgressTracker\Database\Seeders\ApiProgressTrackerSeeder"
```

## 🎯 Usage

### Access Dashboard
Visit: `http://yourapp.com/api-progress`

**Default Admin Login:**
- Email: `admin@apipt.com`
- Password: `password`

### Available Commands

#### Sync API Routes
```bash
# Sync all API routes
php artisan api-progress:sync-routes

# Sync specific group
php artisan api-progress:sync-routes --group=users

# Force sync (update existing)
php artisan api-progress:sync-routes --force
```
- Beautiful Livewire-powered interface
- Automatic route discovery and tracking

## Installation

```bash
composer require gmrakibulhasan/api-progress-tracker
```

Publish and run migrations:

```bash
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider"
php artisan migrate
```

## Usage

Visit `/api-progress` in your browser to access the dashboard.

### Commands

Sync API routes:
```bash
php artisan api-progress:sync-routes
```

## Configuration

The package uses a separate database connection to prevent data loss during migrations. Configure your `.env`:

```env
APIPT_DB_CONNECTION=sqlite
#APIPT_DB_HOST=127.0.0.1
#APIPT_DB_PORT=3306
#APIPT_DB_DATABASE=api_progress_tracker
#APIPT_DB_USERNAME=root
#APIPT_DB_PASSWORD=
```

## License

MIT License
