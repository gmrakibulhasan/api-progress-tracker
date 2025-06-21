<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('apipt')->table('apipt_api_progress', function (Blueprint $table) {
            $table->foreignId('assigned_by')->nullable()->after('status')->constrained('apipt_developers')->nullOnDelete();
            $table->index('assigned_by');
        });
    }

    public function down()
    {
        Schema::connection('apipt')->table('apipt_api_progress', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
            $table->dropColumn('assigned_by');
        });
    }
};
