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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 30)->default('unpaid')->after('status');
            $table->string('payment_provider', 30)->nullable()->after('payment_status');
            $table->string('stripe_checkout_session_id')->nullable()->unique()->after('payment_provider');
            $table->string('stripe_payment_intent_id')->nullable()->index()->after('stripe_checkout_session_id');
            $table->text('stripe_client_secret')->nullable()->after('stripe_payment_intent_id');
            $table->timestamp('stripe_checkout_session_expires_at')->nullable()->after('stripe_client_secret');
            $table->timestamp('paid_at')->nullable()->after('stripe_checkout_session_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['stripe_checkout_session_id']);
            $table->dropIndex(['stripe_payment_intent_id']);
            $table->dropColumn([
                'payment_status',
                'payment_provider',
                'stripe_checkout_session_id',
                'stripe_payment_intent_id',
                'stripe_client_secret',
                'stripe_checkout_session_expires_at',
                'paid_at',
            ]);
        });
    }
};
