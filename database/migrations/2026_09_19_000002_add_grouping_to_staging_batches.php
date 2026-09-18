<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lokasi, departemen, kategori, dan pemegang diisi manual per batch
     * (satu upload = satu kelompok), bukan per baris CSV.
     */
    public function up(): void
    {
        Schema::table('staging_batches', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('file_name')->constrained('locations')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('location_id')->constrained('departments')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('department_id')->constrained('categories')->nullOnDelete();
            $table->string('user_name')->nullable()->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('staging_batches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn('user_name');
        });
    }
};
