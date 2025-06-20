<?php

namespace Gmrakibulhasan\ApiProgressTracker\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptApiProgress;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptTask;

class ApiProgressTrackerSeeder extends Seeder
{
    public function run()
    {
        // Create sample developers
        $admin = ApiptDeveloper::firstOrCreate(
            ['email' => 'admin@apipt.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password')
            ]
        );

        $developer1 = ApiptDeveloper::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password')
            ]
        );

        $developer2 = ApiptDeveloper::firstOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password')
            ]
        );

        // Create sample API progress entries
        $sampleApis = [
            [
                'method' => 'GET',
                'endpoint' => '/api/users',
                'group_name' => 'User Management',
                'description' => 'Get all users',
                'priority' => 'high',
                'status' => 'complete'
            ],
            [
                'method' => 'POST',
                'endpoint' => '/api/users',
                'group_name' => 'User Management',
                'description' => 'Create new user',
                'priority' => 'high',
                'status' => 'in_progress'
            ],
            [
                'method' => 'GET',
                'endpoint' => '/api/products',
                'group_name' => 'Product Management',
                'description' => 'Get all products',
                'priority' => 'normal',
                'status' => 'todo'
            ],
            [
                'method' => 'POST',
                'endpoint' => '/api/orders',
                'group_name' => 'Order Management',
                'description' => 'Create new order',
                'priority' => 'urgent',
                'status' => 'issue'
            ]
        ];

        foreach ($sampleApis as $apiData) {
            ApiptApiProgress::firstOrCreate(
                ['method' => $apiData['method'], 'endpoint' => $apiData['endpoint']],
                $apiData
            );
        }

        // Create sample tasks
        $sampleTasks = [
            [
                'title' => 'Setup API Documentation',
                'description' => 'Create comprehensive API documentation using Swagger',
                'assigned_by' => $admin->id,
                'priority' => 'high',
                'status' => 'in_progress'
            ],
            [
                'title' => 'Implement Rate Limiting',
                'description' => 'Add rate limiting to all API endpoints',
                'assigned_by' => $admin->id,
                'priority' => 'normal',
                'status' => 'todo'
            ],
            [
                'title' => 'API Testing',
                'description' => 'Write comprehensive tests for all API endpoints',
                'assigned_by' => $admin->id,
                'priority' => 'high',
                'status' => 'todo'
            ]
        ];

        foreach ($sampleTasks as $taskData) {
            $task = ApiptTask::firstOrCreate(
                ['title' => $taskData['title']],
                $taskData
            );

            // Assign task to developers
            $task->developers()->syncWithoutDetaching([$developer1->id, $developer2->id]);
        }

        $this->command->info('API Progress Tracker sample data created successfully!');
    }
}
