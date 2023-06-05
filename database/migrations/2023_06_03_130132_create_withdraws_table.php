<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->enum('type', ['save', 'transfer', 'manual']);
            $table->string('bank_name')->nullable();
            $table->string('bank_number')->nullable();
            $table->string('wallet_number')->nullable();
            $table->integer('cash_out')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
