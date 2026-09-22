<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('election_event_settings', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();

            $table->string('event_1_title')->nullable();
            $table->string('event_1_date')->nullable();
            $table->text('event_1_description')->nullable();
            $table->string('event_1_image')->nullable();

            $table->string('event_2_title')->nullable();
            $table->string('event_2_date')->nullable();
            $table->text('event_2_description')->nullable();
            $table->string('event_2_image')->nullable();

            $table->string('event_3_title')->nullable();
            $table->string('event_3_date')->nullable();
            $table->text('event_3_description')->nullable();
            $table->string('event_3_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_event_settings');
    }
};
