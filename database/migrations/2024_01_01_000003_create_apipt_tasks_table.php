<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('apipt')->create('apipt_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_by')->constrained('apipt_developers')->cascadeOnDelete();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->datetime('estimated_completion_time')->nullable();
            $table->datetime('completion_time')->nullable();
            $table->enum('status', ['todo', 'in_progress', 'issue', 'not_needed', 'complete'])->default('todo');
            $table->timestamps();

            // Add indexes for better performance
            $table->index('assigned_by');
            $table->index('priority');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::connection('apipt')->dropIfExists('apipt_tasks');
    }
};
