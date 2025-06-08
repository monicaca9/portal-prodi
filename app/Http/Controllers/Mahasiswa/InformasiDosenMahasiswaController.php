<?php

namespace App\Http\Controllers\Mahasiswa;

use Illuminate\Http\Request;
use App\Models\Kpta\PeranSeminar;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InformasiDosenMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PesertaDidik $pesertaDidik)
    {
        $prodi = GetProdiIndividu();
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        // $dosen_mahasiswa = PeranSeminar::join('ref.jenis_seminar', 'peran_seminar.id_jns_seminar', '=', 'jenis_seminar.id_jns_seminar')
        // ->select('peran_seminar.id_jns_seminar', 'peran', 'peran_seminar.urutan')
        //     ->where('id_reg_pd', $profil->id_reg_pd)
        //     ->where('a_aktif', 1)
        //     ->orderBy('peran_seminar.id_jns_seminar', 'ASC')
        //     ->get() ->groupBy('id_jns_seminar');
        $dosen_mahasiswa = DB::SELECT("
        SELECT 
            peran.id_peran_seminar,
            tsdm.nm_sdm,
            tsdm.nip,
            peran.peran,
            peran.urutan,
            peran.nm_pembimbing_luar_kampus,
            peran.nm_penguji_luar_kampus,
            CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS nm_lemb,
            CONCAT(peran.nm_pemb_lapangan,' (',peran.jabatan,')') AS nm_pemb_lapangan,
            jenis.nm_jns_seminar, 
            peran.id_jns_seminar
        FROM kpta.peran_seminar AS peran
        JOIN ref.jenis_seminar AS jenis ON jenis.id_jns_seminar = peran.id_jns_seminar
        LEFT JOIN pdrd.sdm AS tsdm ON tsdm.id_sdm = peran.id_sdm
        LEFT JOIN pdrd.reg_ptk AS tr ON tr.id_sdm = tsdm.id_sdm
        LEFT JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms
        LEFT JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
        WHERE peran.id_reg_pd = '" . $profil->id_reg_pd . "'
        AND peran.soft_delete = 0
        AND peran.a_aktif = 1
        AND peran.a_ganti = 0
        ORDER BY peran.peran ASC
        ");
        $dosen_mahasiswa = collect($dosen_mahasiswa)->groupBy('id_jns_seminar');
        // dd($dosen_mahasiswa);
        return view('pendaftaran_seminar.informasi_dosen_mahasiswa.index', compact('prodi', 'dosen_mahasiswa', 'profil'));
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
    public function show(string $id)
    {
        //
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
