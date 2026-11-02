# DOKUMENTASI SISTEM PENGGAJIAN PT GADING JAYA PERKASA (GJP)
*Rangkuman Diskusi, Panduan Alur 3 Role, Penjelasan Ritase, Formula Penggajian, dan Hasil Cetak Slip Gaji*

---

## 1. STRUKTUR 3 ROLE & MATRIKS HAK AKSES

Aplikasi ini menggunakan **3 Role Pengguna**:
1. **Admin** (`admin@gjp.co.id`): Pengendali penuh data master, konfigurasi tarif, eksekusi payroll, dan laporan manajemen.
2. **Supervisor** (`supervisor@gjp.co.id`): Pencatat operasional lapangan (timesheet absensi harian dan ritase angkutan tambang) serta pemegang hak verifikasi data lapangan.
3. **Karyawan** (`karyawan@gjp.co.id`): Akses mandiri (*Employee Self-Service*) untuk memeriksa jam kerja pribadi, akumulasi ritase, dan mencetak slip gaji resmi.

### Tabel Matriks Fitur & Alur Kerja
| Fitur / Menu | Admin | Supervisor | Karyawan | Alur Kerja Lapangan |
| :--- | :---: | :---: | :---: | :--- |
| **Pengaturan Periode & Tarif** | ✅ Penuh | ❌ | ❌ | Admin membuka periode gaji aktif dan mengatur tarif ritase. |
| **Kelola Data Karyawan & Akun**| ✅ Penuh | ❌ | ❌ | Admin mendaftarkan karyawan baru (akun login dibuat otomatis). |
| **Dashboard** | ✅ Analisis Payroll | ✅ List Karyawan (Paginate 10) | ✅ Ringkasan Gaji (Filter Periode) | Supervisor memantau karyawan; Karyawan bisa memfilter periode. |
| **Input Rekap Kerja (Timesheet)** | ✅ | ✅ | ❌ *(Hanya Lihat Milik Sendiri)* | Supervisor mencatat jam reguler, lembur berjenjang, dan tunjangan harian. |
| **Verifikasi Rekap Kerja** | ✅ | ✅ | ❌ | **Syarat Wajib**: Rekap harus diverifikasi sebelum gaji bisa dihitung. |
| **Input Ritase Coal & Topsoil** | ✅ | ✅ | ❌ *(Hanya Lihat Milik Sendiri)* | Supervisor mencatat trip batubara dan volume/jarak tanah penutup. |
| **Input Potongan (BPJS/Pajak)** | ✅ | ❌ | ❌ | Admin menambahkan potongan penyesuaian (BPJS, kasbon, pajak). |
| **Hitung Payroll Otomatis** | ✅ | ❌ | ❌ | Admin menekan tombol kalkulasi otomatis (Status awal: *Draft*). |
| **Finalisasi & Terbitkan Slip** | ✅ | ❌ | ❌ | Admin mengunci payroll (Status: *Final*) agar slip muncul di akun karyawan. |
| **Cetak Slip Gaji (1 Lembar A4)**| ✅ *(Semua Karyawan)* | ❌ | ✅ *(Hanya Milik Sendiri)* | Karyawan atau Admin mencetak dokumen slip gaji resmi berformat A4. |
| **Export Laporan CSV** | ✅ Penuh | ❌ | ❌ | Admin mengekspor rekap payroll, lembur, ritase, dan potongan untuk bank/direksi. |

---

## 2. DETAIL MODUL OPERASIONAL: RITASE (COAL & TOPSOIL)

Ritase adalah insentif produktivitas angkutan material tambang yang dihitung otomatis oleh sistem:

### A. Ritase Coal (Batubara) — Sistem Tiering Target
Menggunakan sistem batas kuota kerja:
- **0.00 – 120.00 ritase**: **Rp 0** (Kuota dasar kerja bulanan sebelum insentif).
- **121.00 – 171.00 ritase**: **Rp 12.000 / ritase**.
- **172.00 ke atas (∞)**: **Rp 30.000 / ritase** (Bonus over-target).

*Contoh Perhitungan*:
Jika karyawan mengangkut **157 ritase**:
- 120 rit pertama = Rp 0
- Sisa 37 rit masuk tier 2 ($37 \times \text{Rp } 12.000 = \text{\textbf{Rp 444.000}}$).

### B. Ritase Topsoil (Tanah Pucuk / Overburden) — Matriks Jarak & Volume BCM
Dihitung berdasarkan jarak angkut dari *pit* ke *disposal* (KM) dan volume tanah (*Bank Cubic Meter*):
- `0.00 – 2.00 KM` = **Rp 8.000 / BCM**
- `2.00 – 4.00 KM` = **Rp 9.000 / BCM**
- `4.00 – 6.00 KM` = **Rp 10.000 / BCM**
- `6.00 – 8.00 KM` = **Rp 11.000 / BCM**
- `8.00 – 10.00 KM` = **Rp 12.000 / BCM**
- `10.00 – 12.00 KM` = **Rp 13.000 / BCM**

$$\text{Upah Topsoil} = (\text{Volume BCM} \times \text{Tarif Jarak}) + \text{Bonus Operasional}$$

---

## 3. DETAIL MODUL PENGGAJIAN: PAYROLL & SLIP GAJI

### Formula Perhitungan Gaji Lengkap
1. **Konversi Jam Lembur Standar Depnaker**:
   $$\text{Upah Lembur per Jam} = \frac{\text{Gaji Pokok}}{173}$$
2. **Kalkulasi Jam Lembur Berjenjang**:
   $$\text{Total Upah Lembur} = \left[ (x1{,}5 \times 1{,}5) + (x2 \times 2) + (x3 \times 3) + (x4 \times 4) \right] \times \text{Upah per Jam}$$
3. **Kalkulasi Tunjangan Harian Lapangan**:
   $$\text{Total Tunjangan} = \text{BBL (Bahan Bakar Lapangan)} + \text{UJK (Uang Jam Kerja)}$$
4. **Kalkulasi Total Gaji Kotor**:
   $$\text{Gaji Kotor} = \text{Gaji Pokok} + \text{Total Lembur} + \text{Ritase Coal} + \text{Ritase Topsoil} + \text{Tunjangan (BBL + UJK)}$$
5. **Kalkulasi Gaji Bersih (Take Home Pay)**:
   $$\text{Gaji Bersih} = \text{Gaji Kotor} - \text{Total Potongan (BPJS, Pajak, Kasbon, Mangkir)}$$

### Siklus Status Payroll:
- **Draft**: Dihitung oleh Admin, dapat dievaluasi ulang / diedit potongannya. Karyawan belum bisa melihat.
- **Final**: Dikunci oleh Admin, otomatis menerbitkan dokumen slip gaji berstatus `tersedia` di akun karyawan.
- **Rollback (Batal Final)**: Admin dapat menghapus slip untuk mengembalikan payroll ke draft jika ada koreksi data.

---

## 4. FORMULIR INPUT REKAP KERJA HARIAN

Pada formulir **Input Rekap**, tiap kolom berfungsi sebagai berikut:
- **Tanggal**: Tanggal kerja fisik di lapangan.
- **Status**: Status kehadiran (`hadir`, `standby`, `izin`, `sakit`, `mangkir`).
- **Total Jam**: Durasi jam reguler (standar: 8 jam).
- **x1,5**: Jam lembur pertama pada hari kerja biasa.
- **x2**: Jam lembur kedua dst pada hari kerja biasa, atau jam kerja lembur di hari libur.
- **x3**: Jam lembur lanjutan di hari libur resmi.
- **x4**: Jam lembur tingkat tertinggi (jam ke-9 dst pada hari libur nasional).
- **BBL**: Tunjangan Bahan Bakar Lapangan / Beban Lapangan (dalam Rupiah).
- **UJK**: Tunjangan Uang Jam Kerja / Uang Jalan Khusus (dalam Rupiah).
- **Keterangan**: Catatan kondisi lapangan (misal: *Hujan deras unit standby*, *Shift malam*).
- **Tombol Verifikasi**: Disahkan oleh Supervisor agar data valid masuk ke kalkulator payroll.

---

## 5. FORMAT SLIP GAJI PAS 1 LEMBAR A4

Slip gaji dirancang mengikuti standar formal perusahaan:
1. **Kop Surat**: Logo PT Gading Jaya Perkasa, nomor slip otomatis (`SLIP/YYYY/MM/XXXX`), dan periode gaji.
2. **Biodata Karyawan**: NIK, Nama, Jabatan, Rentang Tanggal, dan Total Jam Kerja.
3. **Tabel A (Pendapatan)**: Gaji Pokok, Lembur, Ritase Coal, Ritase Topsoil, Tunjangan, serta baris Subtotal Gaji Kotor.
4. **Tabel B (Potongan)**: Rincian potongan beserta subtotal.
5. **Grand Total**: Kotak *Take Home Pay* (Gaji Bersih).
6. **Tanda Tangan**: Kolom ganda sejajar (Diterima Oleh Karyawan & Disahkan Oleh Admin HR).
7. **Aturan Cetak (@media print)**: Diproteksi dengan margin `8mm 10mm`, ukuran A4 Portrait, `break-inside: avoid`, dan skala yang pas dalam **1 lembar utuh**.

---

## 6. PANDUAN URUTAN DEMO / PRESENTASI KE KLIEN
Untuk alur presentasi yang paling meyakinkan:
1. **Buka dengan ADMIN (1 menit)**: Tunjukkan menu Pengaturan (periode aktif & tarif ritase).
2. **Pindah ke SUPERVISOR (3 menit)**: Tunjukkan Dashboard (ada list karyawan), input Rekap Kerja + Lembur untuk karyawan contoh (misal: Taufik), lalu klik tombol **Verifikasi**. Jelaskan bahwa verifikasi ini mengunci data agar tidak dimanipulasi. Input juga ritase Coal/Topsoil.
3. **Balik ke ADMIN (2 menit)**: Masuk ke Payroll, klik tombol **Hitung Payroll** (tunjukkan kalkulasi otomatis), lalu klik **Finalisasi & Publikasikan Slip**. Tunjukkan sekilas menu Laporan (Export CSV).
4. **Tutup dengan KARYAWAN (2 menit)**: Login sebagai Taufik, tunjukkan fitur pilih periode di Dashboard, buka Payroll, lalu klik **Cetak Slip** dan perlihatkan hasil cetak 1 lembar A4 yang rapi dan elegan.
