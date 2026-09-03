<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // user / siswa yang meminjam
            $table->string('kode_peminjaman')->nullable()->unique();
            $table->string('nama_peminjam');
            $table->string('kelas_atau_jabatan')->nullable();
            $table->string('kontak')->nullable();
            $table->text('keperluan');
            $table->integer('jumlah_pinjam')->default(1);
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_rencana');
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->enum('status', ['Menunggu Persetujuan', 'Dipinjam', 'Dikembalikan', 'Terlambat', 'Ditolak'])->default('Dipinjam');
            $table->enum('kondisi_kembali', ['Baik', 'Perawatan', 'Perbaikan', 'Rusak Berat', 'Hilang'])->nullable();
            $table->text('catatan')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
