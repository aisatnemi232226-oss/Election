<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('systemlogs', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable in case system logs exist without a user
            $table->enum('log_type', ['AUDIT', 'SECURITY', 'USER_ACTIVITY', 'TRANSACTION']);
            $table->string('action'); 
            $table->text('description');
            $table->string('table_name');
            $table->unsignedBigInteger('record_id')->nullable(); // Nullable in case no specific record is linked
            $table->enum('operation', ['INSERT', 'UPDATE', 'DELETE', 'READ', 'LOGIN', 'LOGOUT']);
            $table->json('old_data')->nullable(); // Store previous data (before update/delete)
            $table->json('new_data')->nullable(); // Store new data (after insert/update)
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6 support
            $table->text('user_agent')->nullable(); // Stores browser/device details
            $table->timestamp('created_at')->useCurrent(); // Log entry timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('systemlogs');
    }
};
