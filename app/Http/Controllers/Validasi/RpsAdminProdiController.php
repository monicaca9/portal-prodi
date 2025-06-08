<?php

namespace App\Http\Controllers\Validasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manajemen\MatkulRps;
use App\Models\Manajemen\RincianRps;
use App\Models\Manajemen\Rps;
use App\Models\Manajemen\Pengesahan;
use App\Models\Pdrd\Matkul;
use App\Models\Validasi\AjuanRps;
use App\Models\Validasi\RincianAjuanRps;
use App\Models\Validasi\VerAjuanRps;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RpsAdminProdiController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $prodi = GetProdiIndividu();
        $cari_role = DB::table('man_akses.role_pengguna AS rolepengguna')
            ->join('man_akses.pengguna as pengguna', 'pengguna.id_pengguna','=', 'rolepengguna.id_pengguna')
            ->where('pengguna.id_sdm_pengguna',auth()->user()->id_sdm_pengguna)
            ->where('rolepengguna.id_peran',6)
            ->get();

            if ($request->has('status')) {
                $status = $request->status;
                if ($status=='Disahkan') {
                    $status_periksa = ['Y'];
                    $status_ajuan = 2;
                } 
            } else {
                $status = 'Diajukan';
                $status_periksa = ['Y'];
                $status_ajuan = 1;
            }

            $data = AjuanRps::join('validasi.ver_ajuan_rps as ver', function($join){
                $join->on('ver.id_ajuan_rps', '=', 'validasi.ajuan_rps.id_ajuan_rps')
                ->where('ver.soft_delete', '=', 0)
                ->where("ver.level_ver", "=", 2);
            })
                ->join('pdrd.sdm as tsdm', 'tsdm.id_sdm','=','validasi.ajuan_rps.id_sdm')
                ->join('pdrd.matkul as mk', 'mk.id_mk','=','validasi.ajuan_rps.id_mk')
                ->join('pdrd.reg_ptk as tr', 'tr.id_sdm','=','tsdm.id_sdm')
                ->join('pdrd.sms as tprodi','tprodi.id_sms','=','tr.id_sms' )
                ->where('tprodi.id_sms','=',$prodi->id_sms)
                ->where('validasi.ajuan_rps.stat_ajuan','=',$status_ajuan)
                ->where('ver.status_periksa','=',$status_periksa)
                ->select('ver.id_ver_ajuan',
                'ver.nm_verifikator',
                'ver.wkt_mulai_ver',
                'ver.wkt_selesai_ver',
                'ver.status_periksa',
                'ver.ket_periksa',
                'ver.level_ver',
                'validasi.ajuan_rps.id_ajuan_rps',
                'validasi.ajuan_rps.wkt_ajuan',
                'validasi.ajuan_rps.jns_ajuan',
                'validasi.ajuan_rps.stat_ajuan',
                'tsdm.nm_sdm',
                'tprodi.nm_lemb',
                'mk.id_mk',
                'mk.nm_mk',
                'mk.jns_mk',
                'mk.sks_mk')
                // 'DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan',
                ->get();
                    // dd($cari_role);

        return view('validasi.rps_admin_prodi.index',compact('data','prodi','cari_role','status'));
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
    public function show($id)
    {
        $prodi = GetProdiIndividu();
        $id_ver_ajuan = Crypt::decrypt($id);
        $ver = VerAjuanRps::find($id_ver_ajuan);
        $xx = collect(DB::SELECT("
                SELECT
                        ver.id_ver_ajuan,
                        ver.nm_verifikator,
                        ver.wkt_mulai_ver,
                        ver.wkt_selesai_ver,
                        ver.status_periksa,
                        ver.ket_periksa,
                        ver.level_ver,
                        ajuan.id_ajuan_rps,
                        ajuan.id_mk,
                        ajuan.wkt_ajuan,
                        ajuan.jns_ajuan,
                        ajuan.stat_ajuan,
                        CONCAT(tsdm.nm_sdm,' (',tsdm.nidn,')') AS nm_dosen,
                        CONCAT(tprodi.nm_lemb,' (',tjenj.nm_jenj_didik,')') AS homebase,
                        tpeng.nm_pengguna,
                        DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan,
                        mk.nm_mk
                    FROM validasi.ajuan_rps AS ajuan
                    JOIN validasi.ver_ajuan_rps AS ver ON ver.id_ajuan_rps=ajuan.id_ajuan_rps
                        AND ver.soft_delete=0 AND ver.level_ver='1'
                    JOIN pdrd.sdm AS tsdm ON tsdm.id_sdm = ajuan.id_sdm
                    LEFT JOIN (
                        SELECT t1.id_role_pengguna, t2.nm_pengguna FROM man_akses.role_pengguna AS t1
                      JOIN man_akses.pengguna AS t2 ON t2.id_pengguna = t1.id_pengguna AND t2.soft_delete=0
                      WHERE t1.soft_delete=0
                    ) AS tpeng ON tpeng.id_role_pengguna = ver.id_role_pengguna
                    JOIN pdrd.matkul AS mk ON mk.id_mk=ajuan.id_mk
                    JOIN pdrd.reg_ptk AS tr ON tr.id_sdm = tsdm.id_sdm AND tr.soft_delete=0
                        AND tr.id_jns_keluar IS NULL AND (tr.tgl_ptk_keluar IS NULL OR tr.tgl_ptk_keluar<=NOW())
                    JOIN pdrd.keaktifan_ptk AS taktif ON taktif.id_reg_ptk = tr.id_reg_ptk AND taktif.soft_delete=0
                        AND taktif.id_thn_ajaran=(date_part('year',NOW())-1)
                    JOIN pdrd.sms AS tprodi ON tprodi.id_sms = tr.id_sms AND tprodi.soft_delete=0
                    JOIN ref.jenjang_pendidikan AS tjenj ON tjenj.id_jenj_didik=tprodi.id_jenj_didik
                    WHERE ajuan.soft_delete=0
                    AND ver.id_ver_ajuan ='".$id_ver_ajuan."'
                    ORDER BY ajuan.wkt_ajuan ASC
        "))->first();

        $data = AjuanRps::join('validasi.ver_ajuan_rps as ver', 'ver.id_ajuan_rps','=','validasi.ajuan_rps.id_ajuan_rps')
                ->join('pdrd.sdm as tsdm', 'tsdm.id_sdm','=','validasi.ajuan_rps.id_sdm')
                ->join('pdrd.matkul as mk', 'mk.id_mk','=','validasi.ajuan_rps.id_mk')
                ->join('pdrd.reg_ptk as tr', 'tr.id_sdm','=','tsdm.id_sdm')
                ->join('pdrd.sms as tprodi','tprodi.id_sms','=','tr.id_sms' )
                ->where('ver.id_ver_ajuan','=',$id_ver_ajuan)
                ->select('ver.id_ver_ajuan',
                'ver.nm_verifikator',
                'ver.wkt_mulai_ver',
                'ver.wkt_selesai_ver',
                'ver.status_periksa',
                'ver.ket_periksa',
                'ver.level_ver',
                'validasi.ajuan_rps.id_ajuan_rps',
                'validasi.ajuan_rps.id_mk',
                'validasi.ajuan_rps.wkt_ajuan',
                'validasi.ajuan_rps.jns_ajuan',
                'validasi.ajuan_rps.stat_ajuan',
                'tsdm.nm_sdm',
                'tprodi.nm_lemb',
                // 'DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan',
                'mk.nm_mk')
                ->first();

                // dd($data);
        
        $dokumen = DB::SELECT("
            SELECT list.id_dok_ajuan_rps, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_rps AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_ajuan_rps='".$data->id_ajuan_rps."'
        ");
        $mk = Matkul::find($data->id_mk);
        // dd($mk);
        $ajuan = AjuanRps::find($data->id_ajuan_rps);
        $detail = RincianAjuanRps::where('id_ajuan_rps',$ajuan->id_ajuan_rps)->orderBy('minggu_ke_baru','ASC')->get();
        return view('validasi.rps_tpmps.detail',compact('data','ver','dokumen','prodi','mk','ajuan','detail'));
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
    public function sah(Request $request, $id)
    {
        
        $id_ver = Crypt::decrypt($id);
        $input = $request->all();
        $input['wkt_selesai_ver']   = currDateTime();
        $data = VerAjuanRps::find($id_ver);
        $data->fill($data->prepare($input))->save();
        $ajuan = AjuanRps::find($data->id_ajuan_rps);
        $pengesahan = Pengesahan::where('id_sdm',auth()->user()->id_sdm_pengguna)->first();
        $pengesahan_mkrps = MatkulRps::join('validasi.ajuan_rps as ajuan','ajuan.id_mk','=','matkul_rps.id_mk')
        ->where('ajuan.id_mk',$ajuan->id_mk)
        ->first();
        // dd($pengesahan_mkrps);

        if ($data->level_ver==2) {
            $ver = new VerAjuanRps();
            $ver->fill($ver->prepare([
                'id_ajuan_rps'          => $data->id_ajuan_rps,
                'id_role_pengguna'  => session()->get('login.peran.id_role_pengguna'),  
                'nm_verifikator'        => auth()->user()->nm_pengguna,
                'level_ver'             => 3,
                'status_periksa' => 'Y',
                'ket_periksa' => 'Sah',
                'wkt_selesai_ver'   => currDateTime()
            ]))->save();

            $ajuan->fill($ajuan->prepare([
                '_method'   => 'PUT',
                'stat_ajuan'=> 2,

            ]))->save();
            
            $pengesahan_mkrps->fill($pengesahan_mkrps->prepare([
                'method' => 'PUT',
                'id_pengesahan' => $pengesahan->id_pengesahan,
                'a_aktif' => 1,
                'wkt_aktif' => currDateTime()
            ]))->save();
            // dd($pengesahan_mkrps);
        } elseif (in_array($input['status_periksa'],['T','C']) && $data->level_ver==1) {
            $ajuan->fill($ajuan->prepare([
                '_method'   => 'PUT',
                'stat_ajuan'=> 3
            ]));
        } elseif ($input['status_periksa']==['L'] && $data->level_ver==1) {
            $ajuan->fill($ajuan->prepare([
                '_method'   => 'PUT',
                'stat_ajuan'=> 4
            ]));
        }

        alert()->success('Berhasil mengesahkan RPS')->persistent('OK');
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
