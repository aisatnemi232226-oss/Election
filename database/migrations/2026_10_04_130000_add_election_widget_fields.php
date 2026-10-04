<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('election', function (Blueprint $table): void {
            $table->string('result_visibility')->default('hidden');
        });

        Schema::table('candidate', function (Blueprint $table): void {
            $table->foreignId('election_id')
                ->nullable()
                ->after('id')
                ->constrained('election')
                ->nullOnDelete();
        });

        Schema::table('position_votes', function (Blueprint $table): void {
            $table->dropUnique('position_votes_user_position_unique');
            $table->foreignId('election_id')
                ->nullable()
                ->after('id')
                ->constrained('election')
                ->nullOnDelete();
            $table->unique(
                ['user_id', 'election_id', 'position_id'],
                'position_votes_user_election_position_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('position_votes', function (Blueprint $table): void {
            $table->dropUnique('position_votes_user_election_position_unique');
            $table->dropConstrainedForeignId('election_id');
            $table->unique(['user_id', 'position_id'], 'position_votes_user_position_unique');
        });

        Schema::table('candidate', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('election_id');
        });

        Schema::table('election', function (Blueprint $table): void {
            $table->dropColumn('result_visibility');
        });
    }
};
