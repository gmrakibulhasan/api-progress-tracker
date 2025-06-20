<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apipt_comments', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->json('attachments')->nullable(); // Multiple file upload option
            $table->json('mentions')->nullable(); // JSON data - [{id: developer_id, seen_at: date_time, nullable}]
            $table->foreignId('parent_id')->nullable()->constrained('apipt_comments')->cascadeOnDelete();
            $table->morphs('commentable'); // For ApiProgress or Task
            $table->foreignId('developer_id')->constrained('apipt_developers')->cascadeOnDelete();
            $table->timestamps();

            // Add indexes for better performance
            $table->index('parent_id');
            $table->index(['commentable_type', 'commentable_id']);
            $table->index('developer_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apipt_comments');
    }
};
