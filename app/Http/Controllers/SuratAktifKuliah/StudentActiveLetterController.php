<?php

namespace App\Http\Controllers\SuratAktifKuliah;

use App\Http\Controllers\Controller;
use App\Models\SuratAktifKuliah\SuratAktif;
use App\Models\SuratAktifKuliah\ValidasiSurat;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentActiveLetterController extends Controller
{
    // mengambil data surat berdasarkan short code tertentu
    private function getLetterByShortCode($kode)
    {
        // Mencari data di tabel letter_signatures berdasarkan short_code. Kalau tidak ketemu, akan muncul error 404
        $signature = ValidasiSurat::where('short_code', $kode)->firstOrFail();
        // Mencari data surat aktif kuliah (StudentActiveLetter) berdasarkan submission_id dari signature, sekaligus memuat relasi letterNumber
        $letter = SuratAktif::with('letterNumber')->where('id', $signature->submission_id)->firstOrFail();

        // Menambahkan properti signature ke objek surat supaya bisa diakses nanti
        $letter->validasi = $signature;

        // Mengembalikan data surat yang lengkap dengan relasi dan signature-nya
        return $letter;
    }


    // untuk menambahkan informasi dosen pembimbing akademik ke data surat (nama dan NIP)
    private function attachAcademicAdvisorInfo($data)
    {
        // Mengecek apakah nilai academic_advisor berbentuk UUID (biasanya panjangnya 36 karakter). Kalau ya, berarti itu ID dari tabel SDM (dosen/staf)
        if (strlen($data->dosen_pa) === 36) {
            // Mengambil data dosen pembimbing dari tabel sdm di skema pdrd, berdasarkan id_sdm
            $sdm = DB::table('pdrd.sdm')
                ->where('id_sdm', $data->dosen_pa)
                ->select('nm_sdm', 'nip')
                ->first();

                // Menyimpan nama dan NIP pembimbing ke dalam objek data, atau kosong/null jika tidak ditemukan
            $data->dosen_pa_nama = $sdm->nm_sdm ?? '';
            $data->dosen_pa_nip = $sdm->nip ?? null;
        } else {
            // Jika dosen_pa bukan UUID, maka dianggap tidak valid, jadi isi nama dan NIP-nya di-set null
            $data->dosen_pa_nama = null;
            $data->dosen_pa_nip = null;
        }
    }


    // untuk menampilkan preview surat dalam bentuk PDF berdasarkan short code
    public function preview($kode)
    {
        // Mengambil data surat dan signature berdasarkan short kode
        $data = $this->getLetterByShortCode($kode);
        // Menambahkan data dosen pembimbing ke dalam surat.
        $this->attachAcademicAdvisorInfo($data);
        // Memanggil method generateSignature
        return $this->generateSignature($data)->stream('surat_aktif_kuliah.pdf');
    }

    // untuk menampilkan tampilan hasil scan QR code dari surat
    public function seeSignatureByShortCode($kode)
    {
        // Ambil data surat dan dosen pembimbing 
        $data = $this->getLetterByShortCode($kode);
        $this->attachAcademicAdvisorInfo($data);

        // Mengambil nama dan NIP dari orang yang membuat tanda tangan, jika tersedia
        $nama = $data->validasi->createdBySdm->nm_sdm ?? null;
        $nip = $data->validasi->createdBySdm->nip ?? null;

        return view('administrasi.surat_aktif_kuliah.student_active_letter_qr_result_v2', compact('data', 'nama', 'nip'));
    }
}
