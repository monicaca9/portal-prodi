<?php

namespace App\Http\Controllers\Validasi;

use App\Http\Controllers\Controller;
use App\Models\Beasiswa\PeriodeBeasiswa;
use App\Models\Beasiswa\SyaratBeasiswa;
use App\Models\Beasiswa\VerDaftarBeasiswa;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PengajuanBeasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list_periode = PeriodeBeasiswa::where('soft_delete',0)->where('wkt_mulai','<=',currDateTime())
            ->pluck('nm_periode_beasiswa','id_periode_beasiswa')->toArray();
        if($request->isMethod('POST')) {
            if($request->periode=='') {
                session()->forget('periode');
            } else {
                session()->put('periode',$request->periode);
            }
        }
        if (session()->has('periode')) {
            $id_periode = session()->get('periode');
            $periode = PeriodeBeasiswa::find($id_periode);
            if (session()->get('login.peran.id_peran')==106) {
                $tingkat=2;
            } elseif (session()->get('login.peran.id_peran')==3) {
                $tingkat=3;
            } else {
                $tingkat=1;
            }

            if ($request->has('status')) {
                $status = $request->status;
                if ($status=='Ditolak') {
                    $status_periksa = ['T','L','C'];
                } elseif ($status=='Disetujui') {
                    $status_periksa=['Y'];
                } else {
                    $status_periksa = ['N'];
                }
            } else {
                $status = 'Diajukan';
                $status_periksa = ['N'];
            }
            $query = "SELECT
                    ver.id_ver_daftar_beasiswa,
                    ver.nm_verifikator,
                    ver.wkt_mulai_ver,
                    ver.wkt_selesai_ver,
                    ver.status_periksa,
                    ver.ket_periksa,
                    ver.level_ver,
                    dftr.waktu_diajukan,
                    dftr.stat_ajuan,
                    trpd.nim,
                    pd.nm_pd,
                    CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS homebase,
                    tpeng.nm_pengguna,
                    tper.nm_periode_beasiswa,
	                DATE_PART('day', NOW() - dftr.waktu_diajukan::timestamp) AS umur_ajuan
                FROM beasiswa.ver_daftar_beasiswa AS ver
                JOIN beasiswa.pendaftar_beasiswa AS dftr ON dftr.id_daftar_beasiswa = ver.id_daftar_beasiswa
                    AND dftr.soft_delete=0
                JOIN beasiswa.periode_beasiswa AS tper ON tper.id_periode_beasiswa = dftr.id_periode_beasiswa
                JOIN pdrd.reg_pd AS trpd ON trpd.id_reg_pd = dftr.id_reg_pd AND trpd.soft_delete=0
                JOIN pdrd.peserta_didik AS pd ON pd.id_pd = trpd.id_pd AND pd.soft_delete=0
                JOIN pdrd.sms AS tprodi ON tprodi.id_sms = trpd.id_sms AND tprodi.soft_delete=0
                JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
                LEFT JOIN (
                    SELECT t1.id_role_pengguna, t2.nm_pengguna FROM man_akses.role_pengguna AS t1
                    JOIN man_akses.pengguna AS t2 ON t2.id_pengguna = t1.id_pengguna AND t2.soft_delete=0
                    WHERE t1.soft_delete=0
                ) AS tpeng ON tpeng.id_role_pengguna = ver.id_role_pengguna
                WHERE ver.soft_delete=0
                AND ver.status_periksa IN ('".implode("','",$status_periksa)."')
                AND ver.level_ver='".$tingkat."'";
            if ($tingkat==1) {
                $query.= " AND tprodi.id_sms = '".session()->get('login.peran.id_organisasi')."'";
            } elseif($tingkat==2) {
                $query.= " AND tprodi.id_induk_sms = '".session()->get('login.peran.id_organisasi')."'";
            }
            $query .= " ORDER BY dftr.waktu_diajukan ASC";
            $data = DB::SELECT($query);
        } else {
            $id_periode = null;
            $periode = null;
            $status = null;
            $data = null;
        }
        return view('validasi.beasiswa.index',compact('list_periode','id_periode','periode','status','data'));
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
    public function show($id, PesertaDidik $pesertaDidik)
    {
        $id_ver = Crypt::decrypt($id);
        $ver = VerDaftarBeasiswa::find($id_ver);
        $data = collect(DB::SELECT("
                SELECT
                    ver.id_ver_daftar_beasiswa,
                    ver.nm_verifikator,
                    ver.wkt_mulai_ver,
                    ver.wkt_selesai_ver,
                    ver.status_periksa,
                    ver.ket_periksa,
                    ver.level_ver,
                    dftr.id_daftar_beasiswa,
                    dftr.waktu_diajukan,
                    dftr.stat_ajuan,
                    trpd.nim,
                    pd.id_pd,
                    pd.nm_pd,
                    CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS homebase,
                    tpeng.nm_pengguna,
                    tper.id_periode_beasiswa,
                    tper.nm_periode_beasiswa,
	                DATE_PART('day', NOW() - dftr.waktu_diajukan::timestamp) AS umur_ajuan
                FROM beasiswa.ver_daftar_beasiswa AS ver
                JOIN beasiswa.pendaftar_beasiswa AS dftr ON dftr.id_daftar_beasiswa = ver.id_daftar_beasiswa
                    AND dftr.soft_delete=0
                JOIN beasiswa.periode_beasiswa AS tper ON tper.id_periode_beasiswa = dftr.id_periode_beasiswa
                JOIN pdrd.reg_pd AS trpd ON trpd.id_reg_pd = dftr.id_reg_pd AND trpd.soft_delete=0
                JOIN pdrd.peserta_didik AS pd ON pd.id_pd = trpd.id_pd AND pd.soft_delete=0
                JOIN pdrd.sms AS tprodi ON tprodi.id_sms = trpd.id_sms AND tprodi.soft_delete=0
                JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik = tprodi.id_jenj_didik
                LEFT JOIN (
                    SELECT t1.id_role_pengguna, t2.nm_pengguna FROM man_akses.role_pengguna AS t1
                    JOIN man_akses.pengguna AS t2 ON t2.id_pengguna = t1.id_pengguna AND t2.soft_delete=0
                    WHERE t1.soft_delete=0
                ) AS tpeng ON tpeng.id_role_pengguna = ver.id_role_pengguna
                WHERE ver.soft_delete=0
                AND ver.id_ver_daftar_beasiswa='".$id_ver."'
                ORDER BY dftr.waktu_diajukan ASC
        "))->first();
        $periode = PeriodeBeasiswa::find($data->id_periode_beasiswa);
        $profil = $pesertaDidik->id_detail_mahasiswa($data->id_pd);
        $syarat = SyaratBeasiswa::where('id_periode_beasiswa',$data->id_periode_beasiswa)->where('soft_delete',0)->orderBy('nm_syarat','ASC')->get();
        if (is_null($data->wkt_mulai_ver)) {
            $ver->id_role_pengguna  = session()->get('login.peran.id_role_pengguna');
            $ver->wkt_mulai_ver     = currDateTime();
            $ver->last_update       = currDateTime();
            $ver->id_updater        = getIDUser();
            $ver->save();
        }
        return view('validasi.beasiswa.detail',compact('data','periode','profil','syarat'));
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
        $this->validate($request, [
            'ket_periksa'   => 'required|string|max:500'
        ]);
        $id_ver = Crypt::decrypt($id);
        $input = $request->all();
        $input['wkt_selesai_ver']   = currDateTime();
        $data = VerDaftarBeasiswa::find($id_ver);
        $data->fill($data->prepare($input))->save();

        if ($input['status_periksa']=='Y' && $data->level_ver==2) {
            $ver = new VerDaftarBeasiswa();
            $ver->fill($ver->prepare([
                'id_daftar_beasiswa'    => $data->id_daftar_beasiswa,
                'level_ver'             => 3
            ]))->save();
        }
        alert()->success('Berhasil memvalidasi ajuan')->persistent('OK');
        return redirect()->back();
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
