<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change permissions column from string/VARCHAR to TEXT to allow many permissions
        Schema::table('roles', function (Blueprint $table) {
            // Some database drivers require raw SQL for type changes
        });

        DB::statement('ALTER TABLE roles MODIFY permissions TEXT NULL');
    }

    public function down(): void
    {
        // Revert back to VARCHAR(255) if needed
        Schema::table('roles', function (Blueprint $table) {
            // Placeholder for schema builder
        });

        DB::statement('ALTER TABLE roles MODIFY permissions VARCHAR(255) NULL');
    }
};
