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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('kategori');
            $table->string('merk')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('satuan')->default('Unit');
            $table->enum('kondisi', ['Baik', 'Perawatan', 'Perbaikan', 'Rusak Berat', 'Hilang'])->default('Baik');
            $table->string('lokasi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->year('tahun_pembelian')->nullable();
            $table->date('tanggal_pembelian')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->string('sumber_dana')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};