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
        Schema::create('cameras', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->string('merek');
    $table->unsignedInteger('harga_per_hari');
    $table->text('deskripsi')->nullable();
    $table->string('foto')->nullable();
    $table->enum('status', ['tersedia', 'disewa'])->default('tersedia');
    $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cameras');
    }
};
