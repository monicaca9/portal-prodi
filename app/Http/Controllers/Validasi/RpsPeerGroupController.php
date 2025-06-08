<?php

namespace App\Http\Controllers\Validasi;

use App\Http\Controllers\Controller;
use App\Models\Pdrd\Matkul;
use App\Models\Manajemen\Rps;
use App\Models\Manajemen\Cpl;
use App\Models\Manajemen\CplMk;
use App\Models\Manajemen\CpMatkul;
use App\Models\Manajemen\DaftarPustaka;
use App\Models\Validasi\AjuanRps;
use App\Models\Validasi\RincianAjuanRps;
use App\Models\Validasi\VerAjuanRps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RpsPeerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $prodi = GetProdiIndividu();
        $cari_peer_group = DB::table('manajemen.peer_group AS tpeer')
            ->join('manajemen.ang_peer_group AS ang','ang.id_peer_group','=','tpeer.id_peer_group')
            ->select('tpeer.id_peer_group','tpeer.nm_peer_group','ang.id_ang_peer_group','ang.a_ketua','ang.a_aktif')
            ->where('ang.soft_delete',0)->where('tpeer.soft_delete',0)
            ->where('tpeer.a_aktif',1)->where('ang.a_aktif',1)
            ->where('ang.id_sdm',auth()->user()->id_sdm_pengguna)
            ->get();
        if ($request->has('status')) {
            $status = $request->status;
            if ($status=='Ditolak') {
                $status_periksa = ['T','C'];
                $status_ajuan = 1;
            } elseif ($status=='Disetujui') {
                $status_periksa=['Y'];
                $status_ajuan = 1;
            } else {
                $status_periksa = ['L'];
                $status_ajuan = 1;
            }
        } else {
            $status = 'Diajukan';
            $status_periksa = ['N'];
            $status_ajuan = 1;
        }
                // dd($data);
                $data = AjuanRps::join('validasi.ver_ajuan_rps as ver', function($join){
                    $join->on('ver.id_ajuan_rps', '=', 'validasi.ajuan_rps.id_ajuan_rps')
                    ->where('ver.soft_delete', '=', 0)
                    ->where("ver.level_ver", "=", 1);
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
                // 'DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan',
                'mk.nm_mk')
                ->get();
                // dd($data);

        return view('validasi.rps_peer_group.index',compact('data','prodi','cari_peer_group','status','status_periksa','status_ajuan'));
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
                'validasi.ajuan_rps.metode',
                'tsdm.nm_sdm',
                'tprodi.nm_lemb',
                // 'DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan',
                'mk.nm_mk')
                ->first();

                // dd($data);
        if (is_null($data->wkt_mulai_ver)) {
            $ver->id_role_pengguna  = session()->get('login.peran.id_role_pengguna');
            $ver->wkt_mulai_ver     = currDateTime();
            $ver->last_update       = currDateTime();
            $ver->id_updater        = getIDUser();
            $ver->save();
        }
        $dokumen = DB::SELECT("
            SELECT list.id_dok_ajuan_rps, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_rps AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_ajuan_rps='".$data->id_ajuan_rps."'
        ");
        $mk = Matkul::find($data->id_mk);
        $cpl = Cpl::pluck('nm_cpl','id_cpl');
        $cpl_mk = CplMk::where('id_mk', $data->id_mk)
        ->with('Cpl')
        ->get();
        $cpmk = Cpmatkul::where('id_mk', $data->id_mk)->get();
        $dapusmk = DaftarPustaka::where('id_mk', $data->id_mk)->get();
        // dd($mk);
        $ajuan = AjuanRps::find($data->id_ajuan_rps);
        $detail = RincianAjuanRps::where('id_ajuan_rps',$ajuan->id_ajuan_rps)->orderBy('minggu_ke_baru','ASC')->get();
        return view('validasi.rps_peer_group.detail',compact('data','ver','dokumen','prodi','mk','ajuan','detail','cpl','cpl_mk','cpmk','dapusmk'));
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
        $data = VerAjuanRps::find($id_ver);
        $data->fill($data->prepare($input))->save();
        $ajuan = AjuanRps::find($data->id_ajuan_rps);

        if ($input['status_periksa']=='Y' && $data->level_ver==1) {
            $ver = new VerAjuanRps();
            $ver->fill($ver->prepare([
                'id_ajuan_rps'          => $data->id_ajuan_rps,
                'level_ver'             => 2
            ]))->save();
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
