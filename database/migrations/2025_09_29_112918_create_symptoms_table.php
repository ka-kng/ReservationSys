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
        Schema::create('symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->onDelete('cascade');
            $table->json('symptoms_start')->nullable();
            $table->json('symptoms_type')->nullable();
            $table->text('symptoms_other')->nullable();
            $table->boolean('past_disease_flag')->nullable();
            $table->text('past_disease_detail')->nullable();
            $table->boolean('allergy_flag')->nullable();
            $table->text('allergy_detail')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptoms');
    }
};
