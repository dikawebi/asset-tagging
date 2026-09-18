<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel staging untuk flow registrasi aset Kasus 2:
     * CSV sysinfo di-upload -> divalidasi per baris -> di-commit berurutan
     * ke pool dummy (Idle + serial NULL). All-or-nothing saat commit.
     */
    public function up(): void
    {
        Schema::create('staging_batches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_name')->nullable();
            $table->string('status')->default('uploaded'); // uploaded | validated | committed
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('valid_rows')->default(0);
            $table->unsignedInteger('assigned_rows')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('committed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('staging_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('staging_batches')->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->json('data');
            $table->string('status')->default('pending'); // pending | valid | invalid | assigned
            $table->text('error_message')->nullable();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->timestamps();

            $table->unique(['batch_id', 'row_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staging_rows');
        Schema::dropIfExists('staging_batches');
    }
};
