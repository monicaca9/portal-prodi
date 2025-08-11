<?php

namespace App\Http\Controllers\Administrasi\SuratMasihKuliah;

use App\Http\Controllers\Controller;
use App\Models\SuratMasihKuliah\SuratMasih;
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

class StillStudyLetterController extends Controller
{
    public function index(PesertaDidik $pesertaDidik)
    {
        $stillStudyLetters = SuratMasih::where('id_creator', auth()->user()->id_pd_pengguna)
            ->orderBy('tgl_create', 'desc')
            ->get();
        return view('administrasi.surat_masih_kuliah.index', compact('stillStudyLetters'));
    }


    public function history(Request $request, PesertaDidik $pesertaDidik)
    {
        $query = SuratMasih::where('id_creator', auth()->user()->id_pd_pengguna);

        if ($request->filled('created_start')) {
            $query->whereDate('tgl_create', '>=', $request->created_start);
        }

        if ($request->filled('created_end')) {
            $query->whereDate('tgl_create', '<=', $request->created_end);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stillStudyLetters = $query->orderBy('tgl_create', 'desc')->get();

        return view('administrasi.surat_masih_kuliah.history', compact('stillStudyLetters'));
    }


    public function create(PesertaDidik $pesertaDidik)
    {
        $profile = $pesertaDidik->detailPD(auth()->user()->detailPD);

        $jurusan = DB::table('pdrd.sms')
        ->where('id_sms', 'c4b67b31-fd42-4670-bcf0-541ff1c20ff7')
        ->value('nm_lemb');

        $academicYear = $this->getCurrentAcademicYear();
        $semesterNumber = $this->calculateCurrentSemester($profile->tgl_masuk ?? now());

        $data = new SuratMasih();
        $data->fill([
            'id'                => Str::uuid(),
            'nama'              => $profile->nm_pd,
            'npm'               => $profile->nim,
            'jurusan'            => $jurusan,       
            'prodi'             => $profile->prodi,
            'semester'          => $semesterNumber,
            'thn_akademik'     => $academicYear,
            'no_hp'             => $profile->tlpn_hp,
            'alamat'           => $profile->jln,
            'tujuan'           => '',
            'nama_ortu'       => $profile->nm_ayah,
            'nip_ortu'        => '',
            'pangkat_ortu'      => '',
            'pekerjaan_ortu'     => $profile->nm_pekerjaan_ayah,
            'instansi_ortu'     => '',     
            'alamat_ortu'    => '',
            'validasi'         => '',
            'dosen_pa'         => '',
        ]);

        $studentId = $profile->id_pd ?? null;
        
        $academicAdvisors = $this->getAcademicAdvisors($studentId);

        return view('administrasi.surat_masih_kuliah.create', compact('profile', 'data', 'academicAdvisors', 'academicYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'                 => 'required|string|max:100',
            'npm'                  => 'required|max:10',
            'jurusan'               => 'required|string|max:100',
            'prodi'                  => 'required|string|max:100',
            'semester'             => 'required|max:20',
            'thn_akademik'        => 'required|max:20',
            'no_hp'               => 'required|max:15',
            'alamat'              => 'required|string',
            'tujuan'              => 'required|string|max:255',
            'nama_ortu'          => 'required|string|max:100',
            'nip_ortu'           => 'required|max:20',
            'pangkat_ortu'         => 'required|string|max:100',
            'pekerjaan_ortu'      => 'required|string|max:100',
            'instansi_ortu'     => 'required|string|max:100',
            'alamat_ortu'       => 'required|string',
            'validasi'            => 'required|string',
            'dosen_pa'           => 'required|string|max:100',
            'dokumen'            => 'required|file|mimes:pdf|max:2048',
            'dokumen2'           => 'required|file|mimes:pdf|max:2048',
        ]);

        $signaturePath = null;
        $supportingDocumentPath = null;

        // Simpan signature dari base64
    if ($request->validasi && Str::startsWith($request->validasi, 'data:image')) {
        $imageData = $request->validasi;
        list($type, $imageData) = explode(';', $imageData);
        list(, $imageData) = explode(',', $imageData);
        $imageData = base64_decode($imageData);

        $filename = time() . '_' . uniqid() . '.png';
        $path = storage_path('app/public/signatures/' . $filename);
        file_put_contents($path, $imageData);

        $signaturePath = 'public/signatures/' . $filename;
    }

    // Simpan dokumen pendukung 1
$supportingDocumentPath = null;
if ($request->hasFile('dokumen')) {
    $file = $request->file('dokumen');
    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    $supportingDocumentPath = $file->storeAs('public/supporting_documents', $filename);
}

// Simpan dokumen pendukung 2
$supportingDocument2Path = null;
if ($request->hasFile('dokumen2')) {
    $file2 = $request->file('dokumen2');
    $filename2 = time() . '_' . uniqid() . '.' . $file2->getClientOriginalExtension();
    $supportingDocument2Path = $file2->storeAs('public/supporting_documents2', $filename2);
}

// Simpan ke database
$data = new SuratMasih();
$data->fill(array_merge(
    $request->except(['validasi', 'dokumen', 'dokumen2']),
    [
        'id' => Str::uuid(),
        'validasi' => $signaturePath,
        'dokumen' => $supportingDocumentPath,
        'dokumen2' => $supportingDocument2Path,
    ]
));
$data->save();


        return redirect()->route('administrasi.surat_masih_kuliah')->with('success', 'Data berhasil disimpan!');
    }


    public function submit($id)
    {
        $data = $this->getLetterByDecryptedId($id);
        $data->status = 'menunggu';
        $data->save();

        return redirect()->route('administrasi.surat_masih_kuliah', ['id' => Crypt::encrypt($data->id)])
            ->with('success', 'Surat berhasil diajukan dan sedang menunggu proses.');
    }


    // public function detail($id)
    // {
    //     $data = $this->getLetterByDecryptedId($id);
    //     $this->attachAcademicAdvisorInfo($data);

    //     return view('administrasi.surat_masih_kuliah.detail', compact('data'));
    // }

    public function preview($id)
    {
        $data = $this->getLetterByDecryptedId($id);
        $this->attachAcademicAdvisorInfo($data);

        return view('administrasi.surat_masih_kuliah.preview', compact('data'));
    }

    public function edit($id, PesertaDidik $pesertaDidik)
    {
        $profile = $pesertaDidik->detailPD(auth()->user()->detailPD);
        $data = $this->getLetterByDecryptedId($id);
        $studentId = $profile->id_pd ?? null;
        $academicAdvisors = $this->getAcademicAdvisors($studentId);
        $semesters = []; 
        $currentAcademicYear = $this->getCurrentAcademicYear();

        return view('administrasi.surat_masih_kuliah.edit', compact('data', 'academicAdvisors','semesters', 'currentAcademicYear'));
    }

    public function update(Request $request, $id)
    {
        $data = $this->getLetterByDecryptedId($id);

        $request->validate([
            'nama'                 => 'required|string|max:100',
            'npm'                    => 'required|max:10',
            'jurusan'              => 'required|string|max:100',
            'prodi'                => 'required|string|max:100',
            'semester'             => 'required|max:20',
            'thn_akademik'        => 'required|max:20',
            'no_hp'                => 'required|max:15',
            'alamat'              => 'required|string',
            'tujuan'              => 'required|string|max:255',
            'nama_ortu'          => 'required|string|max:100',
            'nip_ortu'           => 'required|max:20',
            'pangkat_ortu'         => 'required|string|max:100',
            'pekerjaan_ortu'           => 'required|string|max:100',
            'instansi_ortu'   => 'required|string|max:100',
            'alamat_ortu'       => 'required|string',
            'validasi'            => 'required|string',
            'dosen_pa'     => 'required|string|max:100',
            'dokumen'  => 'sometimes|file|mimes:pdf|max:2048',
            'dokumen2' => 'sometimes|file|mimes:pdf|max:2048',
        ]);


        $signaturePath = $data->validasi;
        $supportingDocumentPath = $data->dokumen;
        $supportingDocument2Path = $data->dokumen2;

        // Jika signature baru dikirim (base64)
    if ($request->validasi && Str::startsWith($request->validasi, 'data:image')) {
        // Hapus file tanda tangan lama jika ada (optional)
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
    
    if ($request->hasFile('dokumen2')) {
        // Hapus file lama jika ada (optional)
        if ($supportingDocument2Path && \Storage::exists($supportingDocument2Path)) {
            \Storage::delete($supportingDocument2Path);
        }

        $file = $request->file('dokumen2');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $supportingDocument2Path = $file->storeAs('public/supporting_documents2', $filename);
    }

    $data->fill(array_merge(
        $request->except(['validasi', 'dokumen', 'dokumen2']),
        [
            'validasi' => $signaturePath,
            'dokumen' => $supportingDocumentPath,
            'dokumen2' => $supportingDocument2Path,
        ]
    ));
    $data->save();

        return redirect()->route('administrasi.surat_masih_kuliah', ['id' => $id])->with('success', 'Data berhasil diperbarui.');
    }

    public function previewPDF($id)
    {
        $data = $this->getLetterByDecryptedId($id);
        $this->attachAcademicAdvisorInfo($data);

        // 1. Generate surat aktif kuliah PDF (sementara simpan ke file)
        $generatedPDF = $this->generatePDF($data);
        $tempPath = storage_path('app/temp_surat.pdf');
        $generatedPDF->save($tempPath);

        // 2. Ambil dokumen pendukung 1
        $supportingFile1 = $data->dokumen;
        $relativePath1 = str_replace('public/', '', $supportingFile1);
        $supportingPath1 = storage_path('app/public/' . $relativePath1);

        // 3. Ambil dokumen pendukung 2
        $supportingFile2 = $data->dokumen2;
        $relativePath2 = str_replace('public/', '', $supportingFile2);
        $supportingPath2 = storage_path('app/public/' . $relativePath2);

        // 4. Cek keberadaan file
        if (!file_exists($supportingPath1)) {
            return response()->json(['message' => 'File dokumen pendukung 1 tidak ditemukan.'], 404);
        }

        if (!file_exists($supportingPath2)) {
            return response()->json(['message' => 'File dokumen pendukung 2 tidak ditemukan.'], 404);
        }

        // 5. Gabungkan semua
        $mergedPath = storage_path('app/merged_surat.pdf');
        $this->mergePDFs([$tempPath, $supportingPath1, $supportingPath2], $mergedPath);

        // 6. Return hasil gabungan
        return response()->stream(function () use ($mergedPath) {
            echo file_get_contents($mergedPath);
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="surat_masih_kuliah.pdf"',
        ]);
    }


    private function getLetterByDecryptedId($id)
    {
        $decryptedId = Crypt::decrypt($id);
        return SuratMasih::with([
            'letterNumber',
            'adminValidation',
            'advisorSignature',
            'headOfProgramSignature',
            'headOfDepartmentSignature'
        ])->findOrFail($decryptedId);
    }

    private function attachAcademicAdvisorInfo(&$data)
    {
        if (strlen($data->dosen_pa) === 36) {
            $sdm = DB::table('pdrd.sdm')
                ->where('id_sdm', $data->dosen_pa)
                ->select('nm_sdm', 'nip')
                ->first();

            $data->dosen_pa_nama = $sdm->nm_sdm ?? '';
            $data->dosen_pa_nip = $sdm->nip ?? null;
        } else {
            $data->dosen_pa_nama = null;
            $data->dosen_pa_nip = null;
        }
    }

    private function getAcademicAdvisors($studentId)
{
    // Ambil prodi mahasiswa dari reg_pd
    $prodiMahasiswa = DB::table('pdrd.reg_pd')
        ->where('id_pd', $studentId)
        ->value('id_sms');  // ambil id_sms prodi mahasiswa

    // Ambil dosen PA dari prodi yang sama dan aktif (home base)
    $dosenPA = DB::table('pdrd.sdm as s')
    ->join('pdrd.reg_ptk as r', 's.id_sdm', '=', 'r.id_sdm')
    ->join('pdrd.keaktifan_ptk as k', 'r.id_reg_ptk', '=', 'k.id_reg_ptk')
    ->where('r.id_sms', $prodiMahasiswa)
    ->where('k.a_sp_homebase', 1)       
    ->pluck('s.nm_sdm', 's.id_sdm');

    return $dosenPA;
}

        public function getCurrentAcademicYear()
    {
        $today = Carbon::today();

        $year = DB::table('ref.semester')
            ->whereDate('tgl_mulai', '<=', $today)
            ->whereDate('tgl_selesai', '>=', $today)
            ->orderByDesc('id_smt')
            ->first();

        if (!$year) {
            return 'Tahun Akademik Tidak Ditemukan';
        }

        // Ambil nama tahun akademik dari tabel tahun_ajaran
        $academicYear = DB::table('ref.tahun_ajaran')
            ->where('id_thn_ajaran', $year->id_thn_ajaran)
            ->value('nm_thn_ajaran');

        return $academicYear ?: 'Tahun Akademik Tidak Ditemukan';
    }


    private function calculateCurrentSemester($entryDate)
    {
        // Konversi tgl masuk ke Carbon
        $entry = Carbon::parse($entryDate);
        $now = Carbon::now();

        // Hitung jumlah bulan antara sekarang dan tanggal masuk
        $diffInMonths = $entry->diffInMonths($now);

        // Setiap semester 6 bulan
        $semesterNumber = (int) floor($diffInMonths / 6) + 1;

        return $semesterNumber;
    }


    private function generatePDF($data)
    {
        $pathImage = 'file://' . public_path('images/kop_fakultas.png');
        $pathImageLogo = 'file://' . public_path('images/logo-unila.png');

        $advisorQrCode = null;
        if (!empty($data->advisorSignature->short_code)) {
            $url = route('smk.preview', ['code' => $data->advisorSignature->short_code]);
            $qrImage = QrCode::format('png')->size(100)->generate($url);
            $advisorQrCode = 'data:image/png;base64,' . base64_encode($qrImage);
        }

        $headOfProgramQrCode = null;
        if (!empty($data->headOfProgramSignature->short_code)) {
            $url = route('smk.preview', ['code' => $data->headOfProgramSignature->short_code]);
            $qrImage = QrCode::format('png')->size(100)->generate($url);
            $headOfProgramQrCode = 'data:image/png;base64,' . base64_encode($qrImage);
        }

        $headOfDepartementQrCode = null;
        if (!empty($data->headOfDepartmentSignature->short_code)) {
            $url = route('smk.preview', ['code' => $data->headOfDepartmentSignature->short_code]);
            $qrImage = QrCode::format('png')->size(100)->generate($url);
            $headOfDepartementQrCode = 'data:image/png;base64,' . base64_encode($qrImage);
        }

        return Pdf::loadView('administrasi.surat_masih_kuliah.combined_template', [
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