<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddElectionpartyIdToCandidateTable extends Migration
{
    public function up()
    {
        Schema::table('candidate', function (Blueprint $table) {
            if (!Schema::hasColumn('candidate', 'electionparty_id')) {
                $table->unsignedBigInteger('electionparty_id')->nullable()->after('course_id');
            }
            $table->foreign('electionparty_id')->references('id')->on('electionparty');
        });
    }

    public function down()
    {
        Schema::table('candidate', function (Blueprint $table) {
            $table->dropForeign(['electionparty_id']);
            $table->dropColumn('electionparty_id');
        });
    }
}