<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'voter_verification_submitted_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('voter_verification_submitted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'voter_verification_submitted_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('voter_verification_submitted_at');
            });
        }
    }
};
