<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('laboratorium');
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('unit_index')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('teknisi')->nullable();
            $table->date('tanggal_maintenance');
            $table->enum('jenis', ['Preventif', 'Korektif', 'Penggantian'])->default('Korektif');
            $table->text('deskripsi_kerusakan');
            $table->text('tindakan')->nullable();
            $table->decimal('biaya', 15, 2)->nullable();
            $table->enum('status', ['Selesai', 'Proses', 'Pending'])->default('Pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};