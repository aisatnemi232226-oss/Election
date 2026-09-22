<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('positions', 'delete_at') && !Schema::hasColumn('positions', 'deleted_at')) {
            DB::statement('ALTER TABLE `positions` CHANGE `delete_at` `deleted_at` TIMESTAMP NULL DEFAULT NULL');
        }

        if (Schema::hasColumn('positions', 'deleted_at')) {
            DB::statement('ALTER TABLE `positions` MODIFY `deleted_at` TIMESTAMP NULL DEFAULT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('positions', 'deleted_at') && !Schema::hasColumn('positions', 'delete_at')) {
            DB::statement('ALTER TABLE `positions` CHANGE `deleted_at` `delete_at` TIMESTAMP NULL DEFAULT NULL');
        }
    }
};
