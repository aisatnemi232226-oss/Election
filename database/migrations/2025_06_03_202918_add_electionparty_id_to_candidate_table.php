<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddElectionpartyIdToCandidateTable extends Migration
{
    public function up()
    {
        Schema::table('candidate', function (Blueprint $table) {
            if (!Schema::hasColumn('candidate', 'electionparty_id')) {
                $table->unsignedBigInteger('electionparty_id')->nullable()->after('course_id');
            }
        });

        // electionparty.id is BIGINT (signed) based on existing migration.
        // MySQL requires that FK column signedness matches referenced column.
        // Ensure candidate.electionparty_id is BIGINT NULL (signed) before adding FK.
        DB::statement('ALTER TABLE `candidate` MODIFY `electionparty_id` BIGINT(20) NULL');

        // Add foreign key only if it doesn't exist yet
        $fk = DB::selectOne(
            "SELECT CONSTRAINT_NAME AS name
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'candidate'
               AND COLUMN_NAME = 'electionparty_id'
               AND REFERENCED_TABLE_NAME = 'electionparty'
               AND REFERENCED_COLUMN_NAME = 'id'
             LIMIT 1"
        );

        if (!$fk) {
            Schema::table('candidate', function (Blueprint $table) {
                $table->foreign('electionparty_id')->references('id')->on('electionparty');
            });
        }
    }

    public function down()
    {
        Schema::table('candidate', function (Blueprint $table) {
            $table->dropForeign(['electionparty_id']);
            $table->dropColumn('electionparty_id');
        });
    }
}