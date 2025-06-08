<?php

namespace App\Http\Controllers\Validasi;

use Illuminate\Http\Request;
use App\Models\Kpta\SeminarProdi;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Models\Validasi\VerAjuanPdmSeminar;

class RiwayatSeminarKaprodiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $prodi = GetProdiIndividu();
        $query = "
            SELECT 
                valid.id_ver_ajuan,
                ajuan.id_ajuan_pdm_seminar,
                ajuan.id_jns_seminar_lama,
                ajuan.stat_ajuan,
                ajuan.nilai_seminar_baru as nilai_seminar,
                jns.nm_jns_seminar,
                tr.nim,
                pd.nm_pd,
                ajuan.wkt_ajuan,
                tseminar.id_mk as id_mk_seminar,
                kls_mhs.id_mk as id_mk_kls,
                kls_mhs.nm_kls,
                kls_mhs.nilai_angka,
                CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS asal_prodi,
                DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan
            FROM validasi.ajuan_pdm_seminar AS ajuan
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar = ajuan.id_jns_seminar_lama
            JOIN validasi.ver_ajuan_pdm_seminar AS valid ON valid.id_ajuan_pdm_seminar = ajuan.id_ajuan_pdm_seminar
            JOIN pdrd.peserta_didik AS pd ON pd.id_pd = ajuan.id_pd AND pd.soft_delete=0
            JOIN pdrd.reg_pd AS tr ON tr.id_pd = pd.id_pd AND tr.soft_delete=0
            JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0
            JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
            JOIN kpta.seminar_prodi AS tseminar ON tseminar.id_sms = tprodi.id_sms AND tseminar.id_jns_seminar=ajuan.id_jns_seminar_lama
            LEFT JOIN (
                SELECT DISTINCT 
                    kls.id_mk,
                    kls.id_kls,
                    kls.nm_kls,
                    kuliah_mhs.id_reg_pd,
                    kuliah_mhs.nilai_angka
                FROM pdrd.kelas_kuliah AS kls
                LEFT JOIN pdrd.perkuliahan_mahasiswa AS kuliah_mhs ON kuliah_mhs.id_kls = kls.id_kls 
                ) AS kls_mhs ON kls_mhs.id_mk = tseminar.id_mk AND kls_mhs.id_reg_pd = tr.id_reg_pd
            WHERE ajuan.soft_delete=0
            AND tprodi.id_sms = '" . session()->get('login.peran.id_organisasi') . "'
            AND ajuan.stat_ajuan= '2'
            AND valid.status_periksa IN ('Y')
            ORDER BY ajuan.last_update DESC;    
        ";
        $data = DB::SELECT($query);

        return view('validasi.rwy_seminar_kaprodi.index', compact('data', 'prodi'));
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
    public function show($id, PesertaDidik $pesertaDidik)
    {
        $id_ver_ajuan = Crypt::decrypt($id);
        $ajuan = VerAjuanPdmSeminar::find($id_ver_ajuan);
        $data = $ajuan->ajuanSeminar;
        $profil = $pesertaDidik->id_detail_mahasiswa($data->id_pd);
        $dokumen = DB::SELECT("
            SELECT list.id_ajuan_pdm_seminar, list.id_dok_ajuan_seminar, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_seminar AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_ajuan_pdm_seminar='" . $data->id_ajuan_pdm_seminar . "'
        ");
        return view('validasi.rwy_seminar_kaprodi.detail', compact('data', 'profil', 'dokumen', 'ajuan'));
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
