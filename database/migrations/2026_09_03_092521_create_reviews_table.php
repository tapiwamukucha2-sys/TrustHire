<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('target_id')->constrained('users');
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'author_id']);
            $table->index('target_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
