<?php

namespace App\Http\Controllers\SuratMasihKuliah;

use App\Http\Controllers\Controller;
use App\Models\SuratMasihKuliah\SuratMasih;
use App\Models\SuratMasihKuliah\ValidasiSurat;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StillStudyLetterController extends Controller
{

    private function getLetterByShortCode($kode)
    {
        $signature = ValidasiSurat::where('short_code', $kode)->firstOrFail();
        $letter = SuratMasih::with('letterNumber')->where('id', $signature->submission_id)->firstOrFail();

        $letter->validasi = $signature;

        return $letter;
    }


    private function attachAcademicAdvisorInfo($data)
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


    public function preview($kode)
    {
        $data = $this->getLetterByShortCode($kode);
        $this->attachAcademicAdvisorInfo($data);
        return $this->generateSignature($data)->stream('surat_masih_kuliah.pdf');
    }

    public function seeSignatureByShortCode($kode)
    {

        $data = $this->getLetterByShortCode($kode);
        $this->attachAcademicAdvisorInfo($data);

        $nama = $data->validasi->createdBySdm->nm_sdm ?? null;
        $nip = $data->validasi->createdBySdm->nip ?? null;

        return view('administrasi.surat_masih_kuliah.still_study_letter_qr_result', compact('data', 'nama', 'nip'));
    }
}
