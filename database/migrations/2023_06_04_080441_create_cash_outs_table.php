<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->enum('status', ['save', 'transfer', 'manual']);
            $table->date('date_transaction');
            $table->integer('cash_out');
            $table->text('trx_photo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_outs');
    }
};
