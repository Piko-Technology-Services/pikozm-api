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
       Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('focus_area');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 5)->default('USD');
            $table->string('reference')->unique();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->json('payment_response')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
