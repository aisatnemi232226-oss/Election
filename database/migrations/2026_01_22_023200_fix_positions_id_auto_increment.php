<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure positions.id can auto-increment so inserts work without manually providing an id.
        DB::statement('ALTER TABLE `positions` MODIFY `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to non-auto-increment (best-effort)
        DB::statement('ALTER TABLE `positions` MODIFY `id` BIGINT(20) UNSIGNED NOT NULL');
    }
};
