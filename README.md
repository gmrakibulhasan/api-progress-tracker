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

**Option A: Using MySQL CLI**

```sql
CREATE DATABASE api_progress_tracker;
```

**Option B: Using Laravel Tinker**

```bash
php artisan tinker
DB::statement('CREATE DATABASE api_progress_tracker');
exit
```

**Option C: Using Database Management Tool**
Create a database named `api_progress_tracker` using phpMyAdmin, TablePlus, or your preferred tool.

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

# Run migrations on separate database
php artisan api-progress:migrate --fresh --seed

# Sync API routes
php artisan api-progress:sync-routes
```

## 🎯 Usage

### Access Dashboard

Visit: `http://yourapp.com/api-progress`

**Default Admin Login:**

- Email: `admin@apipt.com`
- Password: `password`

### Database Management

The package uses a **separate database connection** to isolate its data from your main application. This prevents conflicts and data loss during migrations.

#### Migration Commands

```bash
# Run migrations on separate database
php artisan api-progress:migrate

# Fresh migration with seeding
php artisan api-progress:migrate --fresh --seed

# Fresh migration only
php artisan api-progress:migrate --fresh
```

#### Database Configuration

The package automatically creates an `apipt` database connection using your `.env` settings:

```env
APIPT_DB_CONNECTION=mysql
APIPT_DB_HOST=127.0.0.1
APIPT_DB_PORT=3306
APIPT_DB_DATABASE=api_progress_tracker
APIPT_DB_USERNAME=root
APIPT_DB_PASSWORD=
```

> **Note:** The package migrations will NOT interfere with your main application's `migrate:fresh --seed` commands.

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

#### Validate Installation

```bash
# Check if everything is working correctly
php artisan api-progress:validate
```

This command will check:

- Database connection
- Table existence
- Model functionality
- Configuration
- Route registration

## 🎨 Features in Detail

### Dashboard

- Real-time progress tracking
- Visual analytics and charts
- API endpoint management
- Task assignment and tracking
- Comment system with file attachments

### API Management

- Automatic route discovery
- Manual API endpoint creation
- Priority and status tracking
- Developer assignment
- Progress monitoring

### Task Management

- Create and assign tasks
- Track completion status
- Comment system for collaboration
- File attachment support
- Mention system for notifications

## 🔧 Configuration

All configuration options are available in the published config file:

```bash
php artisan vendor:publish --provider="Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider" --tag="config"
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 Changelog

### v1.0.5

- Cleaned up README documentation
- Final package optimization and documentation improvements
- Enhanced installation validation and error handling

### v1.0.4

- Added separate database connection support
- Improved installation process with validation
- Enhanced error handling and user guidance
- Added comprehensive installation validation

### v1.0.3

- Fixed migration and seeding issues
- Added custom migration commands
- Improved package stability

## 🐛 Troubleshooting

### Database Connection Issues

1. Ensure the database exists:

   ```sql
   CREATE DATABASE api_progress_tracker;
   ```

2. Check your `.env` configuration
3. Run the validation command:
   ```bash
   php artisan api-progress:validate
   ```

### Migration Issues

If migrations fail, try:

```bash
php artisan api-progress:migrate --fresh --seed
```

## 📧 Support

For support, please open an issue on [GitHub](https://github.com/gmrakibulhasan/api-progress-tracker).

## License

MIT License
