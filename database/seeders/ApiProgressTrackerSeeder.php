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
    }
}
