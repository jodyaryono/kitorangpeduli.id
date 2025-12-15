<?php

// This file contains realistic questionnaire templates for Jayapura city
// Each OPD will have 2 questionnaires with 10-20 questions each

return [
    // DINAS KESEHATAN - 2 questionnaires
    [
        'opd_index' => 0,
        'title' => 'Survei Kepuasan Layanan Puskesmas',
        'description' => 'Evaluasi kepuasan masyarakat terhadap layanan Puskesmas di Kota Jayapura',
        'questions' => [
            ['text' => 'Seberapa sering Anda mengunjungi Puskesmas dalam 6 bulan terakhir?', 'type' => 'single_choice', 'options' => ['Tidak pernah', '1-2 kali', '3-5 kali', 'Lebih dari 5 kali']],
            ['text' => 'Bagaimana penilaian Anda terhadap kebersihan ruang tunggu Puskesmas?', 'type' => 'scale'],
            ['text' => 'Apakah waktu tunggu untuk mendapat pelayanan sudah memuaskan?', 'type' => 'single_choice', 'options' => ['Sangat memuaskan (< 15 menit)', 'Memuaskan (15-30 menit)', 'Cukup (30-60 menit)', 'Kurang (> 60 menit)']],
            ['text' => 'Bagaimana sikap dan keramahan petugas kesehatan?', 'type' => 'scale'],
            ['text' => 'Apakah penjelasan dokter/perawat mudah dipahami?', 'type' => 'single_choice', 'options' => ['Sangat jelas', 'Jelas', 'Cukup jelas', 'Kurang jelas', 'Tidak jelas']],
            ['text' => 'Fasilitas apa saja yang perlu ditingkatkan? (pilih semua yang sesuai)', 'type' => 'multiple_choice', 'options' => ['Ruang tunggu', 'Toilet', 'Peralatan medis', 'Obat-obatan', 'Tempat parkir', 'Ruang periksa']],
            ['text' => 'Apakah ketersediaan obat di Puskesmas sudah memadai?', 'type' => 'single_choice', 'options' => ['Selalu tersedia', 'Sering tersedia', 'Kadang-kadang tersedia', 'Jarang tersedia', 'Tidak pernah tersedia']],
            ['text' => 'Bagaimana sistem antrian di Puskesmas?', 'type' => 'single_choice', 'options' => ['Sangat teratur', 'Teratur', 'Cukup teratur', 'Kurang teratur', 'Tidak teratur']],
            ['text' => 'Apakah Anda puas dengan biaya layanan kesehatan di Puskesmas?', 'type' => 'single_choice', 'options' => ['Sangat puas', 'Puas', 'Cukup puas', 'Kurang puas', 'Tidak puas']],
            ['text' => 'Layanan apa yang paling sering Anda gunakan?', 'type' => 'multiple_choice', 'options' => ['Pemeriksaan umum', 'Vaksinasi', 'KIA/KB', 'Gigi', 'Laboratorium', 'Rawat luka']],
            ['text' => 'Apakah jam operasional Puskesmas sudah sesuai dengan kebutuhan Anda?', 'type' => 'single_choice', 'options' => ['Sangat sesuai', 'Sesuai', 'Cukup sesuai', 'Kurang sesuai', 'Tidak sesuai']],
            ['text' => 'Saran dan masukan untuk peningkatan layanan Puskesmas', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 0,
        'title' => 'Survei Program Kesehatan Masyarakat',
        'description' => 'Penilaian program-program kesehatan masyarakat di Kota Jayapura',
        'questions' => [
            ['text' => 'Apakah Anda mengetahui program Posyandu di lingkungan Anda?', 'type' => 'single_choice', 'options' => ['Sangat tahu', 'Tahu', 'Kurang tahu', 'Tidak tahu']],
            ['text' => 'Seberapa aktif Anda berpartisipasi dalam kegiatan Posyandu?', 'type' => 'single_choice', 'options' => ['Sangat aktif (setiap bulan)', 'Aktif (sering hadir)', 'Kadang-kadang', 'Jarang', 'Tidak pernah']],
            ['text' => 'Apakah anak Anda sudah mendapat imunisasi lengkap?', 'type' => 'single_choice', 'options' => ['Ya, lengkap', 'Belum lengkap', 'Sedang proses', 'Tidak ada anak', 'Tidak tahu']],
            ['text' => 'Bagaimana penilaian Anda terhadap program kesehatan ibu dan anak?', 'type' => 'scale'],
            ['text' => 'Apakah Anda pernah mengikuti penyuluhan kesehatan?', 'type' => 'single_choice', 'options' => ['Sering (lebih dari 3 kali)', 'Pernah (1-3 kali)', 'Belum pernah']],
            ['text' => 'Topik penyuluhan apa yang Anda butuhkan? (pilih semua yang sesuai)', 'type' => 'multiple_choice', 'options' => ['Gizi seimbang', 'Penyakit menular', 'Kesehatan reproduksi', 'Pola hidup sehat', 'Sanitasi lingkungan', 'Kesehatan mental']],
            ['text' => 'Apakah lingkungan tempat tinggal Anda sudah memiliki jamban sehat?', 'type' => 'single_choice', 'options' => ['Ya, milik sendiri', 'Ya, milik bersama', 'Tidak ada', 'Masih menggunakan jamban tradisional']],
            ['text' => 'Apakah Anda atau keluarga memiliki kartu BPJS Kesehatan?', 'type' => 'single_choice', 'options' => ['Ya, semua anggota keluarga', 'Ya, sebagian', 'Tidak ada', 'Sedang proses']],
            ['text' => 'Bagaimana pengetahuan Anda tentang pola hidup bersih dan sehat (PHBS)?', 'type' => 'scale'],
            ['text' => 'Apakah Anda rutin mencuci tangan dengan sabun?', 'type' => 'single_choice', 'options' => ['Selalu', 'Sering', 'Kadang-kadang', 'Jarang', 'Tidak pernah']],
            ['text' => 'Program kesehatan apa yang perlu ditingkatkan di lingkungan Anda?', 'type' => 'textarea'],
        ]
    ],
    // DINAS PENDIDIKAN - 2 questionnaires
    [
        'opd_index' => 1,
        'title' => 'Survei Kualitas Pendidikan Dasar',
        'description' => 'Evaluasi kualitas pendidikan SD/SMP di Kota Jayapura',
        'questions' => [
            ['text' => 'Apakah anak Anda saat ini bersekolah?', 'type' => 'single_choice', 'options' => ['Ya, SD', 'Ya, SMP', 'Ya, SMA/SMK', 'Tidak sekolah', 'Belum usia sekolah']],
            ['text' => 'Bagaimana penilaian Anda terhadap kualitas guru di sekolah anak Anda?', 'type' => 'scale'],
            ['text' => 'Apakah fasilitas belajar di sekolah sudah memadai?', 'type' => 'single_choice', 'options' => ['Sangat memadai', 'Memadai', 'Cukup memadai', 'Kurang memadai', 'Tidak memadai']],
            ['text' => 'Berapa jarak rumah ke sekolah?', 'type' => 'single_choice', 'options' => ['< 1 km', '1-3 km', '3-5 km', '> 5 km']],
            ['text' => 'Bagaimana cara anak Anda berangkat ke sekolah?', 'type' => 'single_choice', 'options' => ['Jalan kaki', 'Sepeda', 'Kendaraan umum', 'Diantar orang tua', 'Lainnya']],
            ['text' => 'Apakah sekolah memiliki perpustakaan yang memadai?', 'type' => 'single_choice', 'options' => ['Ya, sangat lengkap', 'Ya, cukup lengkap', 'Ada tapi kurang lengkap', 'Tidak ada']],
            ['text' => 'Fasilitas apa yang perlu ditambah? (pilih semua yang sesuai)', 'type' => 'multiple_choice', 'options' => ['Ruang kelas', 'Komputer/lab', 'Buku pelajaran', 'Alat olahraga', 'Perpustakaan', 'Toilet']],
            ['text' => 'Bagaimana prestasi akademik anak Anda?', 'type' => 'single_choice', 'options' => ['Sangat baik', 'Baik', 'Cukup', 'Perlu peningkatan']],
            ['text' => 'Apakah anak Anda mengikuti les tambahan di luar sekolah?', 'type' => 'single_choice', 'options' => ['Ya, rutin', 'Ya, kadang-kadang', 'Tidak']],
            ['text' => 'Berapa rata-rata biaya pendidikan per bulan?', 'type' => 'single_choice', 'options' => ['< Rp 100.000', 'Rp 100.000 - 300.000', 'Rp 300.000 - 500.000', '> Rp 500.000']],
            ['text' => 'Apakah anak Anda menerima bantuan pendidikan (KIP/beasiswa)?', 'type' => 'single_choice', 'options' => ['Ya, menerima KIP', 'Ya, menerima beasiswa lain', 'Tidak menerima']],
            ['text' => 'Kendala apa yang dihadapi dalam pendidikan anak?', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 1,
        'title' => 'Survei Fasilitas dan Sarana Pendidikan',
        'description' => 'Penilaian kondisi fasilitas dan sarana pendidikan',
        'questions' => [
            ['text' => 'Bagaimana kondisi gedung sekolah anak Anda?', 'type' => 'scale'],
            ['text' => 'Apakah ruang kelas cukup untuk menampung siswa?', 'type' => 'single_choice', 'options' => ['Sangat cukup', 'Cukup', 'Agak sesak', 'Terlalu sesak']],
            ['text' => 'Bagaimana kondisi toilet/kamar mandi di sekolah?', 'type' => 'single_choice', 'options' => ['Sangat bersih', 'Bersih', 'Cukup bersih', 'Kurang bersih', 'Kotor']],
            ['text' => 'Apakah sekolah memiliki akses internet?', 'type' => 'single_choice', 'options' => ['Ya, sangat lancar', 'Ya, cukup lancar', 'Ya, tapi lambat', 'Tidak ada']],
            ['text' => 'Bagaimana ketersediaan buku pelajaran?', 'type' => 'single_choice', 'options' => ['Sangat lengkap', 'Lengkap', 'Cukup', 'Kurang', 'Sangat kurang']],
            ['text' => 'Apakah sekolah memiliki laboratorium?', 'type' => 'multiple_choice', 'options' => ['Lab IPA', 'Lab Komputer', 'Lab Bahasa', 'Tidak ada lab']],
            ['text' => 'Bagaimana kondisi meja dan kursi belajar?', 'type' => 'single_choice', 'options' => ['Sangat baik', 'Baik', 'Cukup baik', 'Rusak sebagian', 'Banyak yang rusak']],
            ['text' => 'Apakah sekolah memiliki lapangan olahraga?', 'type' => 'single_choice', 'options' => ['Ya, lengkap', 'Ya, sederhana', 'Tidak ada']],
            ['text' => 'Bagaimana kebersihan lingkungan sekolah?', 'type' => 'scale'],
            ['text' => 'Apakah ada program makan siang di sekolah?', 'type' => 'single_choice', 'options' => ['Ya, gratis', 'Ya, berbayar', 'Tidak ada']],
            ['text' => 'Saran untuk perbaikan fasilitas sekolah', 'type' => 'textarea'],
        ]
    ],
    // DINAS PEKERJAAN UMUM - 2 questionnaires
    [
        'opd_index' => 2,
        'title' => 'Survei Kondisi Infrastruktur Jalan',
        'description' => 'Evaluasi kondisi jalan dan infrastruktur transportasi di Kota Jayapura',
        'questions' => [
            ['text' => 'Bagaimana kondisi jalan utama di lingkungan Anda?', 'type' => 'scale'],
            ['text' => 'Jenis kerusakan jalan yang sering Anda temui?', 'type' => 'multiple_choice', 'options' => ['Lubang', 'Retak-retak', 'Permukaan bergelombang', 'Banjir saat hujan', 'Longsor', 'Tidak ada kerusakan']],
            ['text' => 'Seberapa sering jalan di lingkungan Anda mengalami kerusakan?', 'type' => 'single_choice', 'options' => ['Sangat sering (setiap bulan)', 'Sering (setiap 3 bulan)', 'Kadang-kadang (setiap 6 bulan)', 'Jarang (setahun sekali)', 'Tidak pernah']],
            ['text' => 'Apakah penerangan jalan di lingkungan Anda sudah memadai?', 'type' => 'single_choice', 'options' => ['Ya, sangat terang', 'Ya, cukup terang', 'Kurang terang', 'Sangat kurang', 'Tidak ada penerangan']],
            ['text' => 'Bagaimana kondisi trotoar untuk pejalan kaki?', 'type' => 'single_choice', 'options' => ['Sangat baik dan lebar', 'Baik tapi sempit', 'Rusak sebagian', 'Rusak parah', 'Tidak ada trotoar']],
            ['text' => 'Apakah drainase/selokan di jalan berfungsi dengan baik?', 'type' => 'single_choice', 'options' => ['Ya, sangat baik', 'Baik', 'Kurang baik', 'Tidak berfungsi', 'Tidak ada drainase']],
            ['text' => 'Berapa lama waktu tempuh dari rumah ke pusat kota?', 'type' => 'single_choice', 'options' => ['< 15 menit', '15-30 menit', '30-60 menit', '> 60 menit']],
            ['text' => 'Kendala transportasi apa yang sering Anda hadapi?', 'type' => 'multiple_choice', 'options' => ['Macet', 'Jalan rusak', 'Transportasi umum minim', 'Biaya mahal', 'Jarak jauh', 'Tidak ada kendala']],
            ['text' => 'Apakah rambu-rambu lalu lintas sudah lengkap?', 'type' => 'single_choice', 'options' => ['Sangat lengkap', 'Cukup lengkap', 'Kurang lengkap', 'Tidak ada']],
            ['text' => 'Nama jalan atau lokasi yang paling membutuhkan perbaikan', 'type' => 'text'],
            ['text' => 'Saran untuk peningkatan infrastruktur jalan', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 2,
        'title' => 'Survei Fasilitas Umum dan Sanitasi',
        'description' => 'Penilaian fasilitas umum dan sistem sanitasi di Kota Jayapura',
        'questions' => [
            ['text' => 'Apakah rumah Anda memiliki akses air bersih?', 'type' => 'single_choice', 'options' => ['Ya, PDAM lancar', 'Ya, PDAM tapi sering macet', 'Sumur', 'Air hujan', 'Membeli air']],
            ['text' => 'Berapa jam per hari air PDAM mengalir?', 'type' => 'single_choice', 'options' => ['24 jam', '12-24 jam', '6-12 jam', '< 6 jam', 'Tidak pakai PDAM']],
            ['text' => 'Bagaimana kualitas air yang Anda gunakan?', 'type' => 'scale'],
            ['text' => 'Jenis toilet/jamban yang Anda gunakan?', 'type' => 'single_choice', 'options' => ['Jamban sehat dengan septic tank', 'Jamban cemplung', 'Jamban umum', 'Tidak ada jamban']],
            ['text' => 'Bagaimana sistem pembuangan limbah rumah tangga Anda?', 'type' => 'single_choice', 'options' => ['Septic tank pribadi', 'Septic tank komunal', 'Langsung ke sungai/laut', 'Tidak ada sistem']],
            ['text' => 'Apakah ada taman atau ruang terbuka hijau di lingkungan Anda?', 'type' => 'single_choice', 'options' => ['Ada dan terawat', 'Ada tapi kurang terawat', 'Tidak ada']],
            ['text' => 'Fasilitas umum apa yang perlu ditambah? (pilih semua yang sesuai)', 'type' => 'multiple_choice', 'options' => ['Taman bermain', 'Lapangan olahraga', 'Toilet umum', 'Tempat ibadah', 'Halte', 'Tempat parkir']],
            ['text' => 'Bagaimana kondisi jembatan di wilayah Anda?', 'type' => 'single_choice', 'options' => ['Sangat baik', 'Baik', 'Perlu perbaikan', 'Rusak', 'Tidak ada jembatan']],
            ['text' => 'Apakah lingkungan Anda rawan banjir?', 'type' => 'single_choice', 'options' => ['Tidak pernah banjir', 'Banjir saat hujan deras', 'Sering banjir', 'Selalu banjir saat hujan']],
            ['text' => 'Masalah infrastruktur prioritas yang harus segera ditangani', 'type' => 'textarea'],
        ]
    ],
    // DINAS TENAGA KERJA - 2 questionnaires (Petani & Peternakan = Tenaga Kerja Sektor Pertanian)
    [
        'opd_index' => 5,
        'title' => 'Survei Petani dan Lahan Pertanian',
        'description' => 'Survei kondisi petani dan pengembangan pertanian di Kota Jayapura',
        'questions' => [
            ['text' => 'Apakah Anda memiliki lahan pertanian?', 'type' => 'single_choice', 'options' => ['Ya, lahan sendiri', 'Ya, lahan sewa', 'Ya, lahan pinjam', 'Tidak memiliki']],
            ['text' => 'Berapa luas lahan pertanian yang Anda miliki/kelola?', 'type' => 'single_choice', 'options' => ['< 500 m²', '500-1000 m²', '1000-5000 m²', '> 5000 m²', 'Tidak punya lahan']],
            ['text' => 'Jenis tanaman apa yang Anda budidayakan?', 'type' => 'multiple_choice', 'options' => ['Padi', 'Sayuran', 'Buah-buahan', 'Umbi-umbian', 'Tanaman hias', 'Tidak bertani']],
            ['text' => 'Bagaimana hasil panen Anda dalam 6 bulan terakhir?', 'type' => 'single_choice', 'options' => ['Sangat baik (untung besar)', 'Baik (cukup untung)', 'Cukup (impas)', 'Kurang (rugi)', 'Gagal panen']],
            ['text' => 'Kendala utama dalam bercocok tanam?', 'type' => 'multiple_choice', 'options' => ['Hama dan penyakit', 'Modal', 'Irigasi', 'Cuaca', 'Pemasaran hasil', 'Tidak ada kendala']],
            ['text' => 'Apakah Anda pernah mendapat bantuan alat pertanian dari pemerintah?', 'type' => 'single_choice', 'options' => ['Ya, sangat membantu', 'Ya, cukup membantu', 'Ya, tapi kurang sesuai', 'Belum pernah']],
            ['text' => 'Apakah Anda menggunakan pupuk untuk tanaman?', 'type' => 'single_choice', 'options' => ['Ya, pupuk organik', 'Ya, pupuk kimia', 'Ya, kombinasi', 'Tidak menggunakan pupuk']],
            ['text' => 'Bagaimana sistem irigasi di lahan Anda?', 'type' => 'single_choice', 'options' => ['Irigasi teknis lancar', 'Irigasi sederhana', 'Mengandalkan air hujan', 'Tidak ada sistem irigasi']],
            ['text' => 'Apakah Anda tergabung dalam kelompok tani?', 'type' => 'single_choice', 'options' => ['Ya, aktif', 'Ya, tapi kurang aktif', 'Tidak tergabung']],
            ['text' => 'Ke mana Anda menjual hasil panen?', 'type' => 'single_choice', 'options' => ['Pasar tradisional', 'Tengkulak', 'Langsung ke konsumen', 'Koperasi', 'Belum panen']],
            ['text' => 'Bantuan apa yang paling dibutuhkan petani?', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 5,
        'title' => 'Survei Peternakan dan Perikanan',
        'description' => 'Evaluasi usaha peternakan dan perikanan masyarakat',
        'questions' => [
            ['text' => 'Apakah Anda memelihara hewan ternak?', 'type' => 'single_choice', 'options' => ['Ya, ternak besar (sapi/kerbau)', 'Ya, ternak kecil (kambing/babi)', 'Ya, unggas (ayam/bebek)', 'Tidak beternak']],
            ['text' => 'Berapa jumlah ternak yang Anda miliki?', 'type' => 'single_choice', 'options' => ['1-5 ekor', '6-10 ekor', '11-20 ekor', '> 20 ekor', 'Tidak punya ternak']],
            ['text' => 'Tujuan utama beternak?', 'type' => 'single_choice', 'options' => ['Dijual (komersial)', 'Konsumsi sendiri', 'Tabungan', 'Campuran']],
            ['text' => 'Kendala dalam beternak?', 'type' => 'multiple_choice', 'options' => ['Penyakit hewan', 'Pakan mahal', 'Lahan terbatas', 'Modal', 'Pemasaran', 'Tidak ada kendala']],
            ['text' => 'Apakah Anda melakukan usaha perikanan?', 'type' => 'single_choice', 'options' => ['Ya, budidaya ikan', 'Ya, nelayan', 'Ya, keduanya', 'Tidak']],
            ['text' => 'Jenis ikan apa yang Anda budidayakan/tangkap?', 'type' => 'multiple_choice', 'options' => ['Ikan air tawar', 'Ikan laut', 'Udang', 'Kepiting', 'Rumput laut', 'Tidak ada']],
            ['text' => 'Apakah Anda pernah mendapat pelatihan peternakan/perikanan?', 'type' => 'single_choice', 'options' => ['Ya, dari pemerintah', 'Ya, dari swasta', 'Belajar sendiri', 'Belum pernah']],
            ['text' => 'Bagaimana akses vaksin/obat untuk hewan ternak?', 'type' => 'single_choice', 'options' => ['Mudah didapat', 'Cukup mudah', 'Sulit', 'Sangat sulit', 'Tidak pernah butuh']],
            ['text' => 'Pendapatan per bulan dari peternakan/perikanan?', 'type' => 'single_choice', 'options' => ['< Rp 1 juta', 'Rp 1-3 juta', 'Rp 3-5 juta', '> Rp 5 juta', 'Tidak tentu']],
            ['text' => 'Program apa yang dibutuhkan untuk meningkatkan hasil peternakan/perikanan?', 'type' => 'textarea'],
        ]
    ],
    // DINAS PERINDUSTRIAN, PERDAGANGAN, KOPERASI & UKM - 2 questionnaires
    [
        'opd_index' => 3,
        'title' => 'Survei Pasar dan Perdagangan',
        'description' => 'Evaluasi kondisi pasar dan aktivitas perdagangan di Kota Jayapura',
        'questions' => [
            ['text' => 'Seberapa sering Anda berbelanja di pasar tradisional?', 'type' => 'single_choice', 'options' => ['Setiap hari', '2-3 kali seminggu', 'Seminggu sekali', 'Jarang', 'Tidak pernah']],
            ['text' => 'Pasar mana yang paling sering Anda kunjungi?', 'type' => 'text'],
            ['text' => 'Bagaimana kondisi kebersihan pasar?', 'type' => 'scale'],
            ['text' => 'Fasilitas pasar apa yang perlu diperbaiki?', 'type' => 'multiple_choice', 'options' => ['Toilet', 'Tempat parkir', 'Atap/kanopi', 'Drainase', 'Tempat sampah', 'Penerangan']],
            ['text' => 'Bagaimana ketersediaan barang kebutuhan pokok di pasar?', 'type' => 'single_choice', 'options' => ['Selalu lengkap', 'Sering lengkap', 'Kadang kosong', 'Sering kosong']],
            ['text' => 'Apakah harga barang di pasar terjangkau?', 'type' => 'single_choice', 'options' => ['Sangat terjangkau', 'Terjangkau', 'Cukup mahal', 'Sangat mahal']],
            ['text' => 'Apakah Anda lebih suka belanja di pasar tradisional atau modern?', 'type' => 'single_choice', 'options' => ['Pasar tradisional', 'Pasar modern/supermarket', 'Keduanya', 'Belanja online']],
            ['text' => 'Bagaimana pelayanan pedagang di pasar?', 'type' => 'scale'],
            ['text' => 'Apakah ada praktek kecurangan timbangan di pasar?', 'type' => 'single_choice', 'options' => ['Sering terjadi', 'Kadang-kadang', 'Jarang', 'Tidak pernah', 'Tidak tahu']],
            ['text' => 'Jam operasional pasar apakah sudah sesuai kebutuhan?', 'type' => 'single_choice', 'options' => ['Sangat sesuai', 'Sesuai', 'Perlu diperpanjang', 'Perlu disesuaikan']],
            ['text' => 'Saran untuk peningkatan kualitas pasar tradisional', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 3,
        'title' => 'Survei UMKM dan Koperasi',
        'description' => 'Survei pengembangan UMKM dan koperasi di Kota Jayapura',
        'questions' => [
            ['text' => 'Apakah Anda memiliki usaha sendiri?', 'type' => 'single_choice', 'options' => ['Ya, usaha mikro', 'Ya, usaha kecil', 'Ya, usaha menengah', 'Tidak punya usaha']],
            ['text' => 'Jenis usaha yang Anda jalankan?', 'type' => 'single_choice', 'options' => ['Kuliner', 'Fashion/pakaian', 'Kerajinan', 'Jasa', 'Perdagangan umum', 'Lainnya', 'Tidak berusaha']],
            ['text' => 'Sudah berapa lama usaha Anda berjalan?', 'type' => 'single_choice', 'options' => ['< 1 tahun', '1-3 tahun', '3-5 tahun', '> 5 tahun', 'Belum mulai']],
            ['text' => 'Berapa omzet usaha Anda per bulan?', 'type' => 'single_choice', 'options' => ['< Rp 5 juta', 'Rp 5-10 juta', 'Rp 10-50 juta', '> Rp 50 juta']],
            ['text' => 'Apakah Anda memiliki izin usaha resmi?', 'type' => 'single_choice', 'options' => ['Ya, lengkap', 'Sebagian', 'Belum ada', 'Sedang proses']],
            ['text' => 'Kendala terbesar dalam menjalankan usaha?', 'type' => 'multiple_choice', 'options' => ['Modal', 'Pemasaran', 'Persaingan', 'Bahan baku', 'SDM', 'Perizinan']],
            ['text' => 'Apakah Anda pernah mendapat pelatihan kewirausahaan?', 'type' => 'single_choice', 'options' => ['Ya, dari pemerintah', 'Ya, dari swasta', 'Belajar mandiri', 'Belum pernah']],
            ['text' => 'Apakah Anda tergabung dalam koperasi?', 'type' => 'single_choice', 'options' => ['Ya, aktif', 'Ya, pasif', 'Tidak bergabung']],
            ['text' => 'Bagaimana cara Anda memasarkan produk?', 'type' => 'multiple_choice', 'options' => ['Toko fisik', 'Online/medsos', 'Titip toko', 'Event/bazar', 'Door to door']],
            ['text' => 'Bantuan apa yang paling dibutuhkan untuk mengembangkan usaha?', 'type' => 'textarea'],
        ]
    ],
    // DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL - 2 questionnaires
    [
        'opd_index' => 4,
        'title' => 'Survei Pelayanan Administrasi Kependudukan',
        'description' => 'Evaluasi layanan KTP, KK, Akta dan dokumen kependudukan',
        'questions' => [
            ['text' => 'Dokumen kependudukan apa yang pernah Anda urus?', 'type' => 'multiple_choice', 'options' => ['KTP', 'KK', 'Akta kelahiran', 'Akta kematian', 'Akta nikah', 'Belum pernah']],
            ['text' => 'Bagaimana kemudahan mengurus dokumen kependudukan?', 'type' => 'scale'],
            ['text' => 'Berapa lama waktu pengurusan KTP elektronik?', 'type' => 'single_choice', 'options' => ['1-3 hari', '4-7 hari', '1-2 minggu', '> 2 minggu', 'Belum selesai']],
            ['text' => 'Apakah seluruh anggota keluarga sudah memiliki KTP?', 'type' => 'single_choice', 'options' => ['Ya, semua', 'Sebagian besar', 'Sebagian kecil', 'Belum ada']],
            ['text' => 'Apakah persyaratan pengurusan dokumen jelas dan mudah dipenuhi?', 'type' => 'single_choice', 'options' => ['Sangat jelas', 'Cukup jelas', 'Membingungkan', 'Terlalu rumit']],
            ['text' => 'Bagaimana sikap petugas pelayanan?', 'type' => 'scale'],
            ['text' => 'Apakah Anda pernah diminta biaya tidak resmi?', 'type' => 'single_choice', 'options' => ['Tidak pernah', 'Pernah, sedikit', 'Sering', 'Selalu']],
            ['text' => 'Fasilitas apa yang perlu ditingkatkan di kantor Disdukcapil?', 'type' => 'multiple_choice', 'options' => ['Ruang tunggu', 'Toilet', 'Loket pelayanan', 'AC', 'Tempat parkir', 'Sistem antrian']],
            ['text' => 'Apakah layanan online sudah membantu proses pengurusan?', 'type' => 'single_choice', 'options' => ['Sangat membantu', 'Cukup membantu', 'Kurang membantu', 'Tidak tahu ada layanan online']],
            ['text' => 'Kendala apa yang Anda hadapi saat mengurus dokumen?', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 4,
        'title' => 'Survei Data Kependudukan dan Migrasi',
        'description' => 'Survei mobilitas dan perubahan data kependudukan',
        'questions' => [
            ['text' => 'Apakah Anda penduduk asli Jayapura?', 'type' => 'single_choice', 'options' => ['Ya, lahir di Jayapura', 'Tidak, pendatang', 'Lahir di Papua tapi bukan Jayapura']],
            ['text' => 'Jika pendatang, sudah berapa lama tinggal di Jayapura?', 'type' => 'single_choice', 'options' => ['< 1 tahun', '1-5 tahun', '5-10 tahun', '> 10 tahun', 'Penduduk asli']],
            ['text' => 'Apakah alamat di KTP sesuai dengan tempat tinggal sekarang?', 'type' => 'single_choice', 'options' => ['Ya, sesuai', 'Tidak, belum update', 'Tidak, sedang proses update']],
            ['text' => 'Apakah data di Kartu Keluarga sudah akurat dan terbaru?', 'type' => 'single_choice', 'options' => ['Ya, sangat akurat', 'Ada yang perlu diupdate', 'Banyak yang salah', 'Tidak punya KK']],
            ['text' => 'Berapa jumlah anggota keluarga dalam satu KK?', 'type' => 'single_choice', 'options' => ['1-2 orang', '3-4 orang', '5-6 orang', '> 6 orang']],
            ['text' => 'Apakah ada anggota keluarga yang meninggal tapi belum diurus akta kematiannya?', 'type' => 'single_choice', 'options' => ['Tidak ada', 'Ada, 1 orang', 'Ada, > 1 orang']],
            ['text' => 'Apakah semua anak di keluarga Anda sudah memiliki akta kelahiran?', 'type' => 'single_choice', 'options' => ['Ya, semua', 'Sebagian', 'Belum ada', 'Tidak punya anak']],
            ['text' => 'Alasan utama pindah ke Jayapura (jika pendatang)?', 'type' => 'single_choice', 'options' => ['Pekerjaan', 'Pendidikan', 'Ikut keluarga', 'Menikah', 'Lainnya', 'Penduduk asli']],
            ['text' => 'Apakah Anda berencana menetap di Jayapura?', 'type' => 'single_choice', 'options' => ['Ya, selamanya', 'Ya, beberapa tahun lagi', 'Belum tahu', 'Tidak, akan pindah']],
            ['text' => 'Saran untuk peningkatan sistem administrasi kependudukan', 'type' => 'textarea'],
        ]
    ],
    // DINAS SOSIAL - 2 questionnaires
    [
        'opd_index' => 7,
        'title' => 'Survei Bantuan Sosial dan Kesejahteraan',
        'description' => 'Evaluasi program bantuan sosial dan kesejahteraan masyarakat',
        'questions' => [
            ['text' => 'Apakah keluarga Anda termasuk penerima bantuan sosial?', 'type' => 'single_choice', 'options' => ['Ya, rutin menerima', 'Pernah menerima', 'Belum pernah', 'Menolak bantuan']],
            ['text' => 'Jenis bantuan sosial apa yang pernah Anda terima?', 'type' => 'multiple_choice', 'options' => ['PKH', 'BPNT/sembako', 'BST', 'Raskin/rastra', 'Bantuan tunai langsung', 'Belum pernah']],
            ['text' => 'Apakah bantuan sosial tepat sasaran dan sesuai kebutuhan?', 'type' => 'scale'],
            ['text' => 'Bagaimana proses pendaftaran sebagai penerima bantuan?', 'type' => 'single_choice', 'options' => ['Mudah dan jelas', 'Cukup mudah', 'Rumit', 'Sangat rumit', 'Tidak tahu caranya']],
            ['text' => 'Apakah bantuan yang diterima tepat waktu?', 'type' => 'single_choice', 'options' => ['Selalu tepat waktu', 'Sering telat', 'Selalu telat', 'Tidak tentu', 'Tidak pernah terima']],
            ['text' => 'Berapa penghasilan rata-rata keluarga Anda per bulan?', 'type' => 'single_choice', 'options' => ['< Rp 1 juta', 'Rp 1-2 juta', 'Rp 2-4 juta', 'Rp 4-6 juta', '> Rp 6 juta']],
            ['text' => 'Apakah kebutuhan dasar keluarga sudah terpenuhi?', 'type' => 'single_choice', 'options' => ['Sangat terpenuhi', 'Terpenuhi', 'Cukup terpenuhi', 'Kurang', 'Sangat kurang']],
            ['text' => 'Masalah sosial apa yang ada di lingkungan Anda?', 'type' => 'multiple_choice', 'options' => ['Kemiskinan', 'Pengangguran', 'Anak putus sekolah', 'Lansia terlantar', 'Disabilitas terabaikan', 'Tidak ada']],
            ['text' => 'Apakah ada warga di lingkungan Anda yang membutuhkan bantuan sosial tapi belum terdata?', 'type' => 'single_choice', 'options' => ['Ya, banyak', 'Ya, beberapa', 'Tidak ada', 'Tidak tahu']],
            ['text' => 'Jenis bantuan sosial apa yang paling dibutuhkan masyarakat?', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 7,
        'title' => 'Survei Perlindungan Anak dan Keluarga',
        'description' => 'Survei perlindungan anak, lansia, dan penyandang disabilitas',
        'questions' => [
            ['text' => 'Apakah ada anak di keluarga Anda yang putus sekolah?', 'type' => 'single_choice', 'options' => ['Tidak ada', 'Ada, 1 anak', 'Ada, lebih dari 1', 'Tidak punya anak']],
            ['text' => 'Apakah Anda mengetahui kasus kekerasan terhadap anak di lingkungan Anda?', 'type' => 'single_choice', 'options' => ['Tidak tahu', 'Pernah dengar', 'Ya, tahu beberapa kasus', 'Ya, sering terjadi']],
            ['text' => 'Apakah ada anggota keluarga penyandang disabilitas?', 'type' => 'single_choice', 'options' => ['Tidak ada', 'Ya, 1 orang', 'Ya, lebih dari 1']],
            ['text' => 'Jika ada, apakah penyandang disabilitas mendapat bantuan/perhatian khusus?', 'type' => 'single_choice', 'options' => ['Ya, dari pemerintah', 'Ya, dari keluarga', 'Ya, dari LSM', 'Belum mendapat', 'Tidak ada disabilitas']],
            ['text' => 'Apakah ada lansia di keluarga Anda yang membutuhkan perawatan khusus?', 'type' => 'single_choice', 'options' => ['Tidak ada lansia', 'Ada, tapi masih mandiri', 'Ada, butuh perawatan', 'Ada, dalam perawatan']],
            ['text' => 'Fasilitas ramah anak apa yang dibutuhkan di lingkungan Anda?', 'type' => 'multiple_choice', 'options' => ['Taman bermain', 'Perpustakaan anak', 'Sanggar belajar', 'Lapangan olahraga', 'Posyandu aktif', 'PAUD/TK']],
            ['text' => 'Apakah Anda tahu cara melaporkan kasus kekerasan dalam rumah tangga?', 'type' => 'single_choice', 'options' => ['Ya, sangat tahu', 'Tahu sedikit', 'Kurang tahu', 'Tidak tahu sama sekali']],
            ['text' => 'Bagaimana kondisi tempat tinggal Anda?', 'type' => 'single_choice', 'options' => ['Rumah permanen milik sendiri', 'Rumah semi permanen', 'Rumah tidak layak', 'Kontrak/sewa', 'Menumpang']],
            ['text' => 'Apakah lingkungan tempat tinggal aman untuk anak-anak bermain?', 'type' => 'scale'],
            ['text' => 'Program perlindungan sosial apa yang paling dibutuhkan?', 'type' => 'textarea'],
        ]
    ],
    // DINAS LINGKUNGAN HIDUP DAN KEBERSIHAN - 2 questionnaires
    [
        'opd_index' => 8,
        'title' => 'Survei Pengelolaan Sampah dan Kebersihan',
        'description' => 'Evaluasi pengelolaan sampah dan kebersihan lingkungan',
        'questions' => [
            ['text' => 'Bagaimana Anda mengelola sampah rumah tangga?', 'type' => 'single_choice', 'options' => ['Diangkut petugas', 'Dibakar', 'Ditimbun/dikubur', 'Dibuang ke sungai/laut', 'Didaur ulang']],
            ['text' => 'Seberapa sering petugas sampah mengangkut sampah di lingkungan Anda?', 'type' => 'single_choice', 'options' => ['Setiap hari', '2-3 kali seminggu', 'Seminggu sekali', 'Tidak teratur', 'Tidak ada petugas']],
            ['text' => 'Apakah Anda memisahkan sampah organik dan anorganik?', 'type' => 'single_choice', 'options' => ['Ya, selalu', 'Kadang-kadang', 'Jarang', 'Tidak pernah']],
            ['text' => 'Bagaimana kondisi kebersihan lingkungan tempat tinggal Anda?', 'type' => 'scale'],
            ['text' => 'Masalah sampah apa yang sering terjadi di lingkungan Anda?', 'type' => 'multiple_choice', 'options' => ['Sampah menumpuk', 'Bau tidak sedap', 'Sampah berserakan', 'TPS penuh', 'Sampah di sungai', 'Tidak ada masalah']],
            ['text' => 'Apakah ada tempat pembuangan sampah sementara (TPS) di dekat rumah?', 'type' => 'single_choice', 'options' => ['Ya, < 100 meter', 'Ya, 100-500 meter', 'Ya, > 500 meter', 'Tidak ada']],
            ['text' => 'Apakah Anda atau lingkungan melakukan daur ulang sampah?', 'type' => 'single_choice', 'options' => ['Ya, aktif', 'Ya, kadang-kadang', 'Pernah coba', 'Belum pernah']],
            ['text' => 'Apakah Anda bersedia membayar retribusi sampah?', 'type' => 'single_choice', 'options' => ['Ya, sudah bayar rutin', 'Ya, bersedia tapi belum ada sistem', 'Tergantung jumlahnya', 'Tidak bersedia']],
            ['text' => 'Berapa biaya retribusi sampah yang wajar menurut Anda?', 'type' => 'single_choice', 'options' => ['< Rp 10.000/bulan', 'Rp 10.000-20.000', 'Rp 20.000-50.000', '> Rp 50.000']],
            ['text' => 'Saran untuk peningkatan pengelolaan sampah di lingkungan Anda', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 8,
        'title' => 'Survei Kualitas Lingkungan dan Konservasi',
        'description' => 'Penilaian kualitas lingkungan dan upaya konservasi',
        'questions' => [
            ['text' => 'Bagaimana kualitas udara di lingkungan Anda?', 'type' => 'scale'],
            ['text' => 'Apakah ada polusi udara dari kendaraan atau industri?', 'type' => 'single_choice', 'options' => ['Tidak ada', 'Sedikit', 'Cukup mengganggu', 'Sangat mengganggu']],
            ['text' => 'Apakah lingkungan Anda memiliki ruang terbuka hijau?', 'type' => 'single_choice', 'options' => ['Ya, banyak', 'Ya, cukup', 'Sedikit', 'Tidak ada']],
            ['text' => 'Apakah Anda menanam pohon atau tanaman di rumah?', 'type' => 'single_choice', 'options' => ['Ya, banyak (> 5 pohon)', 'Ya, beberapa (1-5 pohon)', 'Hanya tanaman hias', 'Tidak ada']],
            ['text' => 'Masalah lingkungan apa yang paling mengganggu?', 'type' => 'multiple_choice', 'options' => ['Banjir', 'Polusi udara', 'Polusi air', 'Kebisingan', 'Sampah', 'Longsor', 'Tidak ada']],
            ['text' => 'Apakah sungai di sekitar Anda masih bersih?', 'type' => 'single_choice', 'options' => ['Sangat bersih', 'Cukup bersih', 'Kotor', 'Sangat kotor', 'Tidak ada sungai']],
            ['text' => 'Apakah Anda pernah terlibat dalam kegiatan bersih-bersih lingkungan?', 'type' => 'single_choice', 'options' => ['Sering (setiap bulan)', 'Kadang-kadang', 'Jarang', 'Tidak pernah']],
            ['text' => 'Apakah ada program penghijauan di lingkungan Anda?', 'type' => 'single_choice', 'options' => ['Ya, aktif', 'Ya, tapi kurang aktif', 'Pernah ada', 'Tidak ada']],
            ['text' => 'Apakah Anda mengetahui tentang perubahan iklim dan dampaknya?', 'type' => 'single_choice', 'options' => ['Sangat paham', 'Cukup paham', 'Sedikit paham', 'Tidak paham']],
            ['text' => 'Program lingkungan apa yang perlu ditingkatkan di wilayah Anda?', 'type' => 'textarea'],
        ]
    ],
    // DINAS PEMBERDAYAAN PEREMPUAN, PERLINDUNGAN ANAK DAN KB - 2 questionnaires (Wisata Ramah Keluarga)
    [
        'opd_index' => 9,
        'title' => 'Survei Potensi dan Destinasi Wisata',
        'description' => 'Evaluasi potensi wisata dan destinasi di Kota Jayapura',
        'questions' => [
            ['text' => 'Apakah Anda mengetahui objek wisata di Kota Jayapura?', 'type' => 'single_choice', 'options' => ['Sangat tahu (> 5 tempat)', 'Tahu beberapa (3-5 tempat)', 'Tahu sedikit (1-2 tempat)', 'Tidak tahu']],
            ['text' => 'Objek wisata mana yang pernah Anda kunjungi?', 'type' => 'multiple_choice', 'options' => ['Pantai Base-G', 'Taman Imbi', 'Bukit Teletubbies', 'Museum Loka Budaya', 'Danau Sentani', 'Belum pernah wisata']],
            ['text' => 'Seberapa sering Anda berwisata dalam setahun?', 'type' => 'single_choice', 'options' => ['Sering (> 5 kali)', 'Kadang (3-5 kali)', 'Jarang (1-2 kali)', 'Tidak pernah']],
            ['text' => 'Bagaimana kondisi objek wisata yang pernah Anda kunjungi?', 'type' => 'scale'],
            ['text' => 'Fasilitas apa yang kurang di objek wisata?', 'type' => 'multiple_choice', 'options' => ['Toilet', 'Tempat parkir', 'Warung makan', 'Gazebo/tempat istirahat', 'Papan informasi', 'Akses jalan']],
            ['text' => 'Apakah harga tiket masuk wisata terjangkau?', 'type' => 'single_choice', 'options' => ['Sangat terjangkau', 'Terjangkau', 'Agak mahal', 'Mahal']],
            ['text' => 'Apakah akses menuju objek wisata mudah?', 'type' => 'single_choice', 'options' => ['Sangat mudah', 'Mudah', 'Cukup sulit', 'Sangat sulit']],
            ['text' => 'Potensi wisata apa yang bisa dikembangkan di daerah Anda?', 'type' => 'multiple_choice', 'options' => ['Wisata alam', 'Wisata budaya', 'Wisata kuliner', 'Wisata religi', 'Wisata sejarah', 'Tidak ada']],
            ['text' => 'Apakah Anda tertarik menjadi pemandu wisata lokal?', 'type' => 'single_choice', 'options' => ['Sangat tertarik', 'Tertarik', 'Kurang tertarik', 'Tidak tertarik']],
            ['text' => 'Saran untuk pengembangan pariwisata di Jayapura', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 9,
        'title' => 'Survei Budaya dan Event Pariwisata',
        'description' => 'Penilaian pelestarian budaya dan event pariwisata',
        'questions' => [
            ['text' => 'Apakah Anda pernah mengikuti event budaya di Jayapura?', 'type' => 'single_choice', 'options' => ['Sering ikut', 'Pernah beberapa kali', 'Pernah sekali', 'Belum pernah']],
            ['text' => 'Event budaya/pariwisata apa yang pernah Anda ikuti?', 'type' => 'multiple_choice', 'options' => ['Festival Danau Sentani', 'Pekan Budaya Papua', 'Event musik', 'Event kuliner', 'Pameran kerajinan', 'Belum pernah']],
            ['text' => 'Bagaimana penilaian Anda terhadap event budaya yang ada?', 'type' => 'scale'],
            ['text' => 'Apakah Anda masih melestarikan budaya lokal Papua?', 'type' => 'single_choice', 'options' => ['Ya, sangat aktif', 'Ya, cukup aktif', 'Kadang-kadang', 'Tidak']],
            ['text' => 'Kebudayaan lokal apa yang masih Anda praktekkan?', 'type' => 'multiple_choice', 'options' => ['Tarian tradisional', 'Musik tradisional', 'Kerajinan tangan', 'Pakaian adat', 'Bahasa daerah', 'Tidak ada']],
            ['text' => 'Apakah generasi muda masih tertarik dengan budaya Papua?', 'type' => 'single_choice', 'options' => ['Sangat tertarik', 'Cukup tertarik', 'Kurang tertarik', 'Tidak tertarik']],
            ['text' => 'Produk kerajinan lokal apa yang bisa dijadikan oleh-oleh khas?', 'type' => 'text'],
            ['text' => 'Apakah Anda memiliki usaha di bidang pariwisata?', 'type' => 'single_choice', 'options' => ['Ya, homestay/penginapan', 'Ya, usaha kuliner', 'Ya, jual kerajinan', 'Ya, tour guide', 'Tidak ada']],
            ['text' => 'Kendala dalam mengembangkan usaha pariwisata?', 'type' => 'multiple_choice', 'options' => ['Modal', 'Promosi', 'Wisatawan sedikit', 'Infrastruktur', 'Perizinan', 'Tidak ada usaha']],
            ['text' => 'Event atau atraksi wisata apa yang perlu diadakan di Jayapura?', 'type' => 'textarea'],
        ]
    ],
    // DINAS PEMBERDAYAAN MASYARAKAT, PEMERINTAHAN KAMPUNG - 2 questionnaires
    [
        'opd_index' => 6,
        'title' => 'Survei Partisipasi Masyarakat Kampung',
        'description' => 'Evaluasi partisipasi dan pemberdayaan masyarakat kampung',
        'questions' => [
            ['text' => 'Seberapa aktif Anda dalam kegiatan kampung/kelurahan?', 'type' => 'scale'],
            ['text' => 'Kegiatan kemasyarakatan apa yang Anda ikuti?', 'type' => 'multiple_choice', 'options' => ['Gotong royong', 'Rapat RT/RW', 'Arisan', 'Pengajian/kebaktian', 'Posyandu', 'Karang taruna', 'Tidak ikut']],
            ['text' => 'Seberapa sering diadakan musyawarah kampung?', 'type' => 'single_choice', 'options' => ['Setiap bulan', 'Setiap 3 bulan', 'Setiap 6 bulan', 'Setahun sekali', 'Tidak pernah']],
            ['text' => 'Apakah aspirasi masyarakat didengar oleh kepala kampung/lurah?', 'type' => 'single_choice', 'options' => ['Sangat didengar', 'Cukup didengar', 'Kurang didengar', 'Tidak didengar']],
            ['text' => 'Bagaimana transparansi pengelolaan dana kampung?', 'type' => 'scale'],
            ['text' => 'Apakah Anda mengetahui program-program pemerintah untuk kampung?', 'type' => 'single_choice', 'options' => ['Sangat tahu', 'Cukup tahu', 'Kurang tahu', 'Tidak tahu']],
            ['text' => 'Program pemberdayaan apa yang sudah berjalan di kampung Anda?', 'type' => 'multiple_choice', 'options' => ['Pelatihan keterampilan', 'Bantuan modal usaha', 'Program kesehatan', 'Pendidikan', 'Infrastruktur', 'Belum ada']],
            ['text' => 'Apakah ada kelompok pemberdayaan perempuan di kampung Anda?', 'type' => 'single_choice', 'options' => ['Ada dan aktif', 'Ada tapi kurang aktif', 'Tidak ada']],
            ['text' => 'Kendala utama dalam pemberdayaan masyarakat?', 'type' => 'multiple_choice', 'options' => ['Partisipasi rendah', 'Dana terbatas', 'SDM kurang', 'Tidak ada program', 'Kurang sosialisasi']],
            ['text' => 'Program apa yang paling dibutuhkan untuk memajukan kampung?', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 6,
        'title' => 'Survei Kelembagaan dan Organisasi Masyarakat',
        'description' => 'Penilaian kelembagaan dan organisasi kemasyarakatan',
        'questions' => [
            ['text' => 'Apakah Anda tergabung dalam organisasi kemasyarakatan?', 'type' => 'single_choice', 'options' => ['Ya, sangat aktif', 'Ya, cukup aktif', 'Ya, tapi pasif', 'Tidak tergabung']],
            ['text' => 'Organisasi apa yang Anda ikuti?', 'type' => 'multiple_choice', 'options' => ['PKK', 'Karang taruna', 'RT/RW', 'Organisasi keagamaan', 'LSM', 'Koperasi', 'Tidak ada']],
            ['text' => 'Bagaimana kinerja organisasi masyarakat di kampung Anda?', 'type' => 'scale'],
            ['text' => 'Apakah ada forum warga atau musyawarah rutin?', 'type' => 'single_choice', 'options' => ['Ya, rutin setiap bulan', 'Ya, tapi tidak rutin', 'Jarang diadakan', 'Tidak ada']],
            ['text' => 'Masalah apa yang sering dibahas dalam forum warga?', 'type' => 'multiple_choice', 'options' => ['Keamanan', 'Kebersihan', 'Infrastruktur', 'Kesehatan', 'Pendidikan', 'Ekonomi']],
            ['text' => 'Apakah ada program pelatihan keterampilan untuk warga?', 'type' => 'single_choice', 'options' => ['Ada dan saya ikut', 'Ada tapi saya tidak ikut', 'Pernah ada', 'Tidak pernah ada']],
            ['text' => 'Pelatihan keterampilan apa yang Anda butuhkan?', 'type' => 'multiple_choice', 'options' => ['Menjahit', 'Memasak/kuliner', 'Kerajinan tangan', 'Pertanian/berkebun', 'Digital/komputer', 'Wirausaha']],
            ['text' => 'Apakah ada bantuan modal usaha dari pemerintah atau lembaga?', 'type' => 'single_choice', 'options' => ['Ya, mudah diakses', 'Ya, tapi prosesnya sulit', 'Tidak ada', 'Tidak tahu']],
            ['text' => 'Bagaimana koordinasi antar lembaga masyarakat di kampung Anda?', 'type' => 'scale'],
            ['text' => 'Saran untuk meningkatkan peran organisasi masyarakat', 'type' => 'textarea'],
        ]
    ],
    // DINAS PERINDUSTRIAN - 2 questionnaires
    [
        'opd_index' => 10,
        'title' => 'Survei Industri Kecil dan Menengah',
        'description' => 'Evaluasi perkembangan industri kecil dan menengah',
        'questions' => [
            ['text' => 'Apakah Anda memiliki usaha industri kecil/rumahan?', 'type' => 'single_choice', 'options' => ['Ya, industri makanan/minuman', 'Ya, industri kerajinan', 'Ya, industri lainnya', 'Tidak punya']],
            ['text' => 'Berapa lama usaha industri Anda berjalan?', 'type' => 'single_choice', 'options' => ['< 1 tahun', '1-3 tahun', '3-5 tahun', '> 5 tahun', 'Belum memulai']],
            ['text' => 'Berapa jumlah tenaga kerja dalam usaha Anda?', 'type' => 'single_choice', 'options' => ['1-2 orang', '3-5 orang', '6-10 orang', '> 10 orang', 'Tidak punya usaha']],
            ['text' => 'Dari mana Anda mendapat bahan baku produksi?', 'type' => 'single_choice', 'options' => ['Lokal (Jayapura)', 'Dalam Papua', 'Luar Papua', 'Impor', 'Tidak produksi']],
            ['text' => 'Kendala utama dalam produksi?', 'type' => 'multiple_choice', 'options' => ['Bahan baku mahal', 'Teknologi terbatas', 'Modal kurang', 'SDM kurang terampil', 'Listrik tidak stabil', 'Tidak ada kendala']],
            ['text' => 'Apakah produk Anda sudah memiliki izin edar (PIRT/BPOM)?', 'type' => 'single_choice', 'options' => ['Ya, sudah ada', 'Sedang proses', 'Belum ada', 'Tidak tahu caranya', 'Tidak produksi']],
            ['text' => 'Bagaimana cara Anda memasarkan produk?', 'type' => 'multiple_choice', 'options' => ['Jual langsung', 'Online/medsos', 'Toko/agen', 'Event/pameran', 'Belum produksi']],
            ['text' => 'Apakah Anda pernah mengikuti pameran produk industri?', 'type' => 'single_choice', 'options' => ['Sering (> 3 kali)', 'Pernah (1-3 kali)', 'Belum pernah', 'Tidak berminat']],
            ['text' => 'Omzet produksi per bulan?', 'type' => 'single_choice', 'options' => ['< Rp 5 juta', 'Rp 5-10 juta', 'Rp 10-50 juta', '> Rp 50 juta', 'Belum produksi']],
            ['text' => 'Bantuan apa yang dibutuhkan untuk mengembangkan industri?', 'type' => 'textarea'],
        ]
    ],
    [
        'opd_index' => 10,
        'title' => 'Survei Teknologi dan Inovasi Industri',
        'description' => 'Penilaian adopsi teknologi dan inovasi dalam industri',
        'questions' => [
            ['text' => 'Apakah Anda menggunakan mesin/teknologi dalam produksi?', 'type' => 'single_choice', 'options' => ['Ya, teknologi modern', 'Ya, teknologi sederhana', 'Masih manual', 'Tidak produksi']],
            ['text' => 'Bagaimana pengetahuan Anda tentang teknologi industri?', 'type' => 'scale'],
            ['text' => 'Apakah Anda pernah mendapat pelatihan teknologi industri?', 'type' => 'single_choice', 'options' => ['Ya, dari pemerintah', 'Ya, dari swasta', 'Belajar mandiri', 'Belum pernah']],
            ['text' => 'Kendala dalam adopsi teknologi?', 'type' => 'multiple_choice', 'options' => ['Harga mahal', 'Tidak tahu cara pakai', 'Tidak ada pelatihan', 'Listrik tidak stabil', 'Tidak perlu teknologi']],
            ['text' => 'Apakah produk Anda sudah memiliki merek/brand?', 'type' => 'single_choice', 'options' => ['Ya, terdaftar', 'Ya, belum terdaftar', 'Belum punya merek', 'Tidak produksi']],
            ['text' => 'Apakah Anda melakukan inovasi produk?', 'type' => 'single_choice', 'options' => ['Sering berinovasi', 'Kadang-kadang', 'Jarang', 'Tidak pernah']],
            ['text' => 'Sumber inspirasi inovasi produk?', 'type' => 'multiple_choice', 'options' => ['Permintaan pasar', 'Kompetitor', 'Media sosial', 'Pelatihan', 'Ide sendiri', 'Belum inovasi']],
            ['text' => 'Apakah Anda memanfaatkan internet untuk bisnis?', 'type' => 'single_choice', 'options' => ['Ya, maksimal', 'Ya, cukup', 'Sedikit', 'Tidak']],
            ['text' => 'Tantangan terbesar industri di Jayapura menurut Anda?', 'type' => 'single_choice', 'options' => ['Bahan baku', 'Modal', 'Teknologi', 'Pemasaran', 'Infrastruktur', 'SDM']],
            ['text' => 'Saran untuk memajukan sektor industri di Jayapura', 'type' => 'textarea'],
        ]
    ],
];
