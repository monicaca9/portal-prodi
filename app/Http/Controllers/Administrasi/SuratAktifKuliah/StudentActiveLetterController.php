<?php

namespace App\Http\Controllers\Administrasi\SuratAktifKuliah;

use App\Http\Controllers\Controller;
use App\Models\SuratAktifKuliah\SuratAktif;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Carbon\Carbon;

class StudentActiveLetterController extends Controller
{
    // Menampilkan daftar surat aktif kuliah milik user (mahasiswa yang login)
    // $pesertaDidik = objek dari model PesertaDidik (namanya bebas dibuat sendiri ga harus $pesertaDidik yg penting tipe parameternya PesertaDidik)
    public function index(PesertaDidik $pesertaDidik)
    {
        $studentActiveLetters = SuratAktif::where('id_creator', auth()->user()->id_pd_pengguna)
            // urutkan data dari yang terbaru
            ->orderBy('id', 'desc')
            // ambil semua data yang cocok
            ->get();
        // Kalau pakai SQL:
        // SELECT * FROM student_active_letters
        // WHERE id_creator = 12345
        // ORDER BY tgl_create DESC; NOTES: DESC = latest()

        return view('administrasi.surat_aktif_kuliah.index', compact('studentActiveLetters'));
    }


    // Request itu class bawaan Laravel yang isinya semua data permintaan (request) dari user (form, upload, dll)
    public function history(Request $request, PesertaDidik $pesertaDidik)
    // Request $request → untuk mengambil data filter dari form pencarian (misalnya created_start, created_end, status)
    {
        $query = SuratAktif::where('id_creator', auth()->user()->id_pd_pengguna);

        if ($request->filled('created_start')) {
            $query->whereDate('tgl_create', '>=', $request->created_start);
        }

        if ($request->filled('created_end')) {
            $query->whereDate('tgl_create', '<=', $request->created_end);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Setelah semua filter diterapkan, ambil data yang sudah difilter tadi, dan urutkan dari yang terbaru
        $studentActiveLetters = $query->orderBy('id', 'desc')->get();

        return view('administrasi.surat_aktif_kuliah.history', compact('studentActiveLetters'));
    }


    public function create(PesertaDidik $pesertaDidik)
    {
        // Ambil data detail mahasiswa yang sedang login, carinya lewat model PesertaDidik pakai method detailPD
        $profile = $pesertaDidik->detailPD(auth()->user()->detailPD);
        
        $jurusan = DB::table('pdrd.sms')
        ->where('id_sms', 'c4b67b31-fd42-4670-bcf0-541ff1c20ff7')
        ->value('nm_lemb');
        // Ambil nama jurusan dari tabel `pdrd.sms` berdasarkan `id_sms` tertentu (hardcoded)

        $academicYear = $this->getCurrentAcademicYear();

        // Ambil tanggal masuk kuliah dari $profile->tgl_masuk.
        // Kalau gaada / kosong/null, pakai tanggal hari ini (now()).
        // Kirim tanggal itu ke method calculateCurrentSemester().
        $semester = $this->calculateCurrentSemester($profile->tgl_masuk ?? now());

        // new StudentActiveLetter() = bikin object baru dari model surat.
        $data = new SuratAktif();
        $data->fill([
            'id'                => Str::uuid(),
            'nama'              => $profile->nm_pd,
            'npm'                => $profile->nim,
            'jurusan'             => $jurusan,       
            'prodi'              => $profile->prodi,
            'semester'          => $semester,
            'thn_akademik'     => $academicYear,
            'no_hp'             => $profile->tlpn_hp,
            'alamat'           => $profile->jln,
            'tujuan'           => '',
            'validasi'         => '',
            'dosen_pa'          => '',
        ]);

        // Ambil ID mahasiswa dari $profile->id_pd.
        // Kalau kosong, isinya null.
        // Kirim ke method getAcademicAdvisors() → hasilnya daftar dosen pembimbing (PA) dari prodi mahasiswa.
        $studentId = $profile->id_pd ?? null;
        $academicAdvisors = $this->getAcademicAdvisors($studentId);

        // compact: mengirim data ke view
        return view('administrasi.surat_aktif_kuliah.create', compact('profile', 'data', 'academicAdvisors', 'academicYear'));
    }

    // Menerima data form dari user
    public function store(Request $request)
    {
        // Validasi isi form, untuk memastikan semua input tidak kosong dan sesuai
        $request->validate([
            'nama'                 => 'required|string|max:100',
            'npm'                => 'required|max:10',
            'jurusan'           => 'required|string|max:100',
            'prodi'              => 'required|string|max:100',
            'semester'             => 'required|max:20',
            'thn_akademik'        => 'required|max:20',
            'no_hp'             => 'required|max:15',
            'alamat'              => 'required|string',
            'tujuan'              => 'required|string|max:255',
            'validasi'            => 'required|string',
            'dosen_pa'          => 'required|string|max:100',
            'dokumen'           => 'required|file|mimes:pdf|max:2048',
        ]);

        // untuk menyimpan lokasi file signature dan dokumen pendukung
        $signaturePath = null;
        $supportingDocumentPath = null;

    // Mengecek apakah isi signature adalah gambar dalam bentuk base64 string
    // contoh base64: data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...
    if ($request->validasi && Str::startsWith($request->validasi, 'data:image')) {
        // Simpan isi validasi ke variabel imageData
        $imageData = $request->validasi;
        // Pisahkan string menjadi 2 bagian, berdasarkan tanda ;
        // base64: data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...
        // $type = "data:image/png"
        // $imageData = "base64,iVBORw0KGgoAAAANSUhEUg..."
        list($type, $imageData) = explode(';', $imageData);
        // Pisahkan lagi isi $imageData dari tanda koma ,
        // $imageData = "iVBORw0KGgoAAAANSUhEUg..."  // hanya isi base64-nya
        list(, $imageData) = explode(',', $imageData);
        // Konversi teks base64 jadi gambar asli
        $imageData = base64_decode($imageData);

        // Bikin nama file unik
        $filename = time() . '_' . uniqid() . '.png';
        // Buat path lengkap tempat file akan disimpan
        $path = storage_path('app/public/signatures/' . $filename);
        // Simpan file gambar (ke path yang tadi dibuat)
        file_put_contents($path, $imageData);

        // Simpan relatif path dari file untuk nanti dimasukkan ke database
        // ex: public/signatures/1720001113_64a8f2beef15c.png
        $signaturePath = 'public/signatures/' . $filename;
    }

    // Simpan supporting document
    // Mengecek apakah user mengirimkan file di form pada field supporting_document
    if ($request->hasFile('dokumen')) {
        // Ambil objek file-nya dan simpan ke variabel $file
        $file = $request->file('dokumen');
        // Buat nama file baru agar tidak bentrok dengan file lain
        // getClientOriginalExtension() → ambil ekstensi file (misalnya: pdf)
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        //  Simpan file ke folder public/supporting_documents
        $supportingDocumentPath = $file->storeAs('public/supporting_documents', $filename);
    }

    // Buat objek model baru dari StudentActiveLetter
    $data = new SuratAktif();
    // Buat objek model baru dari StudentActiveLetter
    $data->fill(array_merge(
        // Ambil semua data dari form, kecuali:
        // signature → sudah diproses sebelumnya
        // supporting_document → sudah diproses jadi file
        $request->except(['validasi', 'dokumen']),
        [
            // Ditambah data custom:
            'id' => Str::uuid(),
            'validasi' => $signaturePath,
            'dokumen' => $supportingDocumentPath,
        ]
    ));
    $data->save();

        return redirect()->route('administrasi.surat_aktif_kuliah')->with('success', 'Data berhasil disimpan!');
    }


    public function submit($id)
    {
        // Ambil data surat dari database berdasarkan id yang sudah didekripsi
        $data = $this->getLetterByDecryptedId($id);
        // Ubah status surat jadi "menunggu" (diajukan)
        $data->status = 'menunggu';
        $data->save();

        // Crypt::encrypt(...) dipakai agar id disembunyikan (tidak terlihat asli di URL)
        return redirect()->route('administrasi.surat_aktif_kuliah', ['id' => Crypt::encrypt($data->id)])
            ->with('success', 'Surat berhasil diajukan dan sedang menunggu proses.');
    }

    public function preview($id)
    {
        // Ambil surat berdasarkan ID yang sudah didekripsi
        $data = $this->getLetterByDecryptedId($id);
        // Tambahkan informasi dosen pembimbing akademik ke dalam $data
        $this->attachAcademicAdvisorInfo($data);

        return view('administrasi.surat_aktif_kuliah.preview', compact('data'));
    }

    public function edit($id, PesertaDidik $pesertaDidik)
    {
        // Ambil profil lengkap mahasiswa yang login
        $profile = $pesertaDidik->detailPD(auth()->user()->detailPD);
        // Ambil data surat yang ingin diedit berdasarkan id
        $data = $this->getLetterByDecryptedId($id);
        // Ambil ID mahasiswa dari profil
        $studentId = $profile->id_pd ?? null;
        // Ambil daftar dosen PA dari prodi mahasiswa
        $academicAdvisors = $this->getAcademicAdvisors($studentId);
        // Ambil tahun akademik aktif saat ini
        $currentAcademicYear = $this->getCurrentAcademicYear();

        return view('administrasi.surat_aktif_kuliah.edit', compact('data', 'academicAdvisors', 'currentAcademicYear'));
    }

    public function update(Request $request, $id)
{
    // ambil data surat dari database berdasarkan ID yang terenkripsi di URL
    $data = $this->getLetterByDecryptedId($id);

    // Validasi semua data input, pastikan semua wajib diisi dan formatnya benar
    $request->validate([
        'nama'                 => 'required|string|max:100',
        'npm'       => 'required|max:10',
        'jurusan'           => 'required|string|max:100',
        'prodi'        => 'required|string|max:100',
        'semester'             => 'required|max:20',
        'thn_akademik'        => 'required|max:20',
        'no_hp'         => 'required|max:15',
        'alamat'              => 'required|string',
        'tujuan'              => 'required|string|max:255',
        'validasi'            => 'required|string',  
        'dosen_pa'     => 'required|string|max:100',
        'dokumen'  => 'sometimes|file|mimes:pdf|max:2048',  
    ]);

    // Simpan path file lama dulu
    // nanti dipakai untuk:
    // Menghapus file lama (kalau ada update)
    // Atau menyimpan ulang (kalau tidak berubah)
    $signaturePath = $data->validasi; 
    $supportingDocumentPath = $data->dokumen;

    // Jika signature baru dikirim (base64)
    if ($request->validasi && Str::startsWith($request->validasi, 'data:image')) {
        // Hapus file tanda tangan lama jika ada (optional),  supaya tidak numpuk di server
        if ($signaturePath && \Storage::exists($signaturePath)) {
            \Storage::delete($signaturePath);
        }

        // Proses simpan tanda tangan baru dari base64
        $imageData = $request->validasi;
        list($type, $imageData) = explode(';', $imageData);
        list(, $imageData) = explode(',', $imageData);
        $imageData = base64_decode($imageData);

        $filename = time() . '_' . uniqid() . '.png';
        $path = storage_path('app/public/signatures/' . $filename);
        file_put_contents($path, $imageData);

        $signaturePath = 'public/signatures/' . $filename;
    }

    // Update dokumen pendukung jika ada upload baru
    if ($request->hasFile('dokumen')) {
        // Hapus file lama jika ada (optional)
        if ($supportingDocumentPath && \Storage::exists($supportingDocumentPath)) {
            \Storage::delete($supportingDocumentPath);
        }

        $file = $request->file('dokumen');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $supportingDocumentPath = $file->storeAs('public/supporting_documents', $filename);
    }

    $data->fill(array_merge(
        $request->except(['validasi', 'dokumen']),
        [
            'validasi' => $signaturePath,
            'dokumen' => $supportingDocumentPath,
        ]
    ));
    $data->save();

    return redirect()->route('administrasi.surat_aktif_kuliah', ['id' => $id])->with('success', 'Data berhasil diperbarui.');
}

        public function previewPDF($id)
    {
        // Ambil data surat
        $data = $this->getLetterByDecryptedId($id);
        // Tambahkan info nama & NIP dosen PA
        $this->attachAcademicAdvisorInfo($data);

        // Mengambil template surat & Mengisi datanya
        $generatedPDF = $this->generatePDF($data);
        // Simpan file PDF tadi ke file sementara
        $tempPath = storage_path('app/temp_surat.pdf');
        $generatedPDF->save($tempPath);

        $supportingFile = $data->dokumen;
        // Ubah path supaya bisa diakses dari storage_path()
        // Laravel simpan file di: storage/app/public/supporting_documents/krs.pdf
        // Tapi path di DB cuma public/... → makanya diubah biar cocok sama folder storage.
        $relativePath = str_replace('public/', '', $supportingFile);
        $supportingPath = storage_path('app/public/' . $relativePath);


        if (!file_exists($supportingPath)) {
            return response()->json(['message' => 'File dokumen pendukung tidak ditemukan.'], 404);
        }

        // 3. Gabungkan
        $mergedPath = storage_path('app/merged_surat.pdf');
        $this->mergePDFs([$tempPath, $supportingPath], $mergedPath);

        // file_get_contents($mergedPath) = ambil isi file gabungan
        // response()->stream(...) = kirim isi file ke browser
        // 'inline' = PDF langsung dibuka di tab browser, bukan di-download
        // filename=... = nama file saat dibuka: surat_aktif_kuliah.pdf
        return response()->stream(function () use ($mergedPath) {
            echo file_get_contents($mergedPath);
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="surat_aktif_kuliah.pdf"',
        ]);
    }

    // Mengambil data surat aktif kuliah dari database berdasarkan ID terenkripsi 
    // 	Supaya URL-nya aman & nggak bisa ditebak
    private function getLetterByDecryptedId($id)
    {
        // ID surat yang masuk dari URL biasanya dalam bentuk terenkripsi (pakai Crypt::encrypt() sebelumnya). Baris ini mendekripsinya supaya bisa digunakan.
        // buka gembok dari ID yang tadi disembunyikan/enkripsi.
        // ex: $id = "eyJpdiI6Ik9CME95Y3ZCVE5r..." // enkripsi
        // $decryptedId = "fb65e7a2-a0b1-4d2b-9f77-d7f14eaa6543" // hasil decrypt
        $decryptedId = Crypt::decrypt($id);
        // Ambil data surat beserta data terkait lainnya
        return SuratAktif::with([
            'letterNumber',
            'adminValidation',
            'advisorSignature',
            'headOfProgramSignature',
            'headOfDepartmentSignature'
        // Cari surat di tabel student_active_letters berdasarkan id = $decryptedId
        // Kalau tidak ditemukan → langsung 404 Not Found
        ])->findOrFail($decryptedId);
    }


    // Menambahkan nama & NIP dosen pembimbing akademik (PA) ke dalam objek $data, agar bisa ditampilkan
    private function attachAcademicAdvisorInfo(&$data)
    {
        // Cek apakah isian academic_advisor adalah UUID. UUID biasanya panjangnya 36 karakter
        if (strlen($data->dosen_pa) === 36) {
            // Akses tabel sdm dari database pdrd untuk mencari informasi dosen berdasarkan ID.
            $sdm = DB::table('pdrd.sdm')
                ->where('id_sdm', $data->dosen_pa)
                // Ambil hanya nama (nm_sdm) dan NIP dosen dari database
                ->select('nm_sdm', 'nip')
                ->first();

            // Masukkan informasi dosen ke dalam objek $data
            $data->dosen_pa_nama = $sdm->nm_sdm ?? '';
            $data->dosen_pa_nip = $sdm->nip ?? null;
        } else {
            // Kalau ternyata nilai dosen_pa bukan UUID (panjang ≠ 36), artinya bukan dosen valid. Maka isi nama dan NIP jadi null
            $data->dosen_pa_nama = null;
            $data->dosen_pa_nip = null;
        }
    }

    // mengambil daftar dosen pembimbing akademik (PA) berdasarkan prodi (program studi) dari mahasiswa yang sedang login
        private function getAcademicAdvisors($studentId)
    {
        // Ambil prodi mahasiswa dari reg_pd
        $prodiMahasiswa = DB::table('pdrd.reg_pd')
            ->where('id_pd', $studentId)
            ->value('id_sms');  // ambil id_sms prodi mahasiswa

        // QUERY SQL MANUAL
        // SELECT id_sms
        // FROM pdrd.reg_pd
        // WHERE id_pd = 'ID_MAHASISWA';

        // Ambil dosen dari prodi yang sama 
        $dosenPA = DB::table('pdrd.sdm as s')
        // Gabung (JOIN) dengan tabel reg_ptk → untuk tahu dosen ini ngajar di prodi mana
        // Syarat JOIN-nya: s.id_sdm = r.id_sdm -> Artinya: hubungkan data dosen (ID dosen) ke data relasinya dengan prodi.
        ->join('pdrd.reg_ptk as r', 's.id_sdm', '=', 'r.id_sdm')
        // Gabung lagi dengan tabel keaktifan_ptk untuk tahu status aktifnya dosen itu.
        // Syaratnya: id_reg_ptk dari tabel reg_ptk cocok dengan id_reg_ptk dari tabel keaktifan_ptk
        ->join('pdrd.keaktifan_ptk as k', 'r.id_reg_ptk', '=', 'k.id_reg_ptk')
        // Filter: hanya ambil dosen dari prodi yang sama dengan mahasiswa
        ->where('r.id_sms', $prodiMahasiswa)  
        // pluck(kolom_nilai, kolom_kunci) → Menghasilkan array key-value.
        // s.nm_sdm = nama dosen
        // s.id_sdm = ID dosen 
        ->pluck('s.nm_sdm', 's.id_sdm');

        // QUERY SQL MANUAL
        // SELECT s.id_sdm, s.nm_sdm
        // FROM pdrd.sdm s
        // JOIN pdrd.reg_ptk r ON s.id_sdm = r.id_sdm
        // JOIN pdrd.keaktifan_ptk k ON r.id_reg_ptk = k.id_reg_ptk
        // WHERE r.id_sms = 'prodi-456'

        return $dosenPA;
    }

        private function getCurrentAcademicYear()
    {
        // Carbon = library tanggal di Laravel (seperti jam, hari, bulan).
        // Carbon::today() berarti ambil tanggal hari ini, tanpa jam.
        $today = Carbon::today();

        // Langkah 1: Cari semester aktif
        // Ambil data dari tabel ref.semester
        $year = DB::table('ref.semester')
        // Filter semester yang sudah dimulai, yaitu tgl_mulai kurang dari atau sama dengan tanggal hari ini
        // misal: '2025-02-01' <= '2025-06-24' ✅ cocok
            ->whereDate('tgl_mulai', '<=', $today)
        // tgl_selesai lebih dari atau sama dengan hari ini
            ->whereDate('tgl_selesai', '>=', $today)
        // Urutkan dari semester paling terbaru (ID semester paling besar)
            ->orderByDesc('id_smt')
        // Ambil satu data saja (baris pertama hasil filter)
            ->first();

        // SQL MANUAL
        // SELECT *
        // FROM ref.semester
        // WHERE tgl_mulai <= '2025-06-24'
        //   AND tgl_selesai >= '2025-06-24'
        // ORDER BY id_smt DESC
        // LIMIT 1;

        if (!$year) {
            return 'Tahun Akademik Tidak Ditemukan';
        }

        // Langkah 2: Ambil nama tahun akademik
        // Ambil nama tahun akademik dari tabel tahun_ajaran
        $academicYear = DB::table('ref.tahun_ajaran')
        // Ambil baris tahun ajaran berdasarkan ID dari semester
            ->where('id_thn_ajaran', $year->id_thn_ajaran)
        // Ambil hanya kolom nm_thn_ajaran
            ->value('nm_thn_ajaran');

        // SQL MANUAL
        // SELECT nm_thn_ajaran
        // FROM ref.tahun_ajaran
        // WHERE id_thn_ajaran = '2024';

        return $academicYear ?: 'Tahun Akademik Tidak Ditemukan';
    }


    // Untuk menghitung sekarang mahasiswa ada di semester berapa,
    // berdasarkan: Tanggal masuk kuliah (tgl_masuk), Tanggal sekarang, dan Anggapan: 1 semester = 6 bulan
    private function calculateCurrentSemester($entryDate)
    {
        // Konversi tgl masuk ke Carbon biar bisa dihitung perbedaan waktunya
        $entry = Carbon::parse($entryDate);
        // Ambil waktu sekarang, termasuk jam
        $now = Carbon::now();

        // Hitung berapa bulan selisih antara $entry (tanggal masuk) dan $now (sekarang)
        $diffInMonths = $entry->diffInMonths($now);

        // Bagi jumlah bulan dengan 6, lalu Ambil angka bulat ke bawah (floor), Lalu tambah 1 supaya semester dimulai dari angka 1, bukan 0
        // ex: floor(46 / 6) = 7
        //      7 + 1 = semester 8
        $semesterNumber = (int) floor($diffInMonths / 6) + 1;

        return $semesterNumber;
    }


    // Membuat file PDF surat aktif kuliah berdasarkan data surat dan tanda tangan dalam bentuk QR Code
    private function generatePDF($data)
    {
        // Menyimpan path file kop surat dan logo universitas dalam format file:// supaya bisa dibaca oleh PDF generator (DomPDF)
        $pathImage = 'file://' . public_path('images/kop_fakultas.png');
        $pathImageLogo = 'file://' . public_path('images/logo-unila.png');

        $advisorQrCode = null;
        // Cek apakah dosen PA punya (short_code) untuk validasi QR
        if (!empty($data->advisorSignature->short_code)) {
            // Jika ada: Buat URL: route('sak.preview', ...)
            $url = route('sak.preview', ['code' => $data->advisorSignature->short_code]);
            // Buat QR code-nya dalam format PNG
            $qrImage = QrCode::format('png')->size(100)->generate($url);
            // Lalu encode ke base64 agar bisa disisipkan langsung ke HTML
            $advisorQrCode = 'data:image/png;base64,' . base64_encode($qrImage);
        }

        $headOfProgramQrCode = null;
        if (!empty($data->headOfProgramSignature->short_code)) {
            $url = route('sak.preview', ['code' => $data->headOfProgramSignature->short_code]);
            $qrImage = QrCode::format('png')->size(100)->generate($url);
            $headOfProgramQrCode = 'data:image/png;base64,' . base64_encode($qrImage);
        }

        $headOfDepartementQrCode = null;
        if (!empty($data->headOfDepartmentSignature->short_code)) {
            $url = route('sak.preview', ['code' => $data->headOfDepartmentSignature->short_code]);
            $qrImage = QrCode::format('png')->size(100)->generate($url);
            $headOfDepartementQrCode = 'data:image/png;base64,' . base64_encode($qrImage);
        }

        // Buat PDF dari Blade template view bernama combined_template
        // Semua data, logo, QR Code → dikirim ke file view Blade (combined_template)
        return Pdf::loadView('administrasi.surat_aktif_kuliah.combined_template', [
            'data' => $data,
            'pathImage' => $pathImage,
            'pathImageLogo' => $pathImageLogo,
            'advisorQrCode' => $advisorQrCode,
            'headOfProgramQrCode' => $headOfProgramQrCode,
            'headOfDepartementQrCode' => $headOfDepartementQrCode,

        ])
        // Ukuran kertas: A4, orientasi potret
        ->setPaper('a4', 'portrait')
        ->setOptions([
            // Aktifkan HTML5 parsing & PHP di dalam view
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            // batasi akses file hanya di direktori public
            'chroot' => [public_path()],
        ])
        // supaya error kecil tidak ditampilkan
        ->setWarnings(false);
    }

    // Gabungkan beberapa file PDF menjadi satu
        private function mergePDFs(array $pdfPaths, $outputPath = null)
    {
        // Inisialisasi objek FPDI (library untuk manipulasi PDF)
        $pdf = new Fpdi();

        // Loop semua file PDF yang ingin digabung
        foreach ($pdfPaths as $file) {
            // Hitung berapa jumlah halaman di file PDF yang sedang diproses
            $pageCount = $pdf->setSourceFile($file);
            // Loop setiap halaman dalam file tersebut
            // Contoh: Kalau file punya 2 halaman, maka akan ulangi page 1 dan page 2
            for ($page = 1; $page <= $pageCount; $page++) {
                // Import satu halaman dari file PDF ke memori → disimpan ke $tpl
                $tpl = $pdf->importPage($page);
                // Ambil ukuran halaman PDF itu
                // Ini penting supaya nanti saat ditempel ke PDF utama, ukurannya pas
                $size = $pdf->getTemplateSize($tpl);

                // Tambahkan halaman kosong baru ke PDF utama, sesuai ukuran halaman yang diimpor
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                // Tempel isi halaman yang sudah diimpor tadi ($tpl) ke halaman kosong yang baru dibuat
                $pdf->useTemplate($tpl);
            }
        }

        if ($outputPath) {
            $pdf->Output('F', $outputPath); // Simpan ke file
        } else {
            $pdf->Output('I', 'combined.pdf'); // Langsung tampilkan
        }
    }
}