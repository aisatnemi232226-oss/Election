<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ElectionWidgetMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);
        DB::purge('sqlite');

        Schema::create('election', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });
        Schema::create('candidate', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
        });
        Schema::create('position_votes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('candidate_id');
            $table->unique(['user_id', 'position_id'], 'position_votes_user_position_unique');
        });
    }

    public function test_migration_keeps_historical_records_unattributed_and_adds_election_scoped_vote_uniqueness(): void
    {
        DB::table('election')->insert(['id' => 1, 'name' => 'Election']);
        DB::table('candidate')->insert(['id' => 1, 'name' => 'Historical Candidate']);
        DB::table('position_votes')->insert([
            'id' => 1,
            'user_id' => 4,
            'position_id' => 2,
            'candidate_id' => 1,
        ]);

        $migration = require database_path('migrations/2026_10_04_130000_add_election_widget_fields.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumn('election', 'result_visibility'));
        $this->assertTrue(Schema::hasColumn('candidate', 'election_id'));
        $this->assertTrue(Schema::hasColumn('position_votes', 'election_id'));
        $this->assertDatabaseHas('election', [
            'id' => 1,
            'result_visibility' => 'hidden',
        ]);
        $this->assertDatabaseHas('candidate', [
            'id' => 1,
            'election_id' => null,
        ]);
        $this->assertDatabaseHas('position_votes', [
            'id' => 1,
            'election_id' => null,
        ]);

        DB::table('position_votes')->insert([
            'user_id' => 4,
            'position_id' => 2,
            'candidate_id' => 1,
            'election_id' => 1,
        ]);
        $this->assertDatabaseCount('position_votes', 2);
    }
}
