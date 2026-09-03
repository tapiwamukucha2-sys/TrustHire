<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_request_id')->constrained('item_requests');
            $table->foreignId('listing_id')->nullable()->constrained('listings');
            $table->foreignId('lender_id')->constrained('users');
            $table->decimal('price', 10, 2);
            $table->text('message')->nullable();
            $table->string('status')->default('PENDING');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
