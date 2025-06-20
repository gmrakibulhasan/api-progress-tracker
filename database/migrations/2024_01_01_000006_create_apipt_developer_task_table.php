<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apipt_developer_task', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained('apipt_developers')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('apipt_tasks')->cascadeOnDelete();
            $table->datetime('viewed_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate assignments
            $table->unique(['developer_id', 'task_id']);
            
            // Add indexes for better performance
            $table->index('viewed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apipt_developer_task');
    }
};
