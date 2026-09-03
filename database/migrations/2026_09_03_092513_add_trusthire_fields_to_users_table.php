<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('id');
            $table->string('account_type')->default('INDIVIDUAL')->after('name');
            $table->string('location')->nullable()->after('account_type');
            $table->text('photo_url')->nullable()->after('location');
            $table->boolean('marketing_opt_in')->default(false)->after('photo_url');
            $table->timestamp('terms_accepted_at')->nullable()->after('marketing_opt_in');
            $table->timestamp('profile_completed_at')->nullable()->after('terms_accepted_at');
            $table->boolean('is_admin')->default(false)->after('profile_completed_at');
            $table->string('verification_tier')->default('BASIC')->after('is_admin');
            $table->text('id_document_url')->nullable()->after('verification_tier');
            $table->text('selfie_url')->nullable()->after('id_document_url');
            $table->timestamp('verification_requested_at')->nullable()->after('selfie_url');
            $table->timestamp('verification_reviewed_at')->nullable()->after('verification_requested_at');
            $table->string('verification_reject_reason')->nullable()->after('verification_reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'account_type', 'location', 'photo_url', 'marketing_opt_in',
                'terms_accepted_at', 'profile_completed_at', 'is_admin', 'verification_tier',
                'id_document_url', 'selfie_url', 'verification_requested_at',
                'verification_reviewed_at', 'verification_reject_reason',
            ]);
        });
    }
};
