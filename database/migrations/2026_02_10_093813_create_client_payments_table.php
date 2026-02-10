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
        Schema::create('client_payments', function (Blueprint $table) {
            $table->id();

            $table->string('full_name');
            $table->string('email');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 5);
            $table->string('purpose');
            $table->string('reference')->unique();

            $table->enum('status', [
                'pending',
                'processing',
                'paid',
                'failed'
            ])->default('pending');

            $table->json('payment_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_payments');
    }
};
