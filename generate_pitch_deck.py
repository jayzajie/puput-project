import os
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE

def create_presentation():
    prs = Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    # Colors
    NAVY = RGBColor(23, 49, 66)        # #173142
    BLUE = RGBColor(45, 139, 193)      # #2D8BC1
    DARK_BLUE = RGBColor(33, 111, 156) # #216F9C
    RED = RGBColor(201, 65, 60)        # #C9413C
    GREEN = RGBColor(23, 133, 80)      # #178550
    BG_LIGHT = RGBColor(244, 248, 251) # #F4F8FB
    WHITE = RGBColor(255, 255, 255)
    TEXT_DARK = RGBColor(35, 55, 72)
    TEXT_MUTED = RGBColor(105, 120, 135)
    BORDER_COLOR = RGBColor(218, 230, 238)
    BOX_BG = RGBColor(238, 247, 251)

    SCREENSHOTS_DIR = os.path.abspath('screenshots_ppt')

    def set_bg(slide, color):
        bg = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, prs.slide_height)
        bg.fill.solid()
        bg.fill.fore_color.rgb = color
        bg.line.fill.background()
        return bg

    def add_header(slide, tag_text, title_text, tag_color=BLUE):
        # Tag / Badge Box
        tag_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.4), Inches(8), Inches(0.35))
        tf_tag = tag_box.text_frame
        tf_tag.word_wrap = True
        p_tag = tf_tag.paragraphs[0]
        p_tag.text = tag_text.upper()
        p_tag.font.bold = True
        p_tag.font.size = Pt(11)
        p_tag.font.color.rgb = tag_color
        p_tag.font.name = 'Arial'

        # Main Slide Title
        title_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.72), Inches(11.7), Inches(0.65))
        tf_title = title_box.text_frame
        tf_title.word_wrap = True
        p_title = tf_title.paragraphs[0]
        p_title.text = title_text
        p_title.font.bold = True
        p_title.font.size = Pt(21)
        p_title.font.color.rgb = NAVY
        p_title.font.name = 'Arial'

        # Separator line
        line = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.8), Inches(1.42), Inches(11.733), Inches(0.02))
        line.fill.solid()
        line.fill.fore_color.rgb = BORDER_COLOR
        line.line.fill.background()

    def add_bullet(tf, title, desc, icon="✓"):
        p = tf.add_paragraph()
        p.space_after = Pt(10)
        p.space_before = Pt(3)

        r_icon = p.add_run()
        r_icon.text = f"{icon} "
        r_icon.font.bold = True
        r_icon.font.size = Pt(13)
        r_icon.font.color.rgb = BLUE
        r_icon.font.name = 'Arial'

        r_title = p.add_run()
        r_title.text = title + " : "
        r_title.font.bold = True
        r_title.font.size = Pt(13.5)
        r_title.font.color.rgb = NAVY
        r_title.font.name = 'Arial'

        r_desc = p.add_run()
        r_desc.text = desc
        r_desc.font.bold = False
        r_desc.font.size = Pt(12.5)
        r_desc.font.color.rgb = TEXT_DARK
        r_desc.font.name = 'Arial'

    def add_callout(slide, left, top, width, height, title, text, border_color=BLUE, bg_color=None):
        shape = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, left, top, width, height)
        shape.fill.solid()
        shape.fill.fore_color.rgb = bg_color if bg_color else BOX_BG
        shape.line.color.rgb = border_color
        shape.line.width = Pt(1.5)

        tf = shape.text_frame
        tf.word_wrap = True
        tf.margin_left = Inches(0.18)
        tf.margin_right = Inches(0.18)
        tf.margin_top = Inches(0.12)
        tf.margin_bottom = Inches(0.12)

        p0 = tf.paragraphs[0]
        p0.text = "💡 " + title
        p0.font.bold = True
        p0.font.size = Pt(11.5)
        p0.font.color.rgb = border_color
        p0.font.name = 'Arial'
        p0.space_after = Pt(3)

        p1 = tf.add_paragraph()
        p1.text = text
        p1.font.bold = False
        p1.font.size = Pt(11)
        p1.font.color.rgb = TEXT_DARK
        p1.font.name = 'Arial'

    def add_card_image(slide, img_name, left, top, width, height):
        img_path = os.path.join(SCREENSHOTS_DIR, img_name)
        # Background frame
        frame = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, left - Inches(0.04), top - Inches(0.04), width + Inches(0.08), height + Inches(0.08))
        frame.fill.solid()
        frame.fill.fore_color.rgb = WHITE
        frame.line.color.rgb = BORDER_COLOR
        frame.line.width = Pt(1.5)

        if os.path.exists(img_path):
            slide.shapes.add_picture(img_path, left, top, width=width, height=height)

    # ==========================================
    # SLIDE 1: COVER
    # ==========================================
    s1 = prs.slides.add_slide(blank_layout)
    set_bg(s1, NAVY)

    # Accent Top Stripe
    top_bar = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, Inches(0.18))
    top_bar.fill.solid()
    top_bar.fill.fore_color.rgb = RED
    top_bar.line.fill.background()

    # Logo/Company Badge
    badge = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.2), Inches(1.4), Inches(3.2), Inches(0.5))
    badge.fill.solid()
    badge.fill.fore_color.rgb = DARK_BLUE
    badge.line.fill.background()
    p_b = badge.text_frame.paragraphs[0]
    p_b.text = "PT GADING JAYA PERKASA"
    p_b.font.bold = True
    p_b.font.size = Pt(12)
    p_b.font.color.rgb = WHITE
    p_b.alignment = PP_ALIGN.CENTER

    # Main Title
    t_box = s1.shapes.add_textbox(Inches(1.2), Inches(2.2), Inches(10.8), Inches(2.2))
    tf1 = t_box.text_frame
    tf1.word_wrap = True
    p1 = tf1.paragraphs[0]
    p1.text = "SISTEM PENGGAJIAN & RITASE TAMBANG"
    p1.font.bold = True
    p1.font.size = Pt(38)
    p1.font.color.rgb = WHITE
    p1.space_after = Pt(12)

    p2 = tf1.add_paragraph()
    p2.text = "Panduan Lengkap & Praktis Alur Operasional Lapangan hingga Penggajian"
    p2.font.size = Pt(20)
    p2.font.color.rgb = RGBColor(180, 215, 235)

    # 3 Summary Pills on Cover
    pill_data = [
        ("⏱ Otomatisasi Lembur", "Standar Depnaker 1/173"),
        ("🚛 Insentif Ritase", "Tiering Batubara & Topsoil"),
        ("📄 Slip Gaji Pas A4", "1 Lembar Formal & Siap Cetak")
    ]
    for i, (head, sub) in enumerate(pill_data):
        px = Inches(1.2 + i * 3.7)
        py = Inches(4.8)
        p_card = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, px, py, Inches(3.4), Inches(1.2))
        p_card.fill.solid()
        p_card.fill.fore_color.rgb = RGBColor(33, 62, 82)
        p_card.line.color.rgb = BLUE
        p_card.line.width = Pt(1)
        ptf = p_card.text_frame
        ptf.margin_left = Inches(0.2)
        ptf.margin_top = Inches(0.18)
        pp0 = ptf.paragraphs[0]
        pp0.text = head
        pp0.font.bold = True
        pp0.font.size = Pt(13)
        pp0.font.color.rgb = WHITE
        pp0.space_after = Pt(4)
        pp1 = ptf.add_paragraph()
        pp1.text = sub
        pp1.font.size = Pt(11)
        pp1.font.color.rgb = RGBColor(160, 190, 210)

    # Bottom footer
    f_box = s1.shapes.add_textbox(Inches(1.2), Inches(6.5), Inches(10.8), Inches(0.5))
    pf = f_box.text_frame.paragraphs[0]
    pf.text = "Presentasi Alur Aplikasi untuk Klien & Manajemen · Mudah Dipahami & Bebas Istilah Teknis Rumit"
    pf.font.size = Pt(11.5)
    pf.font.color.rgb = RGBColor(140, 165, 185)

    # ==========================================
    # SLIDE 2: GAMBARAN BESAR (3 TAHAP UTAMA)
    # ==========================================
    s2 = prs.slides.add_slide(blank_layout)
    set_bg(s2, BG_LIGHT)
    add_header(s2, "Gambaran Sederhana", "Bagaimana Cara Kerja Sistem Ini dari Hulu ke Hilir?")

    steps = [
        ("1. OPERASIONAL SITE", "SUPERVISOR LAPANGAN",
         ["Catat absen jam kerja harian driver", "Input lembur fisik (x1.5 s/d x4)", "Input ritase angkut batubara & tanah", "Wajib klik tombol 'Verifikasi' agar sah"],
         BLUE, RGBColor(230, 243, 250)),
        ("2. PENGHITUNGAN GAJI", "ADMIN HR & FINANCE",
         ["Tidak perlu rumus Excel manual", "Input potongan (BPJS, kasbon, pajak)", "Klik 1 tombol 'Hitung Payroll'", "Otomatis gabungkan pokok + lembur + ritase"],
         RED, RGBColor(253, 240, 240)),
        ("3. PENERIMAAN HASIL", "KARYAWAN & DIREKSI",
         ["Slip gaji resmi terbit otomatis", "Hasil cetak pas rapi 1 lembar kertas A4", "Karyawan bisa pantau mandiri dari HP", "Admin ekspor laporan CSV untuk transfer bank"],
         GREEN, RGBColor(235, 248, 240))
    ]

    for i, (num_title, role_title, points, color, card_bg) in enumerate(steps):
        sx = Inches(0.8 + i * 4.0)
        sy = Inches(1.75)
        sw = Inches(3.73)
        sh = Inches(4.3)

        box = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, sx, sy, sw, sh)
        box.fill.solid()
        box.fill.fore_color.rgb = WHITE
        box.line.color.rgb = color
        box.line.width = Pt(2)

        # Header bar inside card
        c_head = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, sx, sy, sw, Inches(0.95))
        c_head.fill.solid()
        c_head.fill.fore_color.rgb = color
        c_head.line.fill.background()
        ctf = c_head.text_frame
        ctf.margin_top = Inches(0.12)
        cp0 = ctf.paragraphs[0]
        cp0.text = num_title
        cp0.font.bold = True
        cp0.font.size = Pt(13)
        cp0.font.color.rgb = WHITE
        cp0.alignment = PP_ALIGN.CENTER
        cp1 = ctf.add_paragraph()
        cp1.text = role_title
        cp1.font.size = Pt(11)
        cp1.font.color.rgb = RGBColor(235, 245, 255)
        cp1.alignment = PP_ALIGN.CENTER

        # Body points
        b_box = s2.shapes.add_textbox(sx + Inches(0.15), sy + Inches(1.1), sw - Inches(0.3), Inches(3.0))
        btf = b_box.text_frame
        btf.word_wrap = True
        for p_idx, pt in enumerate(points):
            bp = btf.paragraphs[0] if p_idx == 0 else btf.add_paragraph()
            bp.space_after = Pt(10)
            r0 = bp.add_run()
            r0.text = "• "
            r0.font.bold = True
            r0.font.color.rgb = color
            r1 = bp.add_run()
            r1.text = pt
            r1.font.size = Pt(12)
            r1.font.color.rgb = TEXT_DARK

    add_callout(s2, Inches(0.8), Inches(6.25), Inches(11.733), Inches(0.85),
                "Mengapa Klien Anda Pasti Suka Solusi Ini?",
                "Menggantikan tumpukan kertas absensi fisik dan file Excel terpisah yang rawan salah rumus. Data mengalir otomatis, ada verifikasi pengaman, dan langsung selesai dalam hitungan detik.")

    # ==========================================
    # SLIDE 3: HALAMAN LOGIN
    # ==========================================
    s3 = prs.slides.add_slide(blank_layout)
    set_bg(s3, BG_LIGHT)
    add_header(s3, "Pintu Masuk Aplikasi", "Satu Halaman Login untuk Semua Pengguna")

    add_card_image(s3, "01_login.png", Inches(0.8), Inches(1.7), Inches(6.8), Inches(4.5))

    t3 = s3.shapes.add_textbox(Inches(7.9), Inches(1.65), Inches(4.6), Inches(4.0))
    tf3 = t3.text_frame
    tf3.word_wrap = True
    add_bullet(tf3, "Satu Alamat Web Praktis", "Admin, Pengawas Site (Supervisor), maupun Karyawan/Driver masuk melalui 1 link yang sama tanpa perlu instal software berat.")
    add_bullet(tf3, "Mengenali Peran Otomatis", "Sistem langsung membatasi menu secara ketat: Supervisor tidak bisa intip uang gaji, Karyawan hanya bisa melihat slip miliknya sendiri.")
    add_bullet(tf3, "Akun Otomatis Dibuat", "Saat Admin mendaftarkan karyawan baru di sistem, akun login langsung aktif otomatis dengan password awal.")

    add_callout(s3, Inches(7.9), Inches(5.35), Inches(4.6), Inches(1.5),
                "Kelebihan untuk Klien",
                "Klien tidak perlu bingung mengelola website berbeda-beda. Cukup buka Google Chrome dari laptop kantor atau HP lapangan, sistem langsung siap pakai 24 jam.")

    # ==========================================
    # SLIDE 4: REKAP KERJA & LEMBUR
    # ==========================================
    s4 = prs.slides.add_slide(blank_layout)
    set_bg(s4, BG_LIGHT)
    add_header(s4, "Langkah 1: Operasional Site", "Input Rekap Kerja, Lembur, & Tunjangan Harian")

    add_card_image(s4, "03_rekap_kerja.png", Inches(0.8), Inches(1.7), Inches(6.8), Inches(4.5))

    t4 = s4.shapes.add_textbox(Inches(7.9), Inches(1.65), Inches(4.6), Inches(4.0))
    tf4 = t4.text_frame
    tf4.word_wrap = True
    add_bullet(tf4, "Pencatatan Kehadiran Cepat", "Supervisor memilih nama driver/operator, tanggal kerja, dan status kehadiran (Hadir, Sakit, Izin, atau Standby alat).")
    add_bullet(tf4, "Lembur Berjenjang (x1.5 - x4)", "Cukup input berapa jam lembur fisiknya. Sistem otomatis menghitungkan pengali uang lembur sesuai aturan resmi Depnaker (Upah Pokok / 173).")
    add_bullet(tf4, "Uang Lapangan (BBL & UJK)", "Tersedia kolom untuk memasukkan uang pengganti bahan bakar (BBL) dan uang jam/jalan kerja (UJK) yang langsung cair bersama gaji.")

    add_callout(s4, Inches(7.9), Inches(5.35), Inches(4.6), Inches(1.5),
                "Bebas Pusing Hitung Manual",
                "Orang lapangan fokus pada fakta kehadiran fisik di tambang. Biarkan sistem komputer yang mengalikan rumus rupiahnya secara presisi.")

    # ==========================================
    # SLIDE 5: FITUR VERIFIKASI (KONTROL KEAMANAN)
    # ==========================================
    s5 = prs.slides.add_slide(blank_layout)
    set_bg(s5, BG_LIGHT)
    add_header(s5, "Kunci Keamanan Finansial", "Fitur Verifikasi: Filter Pengaman dari Data Palsu")

    add_card_image(s5, "03_rekap_kerja.png", Inches(0.8), Inches(1.7), Inches(6.8), Inches(4.5))

    t5 = s5.shapes.add_textbox(Inches(7.9), Inches(1.65), Inches(4.6), Inches(4.0))
    tf5 = t5.text_frame
    tf5.word_wrap = True
    add_bullet(tf5, "Status Awal: Menunggu", "Data kerja yang baru dicatat berstatus 'Menunggu' dan belum disahkan, menandakan perlu pemeriksaan silang.")
    add_bullet(tf5, "Pemeriksaan Pengawas", "Supervisor mengecek kesesuaian timesheet fisik lapangan, lalu menekan tombol hijau 'Verifikasi'.")
    add_bullet(tf5, "Sistem Proteksi Otomatis", "Jika ada rekap yang belum diverifikasi, sistem akan MENOLAK penghitungan gaji oleh HR kantor sampai data tersebut disahkan.")

    add_callout(s5, Inches(7.9), Inches(5.35), Inches(4.6), Inches(1.5),
                "Nilai Jual untuk Pemilik Bisnis",
                "Mencegah kecurangan atau salah bayar upah lembur. Perusahaan hanya mengeluarkan uang untuk pekerjaan yang benar-benar diverifikasi keabsahannya oleh pengawas.")

    # ==========================================
    # SLIDE 6: RITASE COAL & TOPSOIL
    # ==========================================
    s6 = prs.slides.add_slide(blank_layout)
    set_bg(s6, BG_LIGHT)
    add_header(s6, "Produktivitas Tambang", "Insentif Ritase Batubara & Tanah Dihitung Instan")

    add_card_image(s6, "04_ritase.png", Inches(0.8), Inches(1.7), Inches(6.8), Inches(4.5))

    t6 = s6.shapes.add_textbox(Inches(7.9), Inches(1.65), Inches(4.6), Inches(4.0))
    tf6 = t6.text_frame
    tf6.word_wrap = True
    add_bullet(tf6, "Insentif Sesuai Hasil Kerja", "Driver dump truck dan operator alat berat mendapat upah tambahan dari seberapa banyak trip/muatan yang mereka selesaikan.")
    add_bullet(tf6, "Ritase Batubara (Target Tier)", "Sistem kuota target: Contoh 120 rit pertama kuota kerja dasar, rit ke-121 dst langsung diberi insentif progresif per ritase.")
    add_bullet(tf6, "Ritase Topsoil (Jarak KM)", "Semakin jauh jarak buang tanah dari pit ke disposal (KM), semakin tinggi tarif upah per meter kubik (BCM) yang diterima.")

    add_callout(s6, Inches(7.9), Inches(5.35), Inches(4.6), Inches(1.5),
                "Langsung Terhitung Rupiahnya",
                "Saat jumlah rit atau volume BCM diinput, total rupiah insentif langsung muncul di layar dan otomatis masuk ke slip gaji akhir bulan.")

    # ==========================================
    # SLIDE 7: DASHBOARD SUPERVISOR
    # ==========================================
    s7 = prs.slides.add_slide(blank_layout)
    set_bg(s7, BG_LIGHT)
    add_header(s7, "Kemudahan Pengawas", "Dashboard Supervisor: Kelola Seluruh Kru Lapangan")

    add_card_image(s7, "08_dashboard_supervisor.png", Inches(0.8), Inches(1.7), Inches(6.8), Inches(4.5))

    t7 = s7.shapes.add_textbox(Inches(7.9), Inches(1.65), Inches(4.6), Inches(4.0))
    tf7 = t7.text_frame
    tf7.word_wrap = True
    add_bullet(tf7, "Tabel Daftar Karyawan Rapi", "Menampilkan seluruh kru aktif di site dalam format tabel teratur (10 orang per halaman) agar layar tetap bersih dan ringan.")
    add_bullet(tf7, "Tombol Pintas Sekali Klik", "Tersedia tombol langsung 'Rekap Kerja' dan 'Ritase' di samping nama setiap pekerja untuk pencatatan instan.")
    add_bullet(tf7, "Kerahasiaan Gaji Terjaga", "Supervisor hanya melihat nama, NIK, jabatan, dan status kerja. Data gaji pokok tetap rahasia milik manajemen HR.")

    add_callout(s7, Inches(7.9), Inches(5.35), Inches(4.6), Inches(1.5),
                "Dirancang untuk Kondisi Lapangan",
                "Antarmuka bersih dan cepat diakses, sehingga pengawas site tidak membuang waktu mencari-cari menu saat menginput data puluhan pekerja.")

    # ==========================================
    # SLIDE 8: PAYROLL ADMIN (HITUNG 1 KLIK)
    # ==========================================
    s8 = prs.slides.add_slide(blank_layout)
    set_bg(s8, BG_LIGHT)
    add_header(s8, "Langkah 2: Finansial HR", "Hitung Payroll Otomatis Cukup Sekali Klik")

    add_card_image(s8, "05_payroll.png", Inches(0.8), Inches(1.7), Inches(6.8), Inches(4.5))

    t8 = s8.shapes.add_textbox(Inches(7.9), Inches(1.65), Inches(4.6), Inches(4.0))
    tf8 = t8.text_frame
    tf8.word_wrap = True
    add_bullet(tf8, "Penggabungan Data Otomatis", "Sistem otomatis menarik Gaji Pokok + Total Upah Lembur + Ritase Coal + Ritase Topsoil + Tunjangan BBL/UJK.")
    add_bullet(tf8, "Input Potongan Fleksibel", "Admin bisa menambahkan potongan penyesuaian seperti iuran BPJS Kesehatan/Ketenagakerjaan, pinjaman kasbon, atau pajak PPh 21.")
    add_bullet(tf8, "Status Draft untuk Evaluasi", "Hasil hitungan awal berstatus 'Draft'. HR bisa mengecek ulang angka-angkanya sebelum resmi disahkan.")

    add_callout(s8, Inches(7.9), Inches(5.35), Inches(4.6), Inches(1.5),
                "Efisiensi Waktu Luar Biasa",
                "Proses rekap gaji bulanan yang biasanya memakan waktu 2 hingga 3 hari kerja kini tuntas hanya dalam hitungan detik tanpa risiko salah rumus.")

    # ==========================================
    # SLIDE 9: SLIP GAJI PAS 1 LEMBAR A4
    # ==========================================
    s9 = prs.slides.add_slide(blank_layout)
    set_bg(s9, BG_LIGHT)
    add_header(s9, "Langkah 3: Dokumen Resmi", "Slip Gaji Korporat Elegan & Pas 1 Lembar A4")

    add_card_image(s9, "06_slip_gaji.png", Inches(0.8), Inches(1.7), Inches(6.8), Inches(4.5))

    t9 = s9.shapes.add_textbox(Inches(7.9), Inches(1.65), Inches(4.6), Inches(4.0))
    tf9 = t9.text_frame
    tf9.word_wrap = True
    add_bullet(tf9, "Format Kop Surat Resmi", "Dilengkapi logo PT Gading Jaya Perkasa, nomor slip otomatis, dan data lengkap identitas pekerja.")
    add_bullet(tf9, "Tabel Rincian Sangat Jelas", "Memisahkan Tabel A (Rincian Pendapatan) dan Tabel B (Rincian Potongan) sehingga karyawan tahu persis asal-usul uang mereka.")
    add_bullet(tf9, "Pas 1 Halaman Utuh", "Didesain presisi agar saat dicetak ke printer atau disimpan ke PDF, dokumen dan tanda tangan pas di 1 lembar kertas A4.")

    add_callout(s9, Inches(7.9), Inches(5.35), Inches(4.6), Inches(1.5),
                "Meningkatkan Citra Perusahaan",
                "Slip gaji yang rapi dan profesional mencerminkan tata kelola manajemen perusahaan yang bonafide dan terpercaya di mata seluruh pekerja.")

    # ==========================================
    # SLIDE 10: SISI KARYAWAN (TRANSPARANSI)
    # ==========================================
    s10 = prs.slides.add_slide(blank_layout)
    set_bg(s10, BG_LIGHT)
    add_header(s10, "Employee Self-Service", "Karyawan Transparan: Bisa Cek & Cetak dari HP")

    add_card_image(s10, "09_dashboard_karyawan.png", Inches(0.8), Inches(1.7), Inches(6.8), Inches(4.5))

    t10 = s10.shapes.add_textbox(Inches(7.9), Inches(1.65), Inches(4.6), Inches(4.0))
    tf10 = t10.text_frame
    tf10.word_wrap = True
    add_bullet(tf10, "Akses Mandiri Kapan Saja", "Pekerja (driver/operator) bisa masuk ke akun mereka sendiri dari smartphone atau komputer untuk memeriksa slip gaji.")
    add_bullet(tf10, "Pilihan Periode Bebas", "Karyawan dapat memilih dan melihat arsip gaji bulan berjalan maupun bulan-bulan sebelumnya secara transparan.")
    add_bullet(tf10, "Cetak Slip Mandiri", "Cukup tekan tombol 'Cetak Slip' untuk download slip PDF tanpa perlu antre meminta cetakan ke kantor HR.")

    add_callout(s10, Inches(7.9), Inches(5.35), Inches(4.6), Inches(1.5),
                "Mengurangi Beban Pertanyaan HR",
                "Karyawan merasa tenang karena perhitungan transparan. Tim HR tidak lagi dibanjiri pertanyaan komplain perihal rincian jam lembur dan ritase.")

    # ==========================================
    # SLIDE 11: LAPORAN MANAJEMEN & BANK
    # ==========================================
    s11 = prs.slides.add_slide(blank_layout)
    set_bg(s11, BG_LIGHT)
    add_header(s11, "Pelaporan & Pembukuan", "Ekspor Laporan CSV: Siap Transfer Bank & Direksi")

    add_card_image(s11, "07_laporan.png", Inches(0.8), Inches(1.7), Inches(6.8), Inches(4.5))

    t11 = s11.shapes.add_textbox(Inches(7.9), Inches(1.65), Inches(4.6), Inches(4.0))
    tf11 = t11.text_frame
    tf11.word_wrap = True
    add_bullet(tf11, "Ekspor Cepat Berbagai Kategori", "Tersedia ekspor Laporan Payroll, Laporan Lembur, Laporan Ritase, Laporan Potongan, dan Data Karyawan dalam format CSV/Excel.")
    add_bullet(tf11, "Format Payroll Transfer Bank", "Data hasil ekspor langsung kompatibel untuk diunggah ke sistem perbankan transfer gaji massal (BCA, Mandiri, BRI, dll).")
    add_bullet(tf11, "Riwayat Audit Tersimpan", "Setiap kali laporan diekspor, sistem mencatat waktu pembuatan dan nama akun yang mengekspor untuk keperluan audit.")

    add_callout(s11, Inches(7.9), Inches(5.35), Inches(4.6), Inches(1.5),
                "Pengambilan Keputusan Cepat",
                "Direksi dan pimpinan manajemen dapat melihat rekapitulasi biaya pengeluaran gaji dan tren lembur per periode dalam hitungan detik.")

    # ==========================================
    # SLIDE 12: RANGKUMAN NILAI BISNIS (CLOSING)
    # ==========================================
    s12 = prs.slides.add_slide(blank_layout)
    set_bg(s12, NAVY)

    add_header(s12, "Kesimpulan & Nilai Tambah", "Mengapa Aplikasi Ini adalah Investasi Terbaik?", tag_color=RGBColor(255, 200, 100))
    # Adjust title color on dark bg
    for shape in s12.shapes:
        if shape.has_text_frame and "Mengapa Aplikasi" in shape.text_frame.text:
            shape.text_frame.paragraphs[0].font.color.rgb = WHITE

    benefits = [
        ("🎯 100% AKURAT", "Tidak ada lagi salah rumus hitung lembur 1/173 atau perhitungan tarif tier ritase batubara.", BLUE),
        ("🛡 AMAN & TERKONTROL", "Sistem verifikasi lapangan mengunci data agar tidak ada pembayaran atas jam kerja atau absensi fiktif.", GREEN),
        ("⚡ HEMAT WAKTU & BIAYA", "Proses penggajian perusahaan tambang yang rumit dipangkas dari berhari-hari menjadi hitungan menit.", RGBColor(255, 170, 0)),
        ("🤝 TRANSPARAN & DIPERCAYA", "Driver dan operator puas karena slip gaji jelas, terperinci, dan bisa diakses mandiri.", RED)
    ]

    for i, (b_title, b_desc, b_color) in enumerate(benefits):
        bx = Inches(0.8 + (i % 2) * 6.0)
        by = Inches(1.8 + (i // 2) * 2.2)
        bw = Inches(5.6)
        bh = Inches(1.9)

        b_card = s12.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, bx, by, bw, bh)
        b_card.fill.solid()
        b_card.fill.fore_color.rgb = RGBColor(33, 62, 82)
        b_card.line.color.rgb = b_color
        b_card.line.width = Pt(1.5)

        btf = b_card.text_frame
        btf.margin_left = Inches(0.25)
        btf.margin_top = Inches(0.2)
        btf.margin_right = Inches(0.25)

        bp0 = btf.paragraphs[0]
        bp0.text = b_title
        bp0.font.bold = True
        bp0.font.size = Pt(15)
        bp0.font.color.rgb = b_color
        bp0.space_after = Pt(6)

        bp1 = btf.add_paragraph()
        bp1.text = b_desc
        bp1.font.size = Pt(12)
        bp1.font.color.rgb = RGBColor(220, 235, 245)

    # Bottom footer tag
    foot = s12.shapes.add_textbox(Inches(0.8), Inches(6.5), Inches(11.733), Inches(0.5))
    p_foot = foot.text_frame.paragraphs[0]
    p_foot.text = "PT Gading Jaya Perkasa · Solusi Digital Terpercaya Penggajian & Ritase Pertambangan"
    p_foot.font.size = Pt(12)
    p_foot.font.color.rgb = RGBColor(160, 190, 210)
    p_foot.alignment = PP_ALIGN.CENTER

    output_path = os.path.abspath("PRESENTASI_FLOW_PENGGAJIAN_GJP.pptx")
    prs.save(output_path)
    print(f"Presentation saved successfully to: {output_path}")

if __name__ == '__main__':
    create_presentation()
