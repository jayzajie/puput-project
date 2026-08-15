<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peran', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('email')->unique();
            $table->unsignedBigInteger('gaji_pokok');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('peran_id')->nullable()->after('id')->constrained('peran');
            $table->foreignId('karyawan_id')->nullable()->after('peran_id')->unique()->constrained('karyawan')->nullOnDelete();
            $table->string('status')->default('aktif')->after('password');
            $table->timestamp('terakhir_login')->nullable()->after('status');
        });

        Schema::create('periode_gaji', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->unique(['bulan', 'tahun']);
        });

        Schema::create('rekap_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('status')->default('hadir');
            $table->decimal('total_jam', 6, 2)->default(0);
            $table->decimal('x1_5', 6, 2)->default(0);
            $table->decimal('x2', 6, 2)->default(0);
            $table->decimal('x3', 6, 2)->default(0);
            $table->decimal('x4', 6, 2)->default(0);
            $table->unsignedBigInteger('bbl')->default(0);
            $table->unsignedBigInteger('ujk')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['karyawan_id', 'tanggal']);
        });

        Schema::create('tarif_ritase', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->decimal('jarak_dari', 8, 2)->nullable();
            $table->decimal('jarak_sampai', 8, 2)->nullable();
            $table->unsignedBigInteger('tarif');
            $table->string('satuan');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('ritase_coal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->unsignedInteger('jumlah_ritase');
            $table->unsignedBigInteger('total_ritase');
            $table->timestamps();
            $table->unique(['karyawan_id', 'tanggal']);
        });

        Schema::create('ritase_topsoil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('jarak_km', 8, 2);
            $table->decimal('bcm', 10, 2);
            $table->unsignedInteger('jumlah_ritase')->default(0);
            $table->unsignedBigInteger('bonus')->default(0);
            $table->unsignedBigInteger('total_ritase');
            $table->timestamps();
            $table->unique(['karyawan_id', 'tanggal']);
        });

        Schema::create('payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('periode_gaji_id')->constrained('periode_gaji')->cascadeOnDelete();
            $table->decimal('total_jam', 8, 2)->default(0);
            $table->unsignedBigInteger('gaji_pokok')->default(0);
            $table->unsignedBigInteger('total_lembur')->default(0);
            $table->unsignedBigInteger('total_ritase_coal')->default(0);
            $table->unsignedBigInteger('total_ritase_topsoil')->default(0);
            $table->unsignedBigInteger('total_tunjangan')->default(0);
            $table->unsignedBigInteger('total_potongan')->default(0);
            $table->unsignedBigInteger('gaji_kotor')->default(0);
            $table->unsignedBigInteger('gaji_bersih')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('difinalisasi_pada')->nullable();
            $table->timestamps();
            $table->unique(['karyawan_id', 'periode_gaji_id']);
        });

        Schema::create('potongan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('periode_gaji_id')->constrained('periode_gaji')->cascadeOnDelete();
            $table->foreignId('payroll_id')->nullable()->constrained('payroll')->cascadeOnDelete();
            $table->string('jenis_potongan');
            $table->string('deskripsi')->nullable();
            $table->unsignedBigInteger('nominal');
            $table->timestamps();
        });

        Schema::create('slip_gaji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->unique()->constrained('payroll')->cascadeOnDelete();
            $table->timestamp('tanggal_cetak')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status')->default('tersedia');
            $table->timestamps();
        });

        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_laporan');
            $table->json('parameter')->nullable();
            $table->date('periode_dari');
            $table->date('periode_sampai');
            $table->string('file_path')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
        Schema::dropIfExists('slip_gaji');
        Schema::dropIfExists('potongan');
        Schema::dropIfExists('payroll');
        Schema::dropIfExists('ritase_topsoil');
        Schema::dropIfExists('ritase_coal');
        Schema::dropIfExists('tarif_ritase');
        Schema::dropIfExists('rekap_kerja');
        Schema::dropIfExists('periode_gaji');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('karyawan_id');
            $table->dropConstrainedForeignId('peran_id');
            $table->dropColumn(['status', 'terakhir_login']);
        });
        Schema::dropIfExists('karyawan');
        Schema::dropIfExists('peran');
    }
};
