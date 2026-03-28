<?php

use App\Enums\TripBookingStatus;
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
        Schema::create('trip_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id');
            $table->date('date');
            $table->time('time');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone_number');
            $table->text('address');
            $table->text('pickup_address')->nullable();
            $table->integer('weight')->default(0);
            $table->decimal('weight_per_kg_price', 20, 2)->default(0);
            $table->decimal('service_fee', 20, 2)->default(0);
            $table->decimal('pickup_fee', 20, 2)->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->boolean('pickup_service_status')->default(false);
            $table->string('transaction_id')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->string('status')->default(TripBookingStatus::PENDING->value);
            $table->timestamps();


            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_bookings');
    }
};
