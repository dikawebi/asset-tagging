<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Spesifikasi perangkat (free-text, opsional): diisi manual di form
     * aset atau via kolom CSV staging (processor, memory, storage).
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('processor')->nullable()->after('serial_number');
            $table->string('memory')->nullable()->after('processor');
            $table->string('storage')->nullable()->after('memory');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['processor', 'memory', 'storage']);
        });
    }
};
