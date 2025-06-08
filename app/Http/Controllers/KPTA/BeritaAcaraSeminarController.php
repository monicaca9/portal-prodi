<?php

namespace App\Http\Controllers\Kpta;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use App\Models\Manajemen\Ruang;
use App\Models\Manajemen\Gedung;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\NilaiAkhirSeminar;
use App\Models\Kpta\PendaftaranSeminar;
use App\Models\Kpta\NomorBaDaftarSeminar;

class BeritaAcaraSeminarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show( $id, PesertaDidik $pesertaDidik)
    {
        $id_daftar_seminar = Crypt::decrypt($id); 
        $data = PendaftaranSeminar::find($id_daftar_seminar); 
        $no_ba_seminar = NomorBaDaftarSeminar::where('id_daftar_seminar', $id_daftar_seminar)->where('soft_delete', 0)->first();
        $nm_jns_seminar = $data->SeminarProdi->jenisSeminar->nm_jns_seminar;
        $profil = $pesertaDidik->id_detail_mahasiswa($data->RegPd->id_pd);
        $ruang = Ruang::where('id_ruang', $data->id_ruang)->first();
        $gedung = Gedung::where('id_gedung', $ruang->id_gedung)->first();

        $komponen_nilai = DB::table('kpta.skor_per_komponen as skor_komponen')
            ->join('kpta.list_komponen_nilai_seminar as list_komponen', 'list_komponen.id_list_komponen_nilai', '=', 'skor_komponen.id_list_komponen_nilai')
            ->join('kpta.komponen_nilai_seminar as komponen', 'komponen.id_komponen_nilai', '=', 'list_komponen.id_komponen_nilai')
            ->join('kpta.list_kategori_nilai_seminar as list_kategori', 'list_kategori.id_list_kategori_nilai', '=', 'list_komponen.id_list_kategori_nilai')
            ->join('kpta.kategori_nilai_seminar as kategori', 'kategori.id_kategori_nilai', '=', 'list_kategori.id_kategori_nilai')
            ->join('kpta.peran_dosen_pendaftar as peran_dosen', 'peran_dosen.id_peran_dosen_pendaftar', '=', 'skor_komponen.id_peran_dosen_pendaftar')
            ->join('kpta.peran_seminar as peran', 'peran.id_peran_seminar', '=', 'peran_dosen.id_peran_seminar')
            ->leftjoin('pdrd.sdm as tsdm', 'tsdm.id_sdm', '=', 'peran.id_sdm')
            ->where([
                ['peran_dosen.id_daftar_seminar', $id_daftar_seminar],
                ['skor_komponen.soft_delete', 0],
                ['peran_dosen.soft_delete', 0],
                ['list_komponen.soft_delete', 0],
                ['list_kategori.soft_delete', 0],
                ['peran.soft_delete', 0],
                ['peran.a_aktif', 1]
            ])
            ->select(
                'skor_komponen.id_peran_dosen_pendaftar',
                'kategori.nm_kategori_nilai',
                'list_kategori.id_list_kategori_nilai',
                'komponen.nm_komponen_nilai',
                'skor_komponen.skor',
                'peran.peran',
                'peran.urutan',
                'peran.nm_pembimbing_luar_kampus',
                'peran.nm_penguji_luar_kampus',
                'peran.nm_pemb_lapangan',
                'tsdm.nm_sdm',
                'tsdm.nip'
            )
            ->get()
            ->groupBy('id_peran_dosen_pendaftar')
            ->map(function ($items) {
                return $items->groupBy('id_list_kategori_nilai');
            });
        // dd($komponen_nilai);

        $data_skor_kategori = DB::table('kpta.avg_skor_kategori as skor_kategori')
            ->join('kpta.peran_dosen_pendaftar as peran_dosen', 'peran_dosen.id_peran_dosen_pendaftar', '=', 'skor_kategori.id_peran_dosen_pendaftar')
            ->join('kpta.peran_seminar as peran', 'peran.id_peran_seminar', '=', 'peran_dosen.id_peran_seminar')
            ->leftjoin('pdrd.sdm as tsdm', 'tsdm.id_sdm', '=', 'peran.id_sdm')
            ->where('peran_dosen.id_daftar_seminar', $id_daftar_seminar)
            ->where('peran_dosen.soft_delete', 0)
            ->orderby('peran.peran', 'ASC')
            ->orderby('peran.urutan', 'ASC')
            ->get();
        // dd($data_skor_kategori);

        $data_distribusi_nilai =  DB::table('kpta.distribusi_nilai as distribusi')
            ->where('distribusi.id_seminar_prodi', $data->id_seminar_prodi)
            ->where('soft_delete', 0)
            ->get();
        $data_nilai_seminar = NilaiAkhirSeminar::where('id_daftar_seminar', $id_daftar_seminar)->where('soft_delete', 0)->first();

        // dd($data_skor_kategori,$data_nilai_seminar);

        $html = view('pendaftaran_seminar.beritaacara.berita', compact('komponen_nilai', 'data', 'nm_jns_seminar', 'profil', 'ruang', 'gedung', 'no_ba_seminar', 'data_distribusi_nilai', 'data_skor_kategori', 'data_nilai_seminar'));

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);


        $dompdf->loadhtml($html);


        $dompdf->setPaper('A4', 'potrait');

        $dompdf->render();

        $dompdf->stream("beritaacara.pdf", array("Attachment" => false));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
