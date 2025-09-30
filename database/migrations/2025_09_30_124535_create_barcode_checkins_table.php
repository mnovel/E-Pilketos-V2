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
        Schema::create('barcode_checkins', function (Blueprint $table) {
            $table->uuid('device_id')->primary();
            $table->uuid('token');
            $table->foreignUuid('participant_id')
                ->nullable()
                ->default(null)
                ->constrained('participants')
                ->onDelete('cascade')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_checkins');
    }
};
