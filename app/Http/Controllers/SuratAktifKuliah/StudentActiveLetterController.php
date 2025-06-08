<?php

namespace App\Http\Controllers\SuratAktifKuliah;

use App\Http\Controllers\Controller;
use App\Models\SuratAktifKuliah\StudentActiveLetter;
use App\Models\SuratAktifKuliah\LetterSignature;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentActiveLetterController extends Controller
{

    private function getLetterByShortCode($code)
    {
        $signature = LetterSignature::where('short_code', $code)->firstOrFail();
        $letter = StudentActiveLetter::with('letterNumber')->where('id', $signature->submission_id)->firstOrFail();

        $letter->signature = $signature;

        return $letter;
    }


    private function attachAcademicAdvisorInfo($data)
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


    public function preview($code)
    {
        $data = $this->getLetterByShortCode($code);
        $this->attachAcademicAdvisorInfo($data);
        return $this->generateSignature($data)->stream('surat_aktif_kuliah.pdf');
    }

    public function seeSignatureByShortCode($code)
    {

        $data = $this->getLetterByShortCode($code);
        $this->attachAcademicAdvisorInfo($data);

        $name = $data->signature->createdBySdm->nm_sdm ?? null;
        $nip = $data->signature->createdBySdm->nip ?? null;

        return view('administrasi.surat_aktif_kuliah.student_active_letter_qr_result_v2', compact('data', 'name', 'nip'));
    }
}
