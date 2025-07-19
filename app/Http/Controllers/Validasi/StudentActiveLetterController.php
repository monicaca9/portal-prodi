<?php

namespace App\Http\Controllers\Validasi;

use App\Http\Controllers\Controller;
use App\Models\SuratAktifKuliah\StudentActiveLetter;
use App\Models\SuratAktifKuliah\LetterNumber;
use App\Models\SuratAktifKuliah\LetterSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Exports\StudentActiveLetterExport;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Fpdi;

class StudentActiveLetterController extends Controller
{
    // Menampilkan daftar surat aktif kuliah sesuai peran pengguna yang sedang login
    public function index(Request $request)
    {
        // Cek dulu: "Kamu yang login itu perannya apa ya?
        // 46: Dosen PA 6: Admin 3000: Kaprodi 3001: Ketua Jurusan
        // Kalau sudah login, ambil id nya
        $userRole = session()->get('login.peran.id_peran');
        $userId = Auth::check()
            ? auth()->user()->id_sdm_pengguna
            : (request()->get('id_sdm_pengguna') ?? Str::uuid());

        // Ambil data surat aktif kuliah
        $query = StudentActiveLetter::query();

        if ($userRole == 46) {
            // Jangan tampilin surat yang masih tahap awal dibuat (belum diajukan mahasiswa).
            $query->where('status', '!=', 'dibuat')
            // Cuma tampilin surat dari mahasiswa yang dosen pembimbingnya adalah userId yg login sblmnya
                ->where('academic_advisor', $userId);
        } elseif ($userRole == 6) {
            // cari data utama (surat) yang punya tanda tangan dosen PA
            $query->whereHas('advisorSignature', function ($q) {
                // $q = variabel untuk nyaring data surat, dan tanda tangannya itu sudah disetujui
                $q->where('status', 'disetujui');
            });
        } elseif ($userRole == 3000) {
            $query->whereHas('adminValidation', function ($q) {
                $q->where('status', 'disetujui');
            });
        } elseif ($userRole == 3001) {
            $query->whereHas('headOfProgramSignature', function ($q) {
                $q->where('status', 'disetujui');
            });
        } else {
            // Ambil semua surat yang status-nya bukan ‘dibuat’
            $query->where('status', '!=', 'dibuat');
        }

        // Ambil semua data surat aktif kuliah dari database, yang paling baru ditampilkan duluan
        // dan simpan ke variabel bernama $studentActiveLetters
        $studentActiveLetters = $query->latest()->get();

        // Ambil surat satu per satu dari daftar $studentActiveLetters dan masing2 surat simpan di $letter
        foreach ($studentActiveLetters as $letter) {
            // Mengecek apakah tombol validasi surat harus dimatikan atau tidak, tergantung siapa yang login 
            $disable = match ($userRole) {
                // Kalau kamu adalah...	Maka sistem akan cek...
                // Simpan hasil pengecekan ini ke variabel $disable
                // Kalau hasilnya ada (misal nilainya 123) → artinya validasi udah dilakukan, jadi tombol harus dimatikan (TRUE).
                // Kalau hasilnya null → artinya belum divalidasi, tombol boleh dipencet (FALSE/NULL).
                6     => $letter->admin_validation_id,
                46 => $letter->advisor_signature_id,
                3000  => $letter->head_of_program_signature_id,
                3001  => $letter->head_of_department_signature_id,
                default => false,
            };
            // Tambahkan properti disable_validation_button ke surat ini, lalu isi pakai $disable
            $letter->disable_validation_button = $disable;
        }

        // kirim variabel $studentActiveLetters ke halaman view agar bisa ditampilkan
        return view('validasi.surat_aktif_kuliah.index', compact('studentActiveLetters'));
    }

    // Data yang dibawa dari halaman bisa berupa tanggal awal, tanggal akhir, atau status surat
    // berdasarkan request dari user
    public function history(Request $request)
    {
        // Ambil data surat dari database, kecuali yang masih status 'dibuat' (alias belum diajukan oleh mahasiswa)
        $query = StudentActiveLetter::where('status', '!=', 'dibuat');

        if ($request->filled('created_start')) {
            $query->whereDate('created_at', '>=', $request->created_start);
        }

        if ($request->filled('created_end')) {
            $query->whereDate('created_at', '<=', $request->created_end);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Setelah semua filter di atas selesai, ambil datanya dari database
        $studentActiveLetters = $query->latest()->get();

        return view('validasi.surat_aktif_kuliah.history', compact('studentActiveLetters'));
    }

    // Data yang dibawa dari halaman bisa berupa tanggal awal, tanggal akhir, atau status surat
    // berdasarkan request dari user
    public function exportExcel(Request $request)
    {
        // Ambil data surat dari database, kecuali yang masih status 'dibuat' (alias belum diajukan oleh mahasiswa)
        $query = StudentActiveLetter::where('status', '!=', 'dibuat');

        if ($request->filled('created_start')) {
            $query->whereDate('created_at', '>=', $request->created_start);
        }

        if ($request->filled('created_end')) {
            $query->whereDate('created_at', '<=', $request->created_end);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Setelah semua filter di atas selesai, ambil datanya dari database
        $studentActiveLetters = $query->latest()->get();

        return Excel::download(new StudentActiveLetterExport($studentActiveLetters), 'riwayat_surat_aktif_kuliah.xlsx');
    }


    // Menampilkan halaman validasi surat aktif kuliah berdasarkan surat yang dipilih
    public function edit($id)
    {
        // Buka kunci ID yang tadinya dikirim dalam bentuk acak (encrypted), jadi bisa dipakai untuk cari data di database
        $decryptedId = Crypt::decrypt($id);
        // Ambil data surat berdasarkan ID yang sudah didekripsi, dan ikut ambil relasi letterNumber-nya (nomor surat)
        $data = StudentActiveLetter::with('letterNumber')->where('id', $decryptedId)->first();

        $defaultCode = 'UN26.15.07/KM';
        // tahun sekarang
        $defaultYear = date('Y');

        // Ambil code dan year dari surat jika sudah ada, kalau belum, pakai nilai default sebelumnya
        $code = $data->letterNumber->code ?? $defaultCode;
        $year = $data->letterNumber->year ?? $defaultYear;

        // cari nomor surat terakhir di database untuk tahun dan kode tertentu,
        // terus tambah 1. misal 57 → nextNumber = 58
        // Kalau sama sekali gak ada, mulai dari 1
        $nextNumber = $data->letterNumber->number ?? LetterNumber::where('year', $year)
            ->where('code', $code)
            ->orderBy('number', 'desc')
            ->value('number') + 1 ?? 1;

        // Ambil riwayat semua surat milik mahasiswa ini (berdasarkan NIM), dari yang terbaru ke paling lama
        $studentActiveLetters = StudentActiveLetter::where('student_number', $data->student_number)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('validasi.surat_aktif_kuliah.edit', compact('data', 'code', 'year', 'nextNumber', 'studentActiveLetters'));
    }


    public function update(Request $request, $id)
    {
        // Dapet ID suratnya ($id) → di-unlock dulu (di-decrypt) biar bisa dicari di database.
        $decryptedId = Crypt::decrypt($id);
        // Ambil peran/role orang yang lagi login.
        $role = session()->get('login.peran');
        $userRole = $role['id_peran'] ?? null;

        // Daftar role yang boleh mem‑validasi
        $validRoles = [6, 46, 3000, 3001];
        // Jika role di luar daftar → langsung balas JSON ‘berhasil’ (early‑return), tanpa mengubah apa‑apa
        if (!in_array($userRole, $validRoles)) {
            return response()->json([
                'message' => 'Data berhasil diperbarui.',
                'user_role' => $userRole,
            ]);
        }

        $rules = [
            // status wajib dan hanya boleh disetujui/ditolak
            'status' => 'required|in:disetujui,ditolak',
            // notes wajib jika status = ditolak
            'notes' => 'required_if:status,ditolak',
        ];
        $request->validate($rules);

        // Cari data surat di tabel surat_aktif_kuliah dari model StudentActiveLetter 
        $letter = StudentActiveLetter::with([
            'adminValidation',
            'advisorSignature',
            'headOfProgramSignature',
            'headOfDepartmentSignature',
            'letterNumber',
        // Ambil surat dengan ID = $decryptedId. Kalau tidak ketemu, lempar error 404
        ])->findOrFail($decryptedId);

        // Pilih “tanda‑tangan saya” berdasarkan role
        $signature = match ($userRole) {
            // “Kalau saya Admin, pakai kolom adminValidation”
            // “Kalau saya Dosen PA, pakai kolom advisorSignature.”, dst...
            // Kalau peran tidak cocok, $signature = null (berarti belum ada tanda‑tangan untuk role itu).
            6 => $letter->adminValidation,
            46 => $letter->advisorSignature,
            3000 => $letter->headOfProgramSignature,
            3001 => $letter->headOfDepartmentSignature,
            default => null,
        };

        // Jika belum ada tanda tangan dari orang yang login sekarang, buat tanda tangan baru
        if (!$signature) {
            $signature = new LetterSignature();
            $signature->id = Str::uuid();
            // Kasih tau: ini ttd buat surat yang mana (submission_id)
            $signature->submission_id = $letter->id;
            // Kasih tau: ini ttd dari siapa (role Admin? Dosen? Kaprodi?)
            $signature->role = $userRole;
        }

        // Isi kolom status dari tanda tangan ini pakai nilai yang dipilih user di form.”
        // Contohnya: kalau ✅ Disetujui → status-nya jadi 'disetujui', kalau ❌ Ditolak → status-nya jadi 'ditolak'
        $signature->status = $request->status;
        // Isi kolom notes pakai teks alasan yang ditulis user, kalau surat ditolak
        $signature->notes = $request->notes;

        // Kalau status-nya disetujui, tambahin shortcode (buat QR Code)
        if ($request->status === 'disetujui') {
            $signature->short_code = Str::random(10);
        }

        // Udah diisi semua nih. Sekarang simpan ke database
        $signature->save();

    // Kalau yang login adalah Admin (role = 6), dan dia barusan menyetujui surat, dan surat itu belum punya nomor...
    // 🎯 Maka: saatnya kasih nomor surat!
    if ($userRole == 6 && $request->status === 'disetujui' && !$letter->letter_number) {
        // buat penomoran surat berdasarkan tahun sekarang
        $currentYear = date('Y');
        // Ambil kode surat dari letter_number kolom code
        $code = $request->letter_number['code'];
        // Cek: apakah user ngisi nomor surat manual? Kalau ya, simpan ke $manualNumber. Kalau nggak, isinya null
        $manualNumber = $request->letter_number['number'] ?? null;

    // 🔎 Validasi: Pastikan nomor surat tidak duplikat jika user isi manual
    if ($manualNumber) {
        // cek ke database: ada gak nomor surat yang tahun & kode & angkanya sama kayak yg diinput user?
        $exists = LetterNumber::where('year', $currentYear)
            ->where('code', $code)
            ->where('number', $manualNumber)
            ->exists();

        // Kalau nomor itu udah dipakai, jangan lanjut. Balikin ke halaman edit, dan kasih pesan error:
        if ($exists) {
            return redirect()->route('validasi.surat_aktif_kuliah.edit', ['id' => $id])
                ->with('error', 'Nomor surat sudah digunakan.');
        }
    }

    // 🔢 Generate data nomor surat (pakai manual jika ada)
    $data = LetterNumber::generateNextLetterNumber($currentYear, $code, $manualNumber);

    // 🔗 Hubungkan ke surat
    $data['letter_id'] = $letter->id;

    // 💾 Simpan ke database
    LetterNumber::create($data);

    // 🔁 Update surat dengan ID letter_number (kasih tau ke surat: “eh surat, ini ID nomor surat kamu ya!)
    $letter->letter_number = $data['id'];
    $letter->save();
}

        // Kalau surat di validasi Admin, maka simpan ID validasi Admin ke surat-nya
        if ($userRole == 6 && !$letter->admin_validation_id) {
            $letter->admin_validation_id = $signature->id;
            $letter->save();
        }

        if ($userRole == 46 && !$letter->advisor_signature_id) {
            $letter->advisor_signature_id = $signature->id;
            $letter->save();
        }

        if ($userRole == 3000 && !$letter->head_of_program_signature_id) {
            $letter->head_of_program_signature_id = $signature->id;
            $letter->save();
        }

        if ($userRole == 3001 && !$letter->head_of_department_signature_id) {
            $letter->head_of_department_signature_id = $signature->id;
            $letter->save();
        }

        // Setelah semua tanda tangan dicek → update status suratnya (misal: udah disetujui semuanya apa belum)
        $letter->updateStatusBasedOnSignatures();

        return redirect()->route('validasi.surat_aktif_kuliah', ['id' => $id])
            ->with('success', 'Data berhasil divalidasi.');
    }


    public function previewPDF($id)
    {
        // Ambil data surat berdasarkan $id, tapi dekripsi dulu id-nya (karena sebelumnya di-enkripsi saat dikirim lewat URL).
        $data = $this->getLetterByDecryptedId($id);
        // Tambahkan info dosen pembimbing akademik (DPA) ke data suratnya
        $this->attachAcademicAdvisorInfo($data);

        // 1. Generate surat aktif kuliah PDF (sementara simpan ke file)
        $generatedPDF = $this->generatePDF($data);
        // Simpan PDF yang tadi dibuat sementara di path storage/app/temp_surat.pdf
        // Namanya sementara karena nanti mau digabung sama file lain.
        $tempPath = storage_path('app/temp_surat.pdf');
        $generatedPDF->save($tempPath);

        // Ambil path file dokumen pendukung yang disimpan di kolom supporting_document
        $supportingFile = $data->supporting_document;
        $relativePath = str_replace('public/', '', $supportingFile);
        $supportingPath = storage_path('app/public/' . $relativePath);

        // Cek apakah file pendukungnya benar-benar ada
        if (!file_exists($supportingPath)) {
            return response()->json(['message' => 'File dokumen pendukung tidak ditemukan.'], 404);
        }

        // 3. Gabungkan
        // Tentukan lokasi akhir hasil gabungan PDF (surat + pendukung).
        // Panggil fungsi mergePDFs() untuk menggabungkan keduanya
        $mergedPath = storage_path('app/merged_surat.pdf');
        $this->mergePDFs([$tempPath, $supportingPath], $mergedPath);

        // Tampilkan isi file merged_surat.pdf ke browser, langsung dalam mode preview (tidak download otomatis).
        // inline; → tampilkan di tab browser
        // filename="..." → nama file saat disimpan kalau di-download
        return response()->stream(function () use ($mergedPath) {
            echo file_get_contents($mergedPath);
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="surat_aktif_kuliah.pdf"',
        ]);
    }

    public function downloadPDF($id)
    {
        $data = $this->getLetterByDecryptedId($id);
        $this->attachAcademicAdvisorInfo($data);
        return $this->generatePDF($data)->download('surat_aktif_kuliah.pdf');
    }

    private function getLetterByDecryptedId($id)
    {
        $decryptedId = Crypt::decrypt($id);
        return StudentActiveLetter::with([
            'letterNumber',
            'adminValidation',
            'advisorSignature',
            'headOfProgramSignature',
            'headOfDepartmentSignature'
        ])->findOrFail($decryptedId);
    }

    private function attachAcademicAdvisorInfo(&$data)
    {
        if (strlen($data->academic_advisor) === 36) {
            $sdm = DB::table('pdrd.sdm')
                ->where('id_sdm', $data->academic_advisor)
                ->select('nm_sdm', 'nip')
                ->first();

            $data->academic_advisor_name = $sdm->nm_sdm ?? '';
            $data->academic_advisor_nip = $sdm->nip ?? null;
        } else {
            $data->academic_advisor_name = null;
            $data->academic_advisor_nip = null;
        }
    }

    private function generatePDF($data)
    {
        $pathImage = 'file://' . public_path('images/kop_fakultas.png');
        $pathImageLogo = 'file://' . public_path('images/logo-unila.png');

        $advisorQrCode = null;
        if (!empty($data->advisorSignature->short_code)) {
            $url = route('sak.preview', ['code' => $data->advisorSignature->short_code]);
            $qrImage = QrCode::format('png')->size(100)->generate($url);
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

        return Pdf::loadView('administrasi.surat_aktif_kuliah.combined_template', [
            'data' => $data,
            'pathImage' => $pathImage,
            'pathImageLogo' => $pathImageLogo,
            'advisorQrCode' => $advisorQrCode,
            'headOfProgramQrCode' => $headOfProgramQrCode,
            'headOfDepartementQrCode' => $headOfDepartementQrCode,

        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'chroot' => [public_path()],
            ])
            ->setWarnings(false);
    }

        private function mergePDFs(array $pdfPaths, $outputPath = null)
    {
        $pdf = new Fpdi();

        foreach ($pdfPaths as $file) {
            $pageCount = $pdf->setSourceFile($file);
            for ($page = 1; $page <= $pageCount; $page++) {
                $tpl = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($tpl);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
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