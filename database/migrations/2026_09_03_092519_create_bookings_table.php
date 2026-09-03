<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_request_id')->unique()->constrained('item_requests');
            $table->foreignId('offer_id')->unique()->constrained('offers');
            $table->foreignId('lender_id')->constrained('users');
            $table->foreignId('renter_id')->constrained('users');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('CONFIRMED');
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
