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
    // mengambil data surat berdasarkan short code tertentu
    private function getLetterByShortCode($code)
    {
        // Mencari data di tabel letter_signatures berdasarkan short_code. Kalau tidak ketemu, akan muncul error 404
        $signature = LetterSignature::where('short_code', $code)->firstOrFail();
        // Mencari data surat aktif kuliah (StudentActiveLetter) berdasarkan submission_id dari signature, sekaligus memuat relasi letterNumber
        $letter = StudentActiveLetter::with('letterNumber')->where('id', $signature->submission_id)->firstOrFail();

        // Menambahkan properti signature ke objek surat supaya bisa diakses nanti
        $letter->signature = $signature;

        // Mengembalikan data surat yang lengkap dengan relasi dan signature-nya
        return $letter;
    }


    // untuk menambahkan informasi dosen pembimbing akademik ke data surat (nama dan NIP)
    private function attachAcademicAdvisorInfo($data)
    {
        // Mengecek apakah nilai academic_advisor berbentuk UUID (biasanya panjangnya 36 karakter). Kalau ya, berarti itu ID dari tabel SDM (dosen/staf)
        if (strlen($data->academic_advisor) === 36) {
            // Mengambil data dosen pembimbing dari tabel sdm di skema pdrd, berdasarkan id_sdm
            $sdm = DB::table('pdrd.sdm')
                ->where('id_sdm', $data->academic_advisor)
                ->select('nm_sdm', 'nip')
                ->first();

                // Menyimpan nama dan NIP pembimbing ke dalam objek data, atau kosong/null jika tidak ditemukan
            $data->academic_advisor_name = $sdm->nm_sdm ?? '';
            $data->academic_advisor_nip = $sdm->nip ?? null;
        } else {
            // Jika academic_advisor bukan UUID, maka dianggap tidak valid, jadi isi nama dan NIP-nya di-set null
            $data->academic_advisor_name = null;
            $data->academic_advisor_nip = null;
        }
    }


    // untuk menampilkan preview surat dalam bentuk PDF berdasarkan short code
    public function preview($code)
    {
        // Mengambil data surat dan signature berdasarkan short code
        $data = $this->getLetterByShortCode($code);
        // Menambahkan data dosen pembimbing ke dalam surat.
        $this->attachAcademicAdvisorInfo($data);
        // Memanggil method generateSignature
        return $this->generateSignature($data)->stream('surat_aktif_kuliah.pdf');
    }

    // untuk menampilkan tampilan hasil scan QR code dari surat
    public function seeSignatureByShortCode($code)
    {
        // Ambil data surat dan dosen pembimbing 
        $data = $this->getLetterByShortCode($code);
        $this->attachAcademicAdvisorInfo($data);

        // Mengambil nama dan NIP dari orang yang membuat tanda tangan, jika tersedia
        $name = $data->signature->createdBySdm->nm_sdm ?? null;
        $nip = $data->signature->createdBySdm->nip ?? null;

        return view('administrasi.surat_aktif_kuliah.student_active_letter_qr_result_v2', compact('data', 'name', 'nip'));
    }
}
