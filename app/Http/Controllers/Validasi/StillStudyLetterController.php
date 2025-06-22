<?php

namespace App\Http\Controllers\Validasi;

use App\Http\Controllers\Controller;
use App\Models\SuratMasihKuliah\StillStudyLetter;
use App\Models\SuratMasihKuliah\NumberLetter;
use App\Models\SuratMasihKuliah\SignatureLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Exports\StillStudyLetterExport;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Fpdi;

class StillStudyLetterController extends Controller
{
    // Menampilkan daftar surat masih kuliah sesuai peran pengguna yang sedang login
    public function index(Request $request)
    {
        // 46: Dosen PA 6: Admin 3000: Kaprodi 3001: Ketua Jurusan
        $userRole = session()->get('login.peran.id_peran');
        $userId = Auth::check()
            ? auth()->user()->id_sdm_pengguna
            : (request()->get('id_sdm_pengguna') ?? Str::uuid());

        $query = StillStudyLetter::query();

        if ($userRole == 46) {
            $query->where('status', '!=', 'dibuat')
                ->where('academic_advisor', $userId);
        } elseif ($userRole == 6) {
            // $query->where('status', '!=', 'dibuat');

            $query->whereHas('advisorSignature', function ($q) {
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
            $query->where('status', '!=', 'dibuat');
        }


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stillStudyLetters = $query->latest()->get();

        foreach ($stillStudyLetters as $letter) {
            $disable = match ($userRole) {
                6     => $letter->admin_validation_id,
                46 => $letter->advisor_signature_id,
                3000  => $letter->head_of_program_signature_id,
                3001  => $letter->head_of_department_signature_id,
                default => false,
            };
            $letter->disable_validation_button = $disable;
        }

        return view('validasi.surat_masih_kuliah.index', compact('stillStudyLetters'));
    }

    public function history(Request $request)
    {
        $query = StillStudyLetter::where('status', '!=', 'dibuat');

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

        return view('validasi.surat_masih_kuliah.history', compact('stillStudyLetters'));
    }

    public function exportExcel(Request $request)
    {
        $query = StillStudyLetter::where('status', '!=', 'dibuat');

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

        return Excel::download(new StillStudyLetterExport($stillStudyLetters), 'riwayat_surat_masih_kuliah.xlsx');
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $data = StillStudyLetter::with('numberLetter')->where('id', $decryptedId)->first();

        $defaultCode = 'UN26.15.07/KM';
        $defaultYear = date('Y');

        $code = $data->numberLetter->code ?? $defaultCode;
        $year = $data->numberLetter->year ?? $defaultYear;

        $nextNumber = $data->numberLetter->number ?? NumberLetter::where('year', $year)
            ->where('code', $code)
            ->orderBy('number', 'desc')
            ->value('number') + 1 ?? 1;

        $stillStudyLetters = StillStudyLetter::where('student_number', $data->student_number)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('validasi.surat_masih_kuliah.edit', compact('data', 'code', 'year', 'nextNumber', 'stillStudyLetters'));
    }


    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $role = session()->get('login.peran');
        $userRole = $role['id_peran'] ?? null;

        $validRoles = [6, 46, 3000, 3001];
        if (!in_array($userRole, $validRoles)) {
            return response()->json([
                'message' => 'Data berhasil diperbarui.',
                'user_role' => $userRole,
            ]);
        }

        $rules = [
            'status' => 'required|in:disetujui,ditolak',
            'notes' => 'required_if:status,ditolak',
        ];
        $request->validate($rules);

        $letter = StillStudyLetter::with([
            'adminValidation',
            'advisorSignature',
            'headOfProgramSignature',
            'headOfDepartmentSignature',
            'numberLetter',
        ])->findOrFail($decryptedId);

        $signature = match ($userRole) {
            6 => $letter->adminValidation,
            46 => $letter->advisorSignature,
            3000 => $letter->headOfProgramSignature,
            3001 => $letter->headOfDepartmentSignature,
            default => null,
        };

        if (!$signature) {
            $signature = new SignatureLetter();
            $signature->id = Str::uuid();

            $signature->submission_id = $letter->id;
            $signature->role = $userRole;
        }

        $signature->status = $request->status;
        $signature->notes = $request->notes;

        if ($request->status === 'disetujui') {
            $signature->short_code = Str::random(10);
        }

        $signature->save();

    if ($userRole == 6 && $request->status === 'disetujui' && !$letter->letter_number) {
        $currentYear = date('Y');
        $code = $request->letter_number['code'];
        $manualNumber = $request->letter_number['number'] ?? null;

    // 🔎 Validasi: Pastikan nomor surat tidak duplikat jika user isi manual
    if ($manualNumber) {
        $exists = NumberLetter::where('year', $currentYear)
            ->where('code', $code)
            ->where('number', $manualNumber)
            ->exists();

        if ($exists) {
            return redirect()->route('validasi.surat_masih_kuliah.edit', ['id' => $id])
                ->with('error', 'Nomor surat sudah digunakan.');
        }
    }

    // 🔢 Generate data nomor surat (pakai manual jika ada)
    $data = NumberLetter::generateNextLetterNumber($currentYear, $code, $manualNumber);

    // 🔗 Hubungkan ke surat
    $data['letter_id'] = $letter->id;

    // 💾 Simpan ke database
    NumberLetter::create($data);

    // 🔁 Update surat dengan ID letter_number
    $letter->letter_number = $data['id'];
    $letter->save();
}


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

        $letter->updateStatusBasedOnSignatures();
        StillStudyLetter::find($decryptedId);

        return redirect()->route('validasi.surat_masih_kuliah', ['id' => $id])
            ->with('success', 'Data berhasil divalidasi.');
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

    public function downloadPDF($id)
    {
        $data = $this->getLetterByDecryptedId($id);
        $this->attachAcademicAdvisorInfo($data);
        return $this->generatePDF($data)->download('surat_masih_kuliah.pdf');
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