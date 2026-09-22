<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('electionparty', function (Blueprint $table) {
            if (!Schema::hasColumn('electionparty', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->after('delete_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('electionparty', function (Blueprint $table) {
            if (Schema::hasColumn('electionparty', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
