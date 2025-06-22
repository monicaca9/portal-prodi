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
        // 46: Dosen PA 6: Admin 3000: Kaprodi 3001: Ketua Jurusan
        $userRole = session()->get('login.peran.id_peran');
        $userId = Auth::check()
            ? auth()->user()->id_sdm_pengguna
            : (request()->get('id_sdm_pengguna') ?? Str::uuid());

        $query = StudentActiveLetter::query();

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

        $studentActiveLetters = $query->latest()->get();

        foreach ($studentActiveLetters as $letter) {
            $disable = match ($userRole) {
                6     => $letter->admin_validation_id,
                46 => $letter->advisor_signature_id,
                3000  => $letter->head_of_program_signature_id,
                3001  => $letter->head_of_department_signature_id,
                default => false,
            };
            $letter->disable_validation_button = $disable;
        }

        return view('validasi.surat_aktif_kuliah.index', compact('studentActiveLetters'));
    }



    public function history(Request $request)
    {
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

        $studentActiveLetters = $query->latest()->get();

        return view('validasi.surat_aktif_kuliah.history', compact('studentActiveLetters'));
    }

    public function exportExcel(Request $request)
    {
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

        $studentActiveLetters = $query->latest()->get();

        return Excel::download(new StudentActiveLetterExport($studentActiveLetters), 'riwayat_surat_aktif_kuliah.xlsx');
    }




    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $data = StudentActiveLetter::with('letterNumber')->where('id', $decryptedId)->first();

        $defaultCode = 'UN26.15.07/KM';
        $defaultYear = date('Y');

        $code = $data->letterNumber->code ?? $defaultCode;
        $year = $data->letterNumber->year ?? $defaultYear;

        $nextNumber = $data->letterNumber->number ?? LetterNumber::where('year', $year)
            ->where('code', $code)
            ->orderBy('number', 'desc')
            ->value('number') + 1 ?? 1;

        $studentActiveLetters = StudentActiveLetter::where('student_number', $data->student_number)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('validasi.surat_aktif_kuliah.edit', compact('data', 'code', 'year', 'nextNumber', 'studentActiveLetters'));
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

        $letter = StudentActiveLetter::with([
            'adminValidation',
            'advisorSignature',
            'headOfProgramSignature',
            'headOfDepartmentSignature',
            'letterNumber',
        ])->findOrFail($decryptedId);

        $signature = match ($userRole) {
            6 => $letter->adminValidation,
            46 => $letter->advisorSignature,
            3000 => $letter->headOfProgramSignature,
            3001 => $letter->headOfDepartmentSignature,
            default => null,
        };

        if (!$signature) {
            $signature = new LetterSignature();
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
        $exists = LetterNumber::where('year', $currentYear)
            ->where('code', $code)
            ->where('number', $manualNumber)
            ->exists();

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
        StudentActiveLetter::find($decryptedId);

        return redirect()->route('validasi.surat_aktif_kuliah', ['id' => $id])
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

        $supportingFile = $data->supporting_document;
        $relativePath = str_replace('public/', '', $supportingFile);
        $supportingPath = storage_path('app/public/' . $relativePath);


        if (!file_exists($supportingPath)) {
            return response()->json(['message' => 'File dokumen pendukung tidak ditemukan.'], 404);
        }

        // 3. Gabungkan
        $mergedPath = storage_path('app/merged_surat.pdf');
        $this->mergePDFs([$tempPath, $supportingPath], $mergedPath);

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