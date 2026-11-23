<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'no_whatsapp')) {
                $table->string('no_whatsapp', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['penyewa', 'admin', 'owner'])->default('penyewa')->after('no_whatsapp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_whatsapp', 'role']);
        });
    }
};
