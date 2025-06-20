<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | This package uses a separate database connection to prevent data loss
    | during migrations. Configure these values in your .env file.
    |
    */
    'database' => [
        'connection' => env('APIPT_DB_CONNECTION', 'mysql'),
        'host' => env('APIPT_DB_HOST', '127.0.0.1'),
        'port' => env('APIPT_DB_PORT', '3306'),
        'database' => env('APIPT_DB_DATABASE', 'api_progress_tracker'),
        'username' => env('APIPT_DB_USERNAME', 'root'),
        'password' => env('APIPT_DB_PASSWORD', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the route settings for the API Progress Tracker dashboard.
    |
    */
    'route' => [
        'prefix' => 'api-progress',
        'middleware' => ['web'],
        'name' => 'apipt.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models Configuration
    |--------------------------------------------------------------------------
    |
    | Configure model settings and table prefixes to avoid conflicts.
    |
    */
    'models' => [
        'table_prefix' => 'apipt_',
        'developer' => \Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper::class,
        'api_progress' => \Gmrakibulhasan\ApiProgressTracker\Models\ApiptApiProgress::class,
        'task' => \Gmrakibulhasan\ApiProgressTracker\Models\ApiptTask::class,
        'comment' => \Gmrakibulhasan\ApiProgressTracker\Models\ApiptComment::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure file upload settings for comment attachments.
    |
    */
    'uploads' => [
        'disk' => 'local',
        'path' => 'api-progress-tracker/attachments',
        'max_size' => 10240, // 10MB in KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Priority Levels
    |--------------------------------------------------------------------------
    |
    | Define the available priority levels for tasks and API progress items.
    |
    */
    'priorities' => [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    |
    | Define the available status options for tasks and API progress items.
    |
    */
    'statuses' => [
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'issue' => 'Issue',
        'not_needed' => 'Not Needed',
        'complete' => 'Complete',
    ],
];
