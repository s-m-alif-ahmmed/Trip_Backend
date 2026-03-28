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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Departure
            $table->string('departure_city');
            $table->string('departure_country');
            $table->string('departure_country_code');

            // Arrival
            $table->string('arrival_city');
            $table->string('arrival_country');
            $table->string('arrival_country_code');

            // Trip Info
            $table->decimal('available_weight', 10, 2);
            $table->date('date');
            $table->string('time');
            $table->decimal('price_per_kg', 20, 2)->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
