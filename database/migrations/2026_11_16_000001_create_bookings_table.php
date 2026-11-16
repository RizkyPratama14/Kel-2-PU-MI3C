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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Kode unik untuk setiap booking (mis. BK-20261116-XXXX)
            $table->string('kode_booking')->unique();

            // Relasi ke tabel users dan cameras (unit)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('cameras')->cascadeOnDelete();

            // Waktu sewa — disimpan dengan timezone Asia/Jakarta
            $table->dateTime('mulai_at');
            $table->dateTime('selesai_at');
            $table->unsignedSmallInteger('durasi_jam');

            // Informasi operasional
            $table->string('lokasi_pickup');

            // Status booking
            $table->enum('status_booking', [
                'Menunggu Verifikasi',
                'Menunggu Pembayaran',
                'Aktif',
                'Selesai',
                'Dibatalkan',
                'Gagal',
            ])->default('Menunggu Verifikasi');

            // Status pembayaran
            $table->enum('status_pembayaran', [
                'Belum Bayar',
                'Lunas',
                'Gagal',
                'Dikembalikan',
            ])->default('Belum Bayar');

            // Referensi pembayaran dari payment gateway
            $table->string('payment_ref')->nullable();

            // Waktu kedaluwarsa untuk status Menunggu Pembayaran
            $table->dateTime('expired_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
