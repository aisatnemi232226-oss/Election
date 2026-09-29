<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'middle_initial')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('middle_initial', 10)->nullable()->after('first_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'middle_initial')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('middle_initial');
            });
        }
    }
};
