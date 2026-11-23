<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();

                $table->string('kode_booking')->unique();

                // Relasi ke tabel users dan cameras (unit)
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('unit_id'); // hardcode camera 1-3, FK opsional

                // Waktu sewa (zona Asia/Jakarta)
                $table->dateTime('mulai_at');
                $table->dateTime('selesai_at');
                $table->unsignedSmallInteger('durasi_jam');
                $table->unsignedInteger('estimasi_harga')->default(0);

                // Detail operasional
                $table->string('lokasi_pickup');
                $table->text('catatan')->nullable();
                $table->string('metode_pembayaran')->nullable(); // bank_transfer, dana, ovo, gopay

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

                $table->string('payment_ref')->nullable();
                $table->dateTime('expired_at')->nullable();

                $table->timestamps(); // created_at diisi server (Asia/Jakarta)
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
