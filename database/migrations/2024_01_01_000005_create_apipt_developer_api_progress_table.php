<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apipt_developer_api_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained('apipt_developers')->cascadeOnDelete();
            $table->foreignId('api_progress_id')->constrained('apipt_api_progress')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('apipt_developers')->cascadeOnDelete();
            $table->datetime('viewed_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate assignments
            $table->unique(['developer_id', 'api_progress_id']);

            // Add indexes for better performance
            $table->index('assigned_by');
            $table->index('viewed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apipt_developer_api_progress');
    }
};
