<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('workflow_status', 20)->nullable()->after('approval_status');
        });

        DB::table('events')->whereNull('workflow_status')->update([
            'workflow_status' => 'submitted',
        ]);

        Schema::table('events', function (Blueprint $table) {
            $table->string('workflow_status', 20)->default('draft')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('workflow_status');
        });
    }
};
