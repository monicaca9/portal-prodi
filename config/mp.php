<?php
return [
    'apps'  => [
        'title' => env('APP_NAME', 'PORTAL PRODI'),
        'acronym_title' => env('APP_NAME_SMALL','PP'),
        'year_development' => 2019,
        'year_launch' => 2020,
        'first_development'  => '2019-12-01',
        'first_launch'      => '2020-01-31',
        'at_use'  => 1,
        'user'  => [
            'institute'     => 'Teknik Informatika',
            'acronym_institute' => 'TI',
            'logo'  => 'images/logo-unila.png'
        ],
        'version_apps'  => '0.1',
        'version_db'    => '0.01'
    ],
    'copyright' => [
        'year'  => 2019,
        'institute' => 'Siger Integra Informatika',
        'acronym_institute' => 'SIGMA',
        'logo'  => 'images/logo-sigma.png'
    ],
    'version_apps'  => [
        '0.1'   => '31-12-2019'
    ],
    'version_db'  => [
        '0.01'   => '31-12-2019'
    ],
    'exp_data_row'  => [
        'create_date'           => date('Y-m-d H:i:s'),
        'created_date'          => date('Y-m-d H:i:s'),
        'tgl_create'            => date('Y-m-d H:i:s'),
        'last_update'           => date('Y-m-d H:i:s'),
        'last_sync'             => date('Y-m-d H:i:s', time()-60),
        'waktu_expired_token'   => date('Y-m-d H:i:s', strtotime("+30 minutes")),
    ],
    'data_master'   => [
        'status_verifikasi'   => [
            0   => 'Pending',
            1   => 'Ada dan Valid',
            2   => 'Ada dan Tidak Valid',
            3   => 'Tidak Ada'
        ],
        'smt'   => [
            1   => 'Ganjil',
            2   => 'Genap',
            3   => 'Pendek'
        ],
        'jns_peran_mhs' => [
            1   => 'Ketua',
            2   => 'Anggota',
            3   => 'Individu/Mandiri'
        ],
        'status_validasi'   => [
            0   => 'Draft',
            1   => 'Diajukan',
            2   => 'Disetujui',
            3   => 'Ditolak',
            4   => 'Ditangguhkan',
        ],
        'status_pd' => [
            'A' => 'Aktif',
            'C' => 'Cuti',
            'D' => 'Drop Out',
            'L' => 'Lulus',
            'P' => 'Pindah',
            'K' => 'Keluar',
            'N' => 'Non-Aktif'
        ],
        'jk'    => [
            'L' => 'Laki-laki',
            'P' => 'Perempuan'
        ],
        'jalur_skripsi' => [
            0   => 'Non Skripsi',
            1   => 'Jalur Skripsi'
        ],
        'jenis_matkul'  => [
            'W' => 'Wajib Nasional',
            'A' => 'Wajib Program Studi',
            'B' => 'Pilihan',
            'C' => 'Peminatan',
//            'D' => 'Pilihan peminatan',
            'S' => 'Tugas akhir/Skripsi/Thesis/Disertasi'
        ],
        'mode_kuliah'  => [
            'O' => 'Online',
            'F' => 'Offline',
            'M' => 'Campuran',
        ],
        'lingkup_kelas'  => [
            1 => 'Internal',
            2 => 'External',
            3 => 'Campuran',
        ],
        'kelompok_matkul'   => [
            'A' => 'MPK (mata kuliah pengembangan kepribadian)',
            'B' => 'MKK (mata kuliah keilmuan dan keterampilan)',
            'C' => 'MKB (mata kuliah keahlian berkarya)',
            'D' => 'MPB (mata kuliah perilaku berkarya)',
            'E' => 'MBB (mata kuliah berkehidupan bermasyarakat)',
            'F' => 'MKU/MKDU (mata kuliah umum/mata kuliah dasar umum)',
            'G' => 'MKDK (mata kuliah dasar keahlian)',
            'H' => 'MKK <perlu diisi>'
        ],
        'jenis_capaian'     => [
            'CPL-PRODI' => 'Capaian Pembelajaran Lulusan Program Studi',
            'CPMK'      => 'Capaian Pembelajaran Mata Kuliah'
        ],
        'kategori_kegiatan_pembelajaran'    => [
            'A' => 'Pendahuluan',
            'B' => 'Penyajian',
            'C' => 'Penutup'
        ],
        'stat_prodi'    => [
            'A' => 'Aktif',
            'B' => 'Alih bentuk',
            'K' => 'Alih kelola',
            'N' => 'Non aktif',
            'H' => 'Dihapus'
        ],
        'status_periksa'    => [
            'N' => 'Belum diperiksa',
            'T' => 'Tidak valid',
            'L' => 'Tidak lengkap',
            'C' => 'Ada kecurangan',
            'Y' => 'Valid',
        ],
        'status_ajuan'  => [
            0   => 'Draft',
            1   => 'Diajukan',
            2   => 'Disetujui',
            3   => 'Ditolak',
            4   => 'Ditangguhkan'
        ],
        'status_ajuan_admin'  => [
            1   => 'Diajukan',
            2   => 'Disetujui',
            3   => 'Ditolak',
            4   => 'Ditangguhkan'
        ],
        'level_verifikasi'  => [
            1	=> 'Program Studi',
            2	=> 'Fakultas',
            3	=> 'PT',
            4	=> 'LLDIKTI',
            5	=> 'Kementerian'
        ],
        'level_verifikasi_prodi'  => [
            1	=> 'Peer Group',
            2	=> 'TPMPS',
            3	=> 'Ketua Program Studi',
            4	=> 'Sekertaris Jurusan',
            5	=> 'Ketua Jurusan',
            6	=> 'Dekan Fakultas',
        ],
        'status_beasiswa'  => [
            1	=> 'Baru',
            2	=> 'Perpanjang',
        ],
        'tipe_boolean'  => [
            0	=> 'Tidak',
            1	=> 'Ya',
        ],
        'peran_seminar'  => [
            1   => 'Pembimbing',
            2   => 'Penguji',
            3   => 'Promotor',
            4   => 'Co-Promotor',
            5   => 'Pembimbing Akademik',
            6   => 'Pembimbing Lapangan',
        ],
        'kps'   => [
            0   => 'Tidak',
            1   => 'Ya, Saya menggunakan KPS (Kartu Perlindungan Sosial)'
        ],
        'status_ubah_file'   => [
            0   => 'Tidak',
            1   => 'Ya, Saya ingin mengubah file dokumen'
        ],
        'jenis_ajuan'   => [
            'U'   => 'Ajuan Perubahan',
            'B'   => 'Ajuan Baru'
        ],
        'hari'  => [
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu'
        ],
        'waktu' => [
            '06:00','06:05','06:10','06:15','06:20','06:25','06:30','06:35','06:40','06:45','06:50','06:55',
            '07:00','07:05','07:10','07:15','07:20','07:25','07:30','07:35','07:40','07:45','07:50','07:55',
            '08:00','08:05','08:10','08:15','08:20','08:25','08:30','08:35','08:40','08:45','08:50','08:55',
            '09:00','09:05','09:10','09:15','09:20','09:25','09:30','09:35','09:40','09:45','09:50','09:55',
            '10:00','10:05','10:10','10:15','10:20','10:25','10:30','10:35','10:40','10:45','10:50','10:55',
            '11:00','11:05','11:10','11:15','11:20','11:25','11:30','11:35','11:40','11:45','11:50','11:55',
            '12:00','12:05','12:10','12:15','12:20','12:25','12:30','12:35','12:40','12:45','12:50','12:55',
            '13:00','13:05','13:10','13:15','13:20','13:25','13:30','13:35','13:40','13:45','13:50','13:55',
            '14:00','14:05','14:10','14:15','14:20','14:25','14:30','14:35','14:40','14:45','14:50','14:55',
            '15:00','15:05','15:10','15:15','15:20','15:25','15:30','15:35','15:40','15:45','15:50','15:55',
            '16:00','16:05','16:10','16:15','16:20','16:25','16:30','16:35','16:40','16:45','16:50','16:55',
            '17:00','17:05','17:10','17:15','17:20','17:25','17:30','17:35','17:40','17:45','17:50','17:55',
        ],
        'simulasi'  => 0,
        'alamat_unila'  => 'Jln. Soemantri Brojonegoro No. 1 Bandarlampung 35145',
        'telp_unila'    => 'Telp (0721)701609 ext 219',
        'status_daftar_praktikum'   => [
            0   => 'Menunggu persetujuan',
            1   => 'Disetujui',
            2   => 'Ditolak',
            3   => 'Ditangguhkan'
        ],
        'status_pengumpulan_laporan_praktikum'  => [
            0   => 'Belum mengumpulkan',
            1   => 'Sudah mengumpulkan',
            2   => 'Revisi',
            3   => 'Sudah Acc',
        ],
        'level_verifikasi_seminar'  => [
            1	=> 'Admin Prodi',
            2	=> 'Ketua Program Studi',
        ],
        'status_ajuan_seminar_admin'  => [
            1   => 'Diajukan',
            2   => 'Diserahkan',
            4   => 'Ditolak',
            5   => 'Ditangguhkan'
        ],
        'status_ajuan_seminar_kaprodi'  => [
            2   => 'Diserahkan',
            3   => 'Disetujui',
            4   => 'Ditolak',
            5   => 'Ditangguhkan'
        ],
        'status_ajuan_daftar_seminar'  => [
            0   => 'Draft',
            1   => 'Diajukan',
            2   => 'Diserahkan',
            3   => 'Disetujui',
            4   => 'Ditolak',
            5   => 'Ditangguhkan'
        ],
    ]
];
