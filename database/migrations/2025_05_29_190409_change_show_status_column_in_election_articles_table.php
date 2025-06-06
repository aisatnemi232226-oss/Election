<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeShowStatusColumnInElectionArticlesTable extends Migration
{
    public function up()
    {
        Schema::table('election_articles', function (Blueprint $table) {
            // Convert existing data: '1' to 1, '0' to 0, null to 0
            \DB::statement("UPDATE election_articles SET show_status = CASE 
                WHEN show_status = '1' THEN 1 
                WHEN show_status = '0' THEN 0 
                ELSE 0 
            END");

            // Change the column to TINYINT
            $table->tinyInteger('show_status')->default(0)->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('election_articles', function (Blueprint $table) {
            // Revert to VARCHAR
            $table->string('show_status')->nullable()->default(null)->change();
        });
    }
}