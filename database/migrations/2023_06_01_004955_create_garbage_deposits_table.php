<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garbage_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nasabah_id');
            $table->foreign('nasabah_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('garbage_id')->constrained();
            $table->date('date');
            $table->integer('weight');
            $table->integer('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garbage_deposits');
    }
};
