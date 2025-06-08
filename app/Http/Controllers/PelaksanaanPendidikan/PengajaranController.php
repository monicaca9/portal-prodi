<?php

namespace App\Http\Controllers\PelaksanaanPendidikan;

use App\Http\Controllers\Controller;
use App\Models\Manajemen\RincianRps;
use App\Models\Manajemen\Cpl;
use App\Models\Manajemen\CplMk;
use App\Models\Manajemen\Cpmatkul;
use App\Models\Manajemen\DaftarPustaka;
use App\Models\Manajemen\RpsMingguMk;
use App\Models\Manajemen\MatkulRps;
use App\Models\Manajemen\Rps;
use App\Models\Pdrd\Matkul;
use App\Models\Pdrd\Sdm;
use App\Models\Pdrd\PesertaDidik;
use App\Models\Pdrd\RegPtk;
use App\Models\Pdrd\KelasKuliah;
use App\Models\Pdrd\AktAjarDosen;
use App\Models\ManAkses\RolePengguna;
use App\Models\Validasi\AjuanRps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PengajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Sdm $sdm)
    {
        $detail_dosen = $sdm->get_data_user();
        
        $detail_smt = DB::table("pdrd.akt_ajar_dosen AS a")
            ->join('pdrd.kelas_kuliah AS k','a.id_kls','=','k.id_kls')
            ->join('ref.semester AS s','s.id_smt','=','k.id_smt')
            ->select('s.id_smt','s.nm_smt')
            ->where('a.soft_delete',0)->where('k.soft_delete',0)
            ->where('a.id_reg_ptk',$detail_dosen->id_reg_ptk)
            ->groupBy('s.id_smt','s.nm_smt')->orderBy('s.id_smt','DESC')
            ->pluck('s.nm_smt','s.id_smt')->toArray();
        if ($request->has('periode_smt')) {
            $smt = $request->periode_smt;
        } else {
            $smt = array_key_first($detail_smt);
        }
        
        // dd($request);
        $data = DB::SELECT("
            SELECT m.id_mk, m.nm_mk, m.kode_mk, m.jns_mk, m.kel_mk, m.sks_mk, kk.nm_kls FROM pdrd.matkul AS m
            JOIN pdrd.kelas_kuliah AS kk ON kk.id_mk = m.id_mk AND kk.soft_delete=0
            JOIN pdrd.akt_ajar_dosen AS aa ON aa.id_kls = kk.id_kls AND aa.soft_delete=0
            WHERE kk.id_smt = '".$smt."' AND aa.id_reg_ptk='".$detail_dosen->id_reg_ptk."'
            ORDER BY m.nm_mk ASC
        ");

        // dd($data);
        return view('portofolio_dosen.pengajaran.index',compact('data','detail_smt','detail_dosen','smt'));
    }

    public function rps_mahasiswa(PesertaDidik $pd){
        $rps = MatkulRps::join('manajemen.rps as rps', 'rps.id_rps','=','manajemen.matkul_rps.id_rps')
        ->join('pdrd.matkul as matkul', 'matkul.id_mk','=','manajemen.matkul_rps.id_mk')
        ->join('pdrd.sdm as sdm','sdm.id_sdm','=','rps.id_sdm')
        ->where('a_aktif', 1)
        ->get();

        // dd($rps);
        return view('portofolio_dosen.pengajaran.rpsmahasiswa',compact('rps'));
    }

    public function rps_mahasiswa_detail($id){
        $id_mk = Crypt::decrypt($id);
        $rps = MatkulRps::join('manajemen.rps as rps', 'rps.id_rps','=','manajemen.matkul_rps.id_rps')
        ->join('pdrd.matkul as matkul', 'matkul.id_mk','=','manajemen.matkul_rps.id_mk')
        ->join('pdrd.sdm as sdm','sdm.id_sdm','=','rps.id_sdm')
        ->get();
        $data = collect(DB::SELECT("
            SELECT tr.id_rps,tr.tujuan_umum, tr.daftar_pustaka, tr.evaluasi, tr.bahan_ajar, tp.nm_lengkap, tp.kota, CONCAT(tsdm.nm_sdm,' (',tsdm.nidn,')') AS dosen
            FROM manajemen.matkul_rps AS m_rps
            JOIN manajemen.rps AS tr ON tr.id_rps = m_rps.id_rps AND tr.soft_delete=0
            JOIN pdrd.sdm AS tsdm ON tsdm.id_sdm = tr.id_sdm AND tsdm.soft_delete=0
            LEFT JOIN manajemen.pengesahan AS tp ON tp.id_pengesahan = m_rps.id_pengesahan AND tp.soft_delete=0
            WHERE m_rps.soft_delete=0 AND m_rps.a_aktif=1
            AND m_rps.id_mk = '".$id_mk."'
        "))->first();
        $mk = Matkul::find($id_mk);
        // $ajuan = AjuanRps::where('id_mk',$mk->id_mk)
        //     ->where('id_sdm',$detail_dosen->id_sdm)->where('soft_delete',0)
        //     ->orderBy('wkt_ajuan','DESC')
        //     ->get();

        $cpl = Cpl::pluck('nm_cpl','id_cpl');
        $cpl_mk = CplMk::where('id_mk', $mk->id_mk)
        ->with('Cpl')
        ->get();
        $cpmk = Cpmatkul::where('id_mk', $mk->id_mk)->get();
        $dapusmk = DaftarPustaka::where('id_mk', $mk->id_mk)->get();

        return view('portofolio_dosen.pengajaran.rps.mahasiswa_detail',compact('rps','mk','cpl','cpl_mk','cpmk','dapusmk','data'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id, Sdm $sdm)
    {
        // dd($id_mk);
        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
        $detail_dosen = $sdm->get_user_data();
        $ajuan = new AjuanRps();
        $ajuan->fill($ajuan->prepare([
            'id_mk'     => $mk->id_mk,
            'id_sdm'    => $detail_dosen->id_sdm,
            'wkt_create'=> currDateTime(),
            'wkt_ajuan' => currDateTime(),
            'stat_ajuan'=> 0,
            'jns_ajuan' => 'B',
        ]))->save();
        for ($minggu=1;$minggu<=16;$minggu++) {
            $rincian_ajuan = new RincianAjuanRps();
            if ($minggu==8) {
                $rincian_ajuan->fill($rincian_ajuan->prepare([
                    'id_ajuan_rps'    => $ajuan->id_ajuan_rps,
                    'minggu_ke_baru'  => $minggu,
                    'pokok_bahasan_baru'=> '<p>UTS</p>'
                ]))->save();
            } elseif ($minggu==16) {
                $rincian_ajuan->fill($rincian_ajuan->prepare([
                    'id_ajuan_rps'    => $ajuan->id_ajuan_rps,
                    'minggu_ke_baru'  => $minggu,
                    'pokok_bahasan_baru'=> '<p>UAS</p>'
                ]))->save();
            } else {
                $rincian_ajuan->fill($rincian_ajuan->prepare([
                    'id_ajuan_rps'    => $ajuan->id_ajuan_rps,
                    'minggu_ke_baru'  => $minggu
                ]))->save();
            }
        }

        alert()->info('Silahkan lengkapi data dibawah ini')->persistent('OK');
        return redirect()->route('pelaksanaan_pendidikan.pengajaran.rps.detail',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($ajuan->id_ajuan_rps)]);
    }

    public function create_rps_minggu(Request $request, $id)
    {

        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
        $rps_minggu_mk = new RpsMingguMk();
        $referensi = DaftarPustaka::where('id_mk',$id_mk)->pluck('daftar_pustaka','id_daftar_pustaka_mk')->toArray();
        // dd($referensi);
        return view('portofolio_dosen.pengajaran.rps.create_rps_minggu',compact('referensi','mk','id_mk'));
    }

    public function store_rps_minggu(Request $request, $id, Sdm $sdm)
    {
        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
        $detail_dosen = $sdm->get_data_user();
        $input = $request->all();
        // dd($input);

        $ajuanRpsId = guid();

        AjuanRps::updateOrInsert([
            'id_ajuan_rps' => $ajuanRpsId,
        ],[
            'id_mk' => $mk->id_mk,
            'id_sdm' => $detail_dosen->id_sdm,
            'wkt_create' => currDateTime(),
            'wkt_ajuan' => currDateTime(),
            'stat_ajuan' => '0',
            'jns_ajuan' => 'B',
            'wkt_update' => currDateTime(),
            'updater_id' => $detail_dosen->id_sdm,
            'tujuan_umum_baru' => $input['sub_cpmk'],
            'bahan_ajar_baru' => $input['bahan_kajian'],
            'tgl_create' => currDateTime(),
            'id_creator' => $detail_dosen->id_sdm,
            'last_update' => currDateTime(),
            'id_updater' => $detail_dosen->id_sdm,
            'soft_delete' => 0,
            'last_sync' => currDateTime()
        ]);

        alert()->success('RPS Berhasil diajukan')->persistent('OK');
        return redirect(route('pelaksanaan_pendidikan.pengajaran.show',Crypt::encrypt($id_mk)));



        
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
    public function show(Sdm $sdm,$id)
    {
        $id_mk = Crypt::decrypt($id);
        $detail_dosen = $sdm->get_data_user();
        $data = collect(DB::SELECT("
            SELECT tr.id_rps,tr.tujuan_umum, tr.daftar_pustaka, tr.evaluasi, tr.bahan_ajar, tp.nm_lengkap, tp.kota, CONCAT(tsdm.nm_sdm,' (',tsdm.nidn,')') AS dosen
            FROM manajemen.matkul_rps AS m_rps
            JOIN manajemen.rps AS tr ON tr.id_rps = m_rps.id_rps AND tr.soft_delete=0
            JOIN pdrd.sdm AS tsdm ON tsdm.id_sdm = tr.id_sdm AND tsdm.soft_delete=0
            LEFT JOIN manajemen.pengesahan AS tp ON tp.id_pengesahan = m_rps.id_pengesahan AND tp.soft_delete=0
            WHERE m_rps.soft_delete=0 AND m_rps.a_aktif=1
            AND m_rps.id_mk = '".$id_mk."'
        "))->first();

        $cek_role = RolePengguna::where('id_role_pengguna', session()->get('login.peran.id_role_pengguna'))->first();
        // dd($cek_role);
        $mk = Matkul::find($id_mk);
        $ajuan = AjuanRps::where('id_mk',$mk->id_mk)
            ->where('id_sdm',$detail_dosen->id_sdm)->where('soft_delete',0)
            ->orderBy('wkt_ajuan','DESC')
            ->get();

        $cpl = Cpl::pluck('nm_cpl','id_cpl');
        $cpl_mk = CplMk::where('id_mk', $mk->id_mk)
        ->with('Cpl')
        ->get();
        $cpmk = Cpmatkul::where('id_mk', $mk->id_mk)->get();
        $dapusmk = DaftarPustaka::where('id_mk', $mk->id_mk)->get();
        return view('portofolio_dosen.pengajaran.rps.index',compact('data','detail_dosen','mk','ajuan','cpl','cpl_mk','cpmk','dapusmk','cek_role'));
    }
    
    public function view_pdf($id){
        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
        $dosen = AjuanRps::where('id_mk', $id_mk)
        ->join('pdrd.sdm as sdm', 'sdm.id_sdm','=', 'validasi.ajuan_rps.id_sdm')
        ->first();
        $cpl = Cpl::pluck('nm_cpl','id_cpl');
        $cpl_mk = CplMk::where('id_mk', $mk->id_mk)
        ->with('Cpl')
        ->get();
        $cpmk = Cpmatkul::where('id_mk', $mk->id_mk)->get();
        $dapusmk = DaftarPustaka::where('id_mk', $mk->id_mk)->get();
        $metode = MatkulRps::join('manajemen.rps as rps','rps.id_rps','=','manajemen.matkul_rps.id_rps')
        ->where('manajemen.matkul_rps.id_mk', $mk->id_mk)
        ->where('manajemen.matkul_rps.a_aktif',1)
        ->select('metode')
        ->first();
        // dd($metode);
        $matkulrps = MatkulRps::join('pdrd.kelas_kuliah as kelas', 'kelas.id_mk','=','manajemen.matkul_rps.id_mk')
        ->join('pdrd.akt_ajar_dosen as akt', 'akt.id_kls','=','kelas.id_kls')
        ->join('pdrd.reg_ptk as reg', 'reg.id_reg_ptk','=','akt.id_reg_ptk')
        ->join('pdrd.sdm as sdm', 'sdm.id_sdm','=','reg.id_sdm')
        ->where('manajemen.matkul_rps.id_mk', $mk->id_mk)
        ->select('nm_sdm')
        ->groupBy('nm_sdm')
        ->get();
        $rpsdata = MatkulRps::join('manajemen.rps as rps','rps.id_rps','=','manajemen.matkul_rps.id_rps')
        ->join('manajemen.rincian_rps as rincian','rincian.id_rps','=','rps.id_rps')
        ->where('manajemen.matkul_rps.id_mk', $mk->id_mk)
        ->select('minggu_ke',
        'tujuan_khusus',
        'pokok_bahasan',
        'referensi',
        'sub_pokok_bahasan',
        'rincian.metode',
        'media',
        'akt_penugasan',
        'bobot')
        ->orderBy('minggu_ke', 'ASC')
        ->get();

        // dd($rpsdata);
        $waktu_aktif = MatkulRps::where('id_mk',$mk->id_mk)->where('a_aktif',1)->first();
        // dd($waktu_aktif);
        // $pdf = App::make('dompdf.wrapper');
        $pdf = Pdf::loadView('portofolio_dosen.pengajaran.rps.pdf',compact('mk','cpl_mk','dosen','cpmk','matkulrps','dapusmk','rpsdata','waktu_aktif','metode'))->setPaper('a4', 'landscape');
        return $pdf->stream();
        // return view('portofolio_dosen.pengajaran.rps.pdf',compact('data'));
    }
    public function store_cpl_mk(Request $request, $id, Sdm $sdm) {
        
        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
        $detail_dosen = $sdm->get_data_user();
        $input = $request->all();
        // dd($input);
        $cplmkid = guid();
        // dd($input);
        CplMk::updateOrInsert([
            'id_cpl_mk' => $cplmkid,
            
        ],[
            'id_cpl' => $input['id_cpl'],
            'id_mk' => $id_mk,
            'last_update' => currDateTime(),
            'id_updater' => $detail_dosen->id_sdm,
            'soft_delete' => 0,
            'last_sync' => currDateTime()
        ]);

        alert()->success('CPL Berhasil Ditambahkan ke MataKuliah')->persistent('OK');
        return redirect()->back();
    }

    public function delete_cpl_mk($id, $id_cplmk){
        $id_mk = Crypt::decrypt($id);
        $id_cplmk = Crypt::decrypt($id_cplmk);
        $data = CplMk::where('id_mk',$id_mk)->where('id_cpl_mk',$id_cplmk)->first();
        // dd($data);
        $data->destroy($id_cplmk);
        alert()->success('CPL berhasil dihapus')->persistent('OK');
        return redirect(route('pelaksanaan_pendidikan.pengajaran.show',Crypt::encrypt($id_mk)));
    }
    public function delete_cpmk($id, $id_cpmk){
        $id_mk = Crypt::decrypt($id);
        $id_cpmk = Crypt::decrypt($id_cpmk);
        $data = CpMatkul::where('id_mk',$id_mk)->where('id_cpmk',$id_cpmk)->first();
        // dd($data);
        $data->destroy($id_cpmk);
        alert()->success('CPMK berhasil dihapus')->persistent('OK');
        return redirect(route('pelaksanaan_pendidikan.pengajaran.show',Crypt::encrypt($id_mk)));
    }
    public function delete_daftar_pustaka_mk($id, $id_dapusmk){
        $id_mk = Crypt::decrypt($id);
        $id_dapusmk = Crypt::decrypt($id_dapusmk);
        $data = DaftarPustaka::where('id_mk',$id_mk)->where('id_daftar_pustaka_mk',$id_dapusmk)->first();
        // dd($data);
        $data->destroy($id_dapusmk);
        alert()->success('Daftar Pustaka berhasil dihapus')->persistent('OK');
        return redirect(route('pelaksanaan_pendidikan.pengajaran.show',Crypt::encrypt($id_mk)));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function store_cpmk(Request $request, $id, Sdm $sdm){
        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
        $detail_dosen = $sdm->get_data_user();
        $input = $request->all();
        $cpmkid = guid();
        // dd($request)['input'];
        Cpmatkul::updateOrInsert([
            'id_cpmk' => $cpmkid,
        ],[
            'id_mk' => $mk->id_mk,
            'cpmk' => $input['cpmk'],
            'id_creator' => $detail_dosen->id_sdm,
            'last_update' => currDateTime(),
            'id_updater' => $detail_dosen->id_sdm,
            'soft_delete' => 0,
            'last_sync' => currDateTime()
        ]);

        alert()->success('CPMK Berhasil Ditambahkan ke MataKuliah')->persistent('OK');
        return redirect()->back();
    }

    public function store_daftar_pustaka_mk(Request $request, $id, Sdm $sdm){
        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
        $detail_dosen = $sdm->get_data_user();
        $input = $request->all();
        $dapusmkid = guid();
        // dd($input);
        DaftarPustaka::updateOrInsert([
            'id_daftar_pustaka_mk' => $dapusmkid,
        ],[
            'id_mk' => $mk->id_mk,
            'penulis' => $input['penulis'],
            'tahun' => $input['tahun'],
            'judul' => $input['judul'],
            'soft_delete' => 0,
            'last_sync' => currDateTime()
        ]);

        alert()->success('Daftar Pustaka Berhasil Ditambahkan ke MataKuliah')->persistent('OK');
        return redirect()->back();
    }


    public function edit_desc_mk($id)
    {
        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
    
        return view('portofolio_dosen.pengajaran.rps.edit',[
            'id'            => $id_mk,
            'mk'          => $mk,
        ]);
    }

    public function update_desc_mk(Request $request, $id){
        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
        $input = $request->all();

        // dd($id_mk);
        Matkul::updateOrInsert([
            'id_mk' => $id_mk,
        ],[
            'desc_mk' => $input['desc_mk']
        ]);

        alert()->success('Deskripsi Mata Kuliah Berhasil Diubah')->persistent('OK');
        return redirect(route('pelaksanaan_pendidikan.pengajaran.show',Crypt::encrypt($id_mk)));
    }

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
