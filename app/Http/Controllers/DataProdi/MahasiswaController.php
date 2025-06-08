<?php

namespace App\Http\Controllers\DataProdi;

use App\Http\Controllers\Controller;
use App\Models\Kpta\KonsentrasiProdiPd;
use App\Models\Pdrd\PesertaDidik;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$data = PesertaDidik::with('register_pd')->where('soft_delete',0)->get();
        //        $data = PesertaDidik::with('register_pd')->where('soft_delete',0)->get();
        return view('sdm.mahasiswa.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function table(Request $request)
    {
        $query = DB::table('pdrd.peserta_didik AS t1')
            ->join('pdrd.reg_pd AS t2', 't2.id_pd', '=', 't1.id_pd')
            ->leftJoin(DB::RAW("(
                SELECT t01.id_reg_pd, t01.id_smt, t01.ips, t01.ipk, t01.sks_semester, (CASE WHEN t01.total_sks IS NULL THEN NULL ELSE CONCAT(t01.total_sks,' sks') END) AS total_sks, t01.id_stat_mhs FROM pdrd.keaktifan_pd AS t01
                JOIN (
                    SELECT max(id_smt) AS smt, id_reg_pd
                    FROM pdrd.keaktifan_pd
                    WHERE soft_delete=0
                    GROUP BY id_reg_pd
                ) AS t02 ON t02.smt = t01.id_smt AND t01.id_reg_pd=t02.id_reg_pd
            ) AS t8"), 't8.id_reg_pd', '=', 't2.id_reg_pd')
            ->join('pdrd.sms AS t3', 't3.id_sms', '=', 't2.id_sms')
            ->join('ref.jenjang_pendidikan AS t4', 't4.id_jenj_didik', '=', 't3.id_jenj_didik')
            ->leftjoin('ref.jenis_keluar AS t5', 't5.id_jns_keluar', '=', 't2.id_jns_keluar')
            ->join('ref.status_mahasiswa AS t6', 't6.id_stat_mhs', '=', 't1.id_stat_mhs')
            ->join('ref.semester AS t7', 't7.id_smt', '=', 't2.id_smt')
            ->select(
                DB::RAW("row_number() OVER (ORDER BY t2.nim, t1.nm_pd) AS rownum"),
                't7.id_thn_ajaran',
                't1.id_pd',
                't1.last_sync',
                't1.nm_pd',
                't2.nim',
                DB::RAW("CONCAT(t3.nm_lemb,' (',t4.nm_jenj_didik,')') AS prodi"),
                't2.id_jns_keluar',
                't5.ket_keluar',
                't1.id_stat_mhs',
                't6.nm_stat_mhs',
                't8.ipk',
                't8.total_sks'
            )->where('t1.soft_delete', 0);
        if (in_array(session()->get('login.peran.id_peran'), [6, 3000])) {
            $query->where('t3.id_sms', session()->get('login.peran.id_organisasi'));
        } elseif (in_array(session()->get('login.peran.id_peran'), [106])) {
            $query->where('t3.id_induk_sms', session()->get('login.peran.id_organisasi'));
        }
        return DataTables::of($query)
            ->filterColumn('prodi', function ($query, $keyword) {
                $sql = "CONCAT(t3.nm_lemb,' (',t4.nm_jenj_didik,')') like ?";
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addColumn('status', function ($aksi) {
                if ($aksi->id_stat_mhs == 'N') {
                    if (is_null($aksi->id_jns_keluar)) {
                        return 'Non Aktif';
                    } else {
                        return $aksi->ket_keluar;
                    }
                } else {
                    return $aksi->nm_stat_mhs;
                }
            })
            ->addColumn('sync', function ($sync) {
                return '<span class="badge badge-info">Terakhir sync ' . tglWaktuIndonesia($sync->last_sync) . '</span>';
            })
            ->addColumn('aksi', function ($aksi) {
                return buttonShow('sdm.mahasiswa.detail', Crypt::encrypt($aksi->id_pd), 'Detail Mahasiswa');
            })
            ->rawColumns(['status', 'sync', 'aksi'])
            ->toJson();
    }

    public function show($id, PesertaDidik $pesertaDidik)
    {
        $id_mhs = Crypt::decrypt($id);
        $profil = $pesertaDidik->id_detail_mahasiswa($id_mhs);
        $data_konsentrasi_pd = KonsentrasiProdiPd::where('id_pd', $profil->id_pd)
            ->where('konsentrasi_prodi.soft_delete', 0)
            ->join('kpta.konsentrasi_prodi', 'konsentrasi_prodi.id_konsentrasi_prodi', '=', 'konsentrasi_prodi_pd.id_konsentrasi_prodi')
            ->first();
        return view('sdm.mahasiswa.detail', compact('profil', 'data_konsentrasi_pd'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
