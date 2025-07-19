<?php

namespace App\Http\Controllers\Administrasi\SuratMasihKuliah;

use App\Http\Controllers\Controller;
use App\Models\SuratMasihKuliah\StillStudyLetter;
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
        $stillStudyLetters = StillStudyLetter::where('created_by', auth()->user()->id_pd_pengguna)
            ->latest()
            ->get();
        return view('administrasi.surat_masih_kuliah.index', compact('stillStudyLetters'));
    }


    public function history(Request $request, PesertaDidik $pesertaDidik)
    {
        $query = StillStudyLetter::where('created_by', auth()->user()->id_pd_pengguna);

        if ($request->filled('created_start')) {
            $query->whereDate('created_at', '>=', $request->created_start);
        }

        if ($request->filled('created_end')) {
            $query->whereDate('created_at', '<=', $request->created_end);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stillStudyLetters = $query->latest()->get();

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

        $data = new StillStudyLetter();
        $data->fill([
            'id'                => Str::uuid(),
            'name'              => $profile->nm_pd,
            'student_number'    => $profile->nim,
            'department'        => $jurusan,       
            'study_program'     => $profile->prodi,
            'semester'          => $semesterNumber,
            'academic_year'     => $academicYear,
            'phone_number'      => $profile->tlpn_hp,
            'address'           => $profile->jln,
            'purpose'           => '',
            'parent_name'       => $profile->nm_ayah,
            'parent_nip'        => '',
            'parent_grade'      => '',
            'parent_job'        => $profile->nm_pekerjaan_ayah,
            'parent_institution' => '',     
            'parent_address'    => '',
            'signature'         => '',
            'academic_advisor'  => '',
        ]);

        $studentId = $profile->id_pd ?? null;
        
        $academicAdvisors = $this->getAcademicAdvisors($studentId);
        $semesters = []; 
        $currentAcademicYear = $this->getCurrentAcademicYear();

        return view('administrasi.surat_masih_kuliah.create', compact('profile', 'data', 'academicAdvisors', 'semesters', 'currentAcademicYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:100',
            'student_number'       => 'required|max:10',
            'department'           => 'required|string|max:100',
            'study_program'        => 'required|string|max:100',
            'semester'             => 'required|max:20',
            'academic_year'        => 'required|max:20',
            'phone_number'         => 'required|max:15',
            'address'              => 'required|string',
            'purpose'              => 'required|string|max:255',
            'parent_name'          => 'required|string|max:100',
            'parent_nip'           => 'required|max:20',
            'parent_grade'         => 'required|string|max:100',
            'parent_job'           => 'required|string|max:100',
            'parent_institution'   => 'required|string|max:100',
            'parent_address'       => 'required|string',
            'signature'            => 'required|string',
            'academic_advisor'     => 'required|string|max:100',
            'supporting_document'  => 'required|file|mimes:pdf|max:2048',
            'supporting_document2' => 'required|file|mimes:pdf|max:2048',
        ]);

        $signaturePath = null;
        $supportingDocumentPath = null;

        // Simpan signature dari base64
    if ($request->signature && Str::startsWith($request->signature, 'data:image')) {
        $imageData = $request->signature;
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
if ($request->hasFile('supporting_document')) {
    $file = $request->file('supporting_document');
    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
    $supportingDocumentPath = $file->storeAs('public/supporting_documents', $filename);
}

// Simpan dokumen pendukung 2
$supportingDocument2Path = null;
if ($request->hasFile('supporting_document2')) {
    $file2 = $request->file('supporting_document2');
    $filename2 = time() . '_' . uniqid() . '.' . $file2->getClientOriginalExtension();
    $supportingDocument2Path = $file2->storeAs('public/supporting_documents2', $filename2);
}

// Simpan ke database
$data = new StillStudyLetter();
$data->fill(array_merge(
    $request->except(['signature', 'supporting_document', 'supporting_document2']),
    [
        'id' => Str::uuid(),
        'signature' => $signaturePath,
        'supporting_document' => $supportingDocumentPath,
        'supporting_document2' => $supportingDocument2Path,
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


    public function detail($id)
    {
        $data = $this->getLetterByDecryptedId($id);
        $this->attachAcademicAdvisorInfo($data);

        return view('administrasi.surat_masih_kuliah.detail', compact('data'));
    }

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
            'name'                 => 'required|string|max:100',
            'student_number'       => 'required|max:10',
            'department'           => 'required|string|max:100',
            'study_program'        => 'required|string|max:100',
            'semester'             => 'required|max:20',
            'academic_year'        => 'required|max:20',
            'phone_number'         => 'required|max:15',
            'address'              => 'required|string',
            'purpose'              => 'required|string|max:255',
            'parent_name'          => 'required|string|max:100',
            'parent_nip'           => 'required|max:20',
            'parent_grade'         => 'required|string|max:100',
            'parent_job'           => 'required|string|max:100',
            'parent_institution'   => 'required|string|max:100',
            'parent_address'       => 'required|string',
            'signature'            => 'required|string',
            'academic_advisor'     => 'required|string|max:100',
            'supporting_document'  => 'sometimes|file|mimes:pdf|max:2048',
            'supporting_document2' => 'sometimes|file|mimes:pdf|max:2048',
        ]);


        $signaturePath = $data->signature;
        $supportingDocumentPath = $data->supporting_document;
        $supportingDocument2Path = $data->supporting_document2;

        // Jika signature baru dikirim (base64)
    if ($request->signature && Str::startsWith($request->signature, 'data:image')) {
        // Hapus file tanda tangan lama jika ada (optional)
        if ($signaturePath && \Storage::exists($signaturePath)) {
            \Storage::delete($signaturePath);
        }

        // Proses simpan tanda tangan baru dari base64
        $imageData = $request->signature;
        list($type, $imageData) = explode(';', $imageData);
        list(, $imageData) = explode(',', $imageData);
        $imageData = base64_decode($imageData);

        $filename = time() . '_' . uniqid() . '.png';
        $path = storage_path('app/public/signatures/' . $filename);
        file_put_contents($path, $imageData);

        $signaturePath = 'public/signatures/' . $filename;
    }

    // Update dokumen pendukung jika ada upload baru
    if ($request->hasFile('supporting_document')) {
        // Hapus file lama jika ada (optional)
        if ($supportingDocumentPath && \Storage::exists($supportingDocumentPath)) {
            \Storage::delete($supportingDocumentPath);
        }

        $file = $request->file('supporting_document');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $supportingDocumentPath = $file->storeAs('public/supporting_documents', $filename);
    }
    
    if ($request->hasFile('supporting_document2')) {
        // Hapus file lama jika ada (optional)
        if ($supportingDocument2Path && \Storage::exists($supportingDocument2Path)) {
            \Storage::delete($supportingDocument2Path);
        }

        $file = $request->file('supporting_document2');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $supportingDocument2Path = $file->storeAs('public/supporting_documents2', $filename);
    }

    $data->fill(array_merge(
        $request->except(['signature', 'supporting_document', 'supporting_document2']),
        [
            'signature' => $signaturePath,
            'supporting_document' => $supportingDocumentPath,
            'supporting_document2' => $supportingDocument2Path,
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
        $supportingFile1 = $data->supporting_document;
        $relativePath1 = str_replace('public/', '', $supportingFile1);
        $supportingPath1 = storage_path('app/public/' . $relativePath1);

        // 3. Ambil dokumen pendukung 2
        $supportingFile2 = $data->supporting_document2;
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
        return StillStudyLetter::with([
            'numberLetter',
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