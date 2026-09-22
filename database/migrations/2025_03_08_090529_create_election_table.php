<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('election', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('reg_start');
            $table->date('reg_end');
            $table->date('campaign_start')->nullable();
            $table->date('campaign_end')->nullable();
            $table->date('result_date')->nullable();
            $table->binary('election_img')->nullable();
            $table->integer('admin_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->softDeletes()->useCurrent();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('election');
    }
};
