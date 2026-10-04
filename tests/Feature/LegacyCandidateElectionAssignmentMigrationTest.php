<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyCandidateElectionAssignmentMigrationTest extends TestCase
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
            $table->softDeletes();
        });
        Schema::create('positions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->softDeletes();
        });
        Schema::create('candidate', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('election_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->string('name');
            $table->string('status');
            $table->softDeletes();
        });
    }

    public function test_only_active_candidates_with_a_unique_matching_election_are_assigned(): void
    {
        DB::table('election')->insert([
            ['id' => 21, 'name' => 'Presidential'],
            ['id' => 22, 'name' => 'Vice-President'],
        ]);
        DB::table('positions')->insert([
            ['id' => 3, 'name' => 'Presidential'],
            ['id' => 2, 'name' => 'Vice'],
            ['id' => 8, 'name' => 'Auditor'],
        ]);
        DB::table('candidate')->insert([
            ['id' => 1, 'name' => 'President Candidate', 'status' => 'Active', 'position_id' => 3, 'election_id' => null, 'deleted_at' => null],
            ['id' => 2, 'name' => 'Vice Candidate', 'status' => 'Active', 'position_id' => 2, 'election_id' => null, 'deleted_at' => null],
            ['id' => 3, 'name' => 'Auditor Candidate', 'status' => 'Active', 'position_id' => 8, 'election_id' => null, 'deleted_at' => null],
            ['id' => 4, 'name' => 'Pending Candidate', 'status' => 'Pending', 'position_id' => 3, 'election_id' => null, 'deleted_at' => null],
            ['id' => 5, 'name' => 'Already Assigned', 'status' => 'Active', 'position_id' => 3, 'election_id' => 22, 'deleted_at' => null],
            ['id' => 6, 'name' => 'Deleted Candidate', 'status' => 'Active', 'position_id' => 3, 'election_id' => null, 'deleted_at' => now()],
        ]);

        $migration = require database_path('migrations/2026_10_04_144000_assign_matching_legacy_candidates_to_elections.php');
        $migration->up();

        $this->assertDatabaseHas('candidate', ['id' => 1, 'election_id' => 21]);
        $this->assertDatabaseHas('candidate', ['id' => 2, 'election_id' => 22]);
        $this->assertDatabaseHas('candidate', ['id' => 3, 'election_id' => null]);
        $this->assertDatabaseHas('candidate', ['id' => 4, 'election_id' => null]);
        $this->assertDatabaseHas('candidate', ['id' => 5, 'election_id' => 22]);
        $this->assertDatabaseHas('candidate', ['id' => 6, 'election_id' => null]);
    }

    public function test_ambiguous_election_names_are_not_assigned(): void
    {
        DB::table('election')->insert([
            ['id' => 21, 'name' => 'Presidential'],
            ['id' => 23, 'name' => 'Presidential'],
        ]);
        DB::table('positions')->insert(['id' => 3, 'name' => 'Presidential']);
        DB::table('candidate')->insert([
            'id' => 1,
            'name' => 'President Candidate',
            'status' => 'Active',
            'position_id' => 3,
        ]);

        $migration = require database_path('migrations/2026_10_04_144000_assign_matching_legacy_candidates_to_elections.php');
        $migration->up();

        $this->assertDatabaseHas('candidate', ['id' => 1, 'election_id' => null]);
    }
}
