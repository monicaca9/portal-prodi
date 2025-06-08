<?php

namespace App\Http\Controllers\PelaksanaanPendidikan;

use App\Http\Controllers\Controller;
use App\Models\Dok\DokAjuanRps;
use App\Models\Pdrd\Matkul;
use App\Models\Pdrd\Sdm;
use App\Models\Ref\JenisDokumen;
use App\Models\Validasi\AjuanRps;
use App\Models\Validasi\RincianAjuanRps;
use App\Models\Validasi\VerAjuanRps;
use App\Models\Manajemen\DaftarPustaka;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DokumenTrait;

class RpsController extends Controller
{
    use DokumenTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        // dd($id);
        $id_mk = Crypt::decrypt($id);
        $detail_dosen = Sdm::get_data_user();
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
        $ajuan = AjuanRps::where('id_mk',$mk->id_mk)
            ->where('id_sdm',$detail_dosen->id_sdm)->where('soft_delete',0)
            ->orderBy('wkt_ajuan','DESC')
            ->get();
        return view('portofolio_dosen.pengajaran.rps.index',compact('data','detail_dosen','mk','ajuan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id, Sdm $sdm)
    {
        $id_mk = Crypt::decrypt($id);
        $mk = Matkul::find($id_mk);
        $detail_dosen = $sdm->get_data_user();
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id,$id_rps)
    {
        $id_mk = Crypt::decrypt($id);        
        $id_ajuan_rps = Crypt::decrypt($id_rps);
        $mk = Matkul::find($id_mk);
        $ajuan = AjuanRps::find($id_ajuan_rps);
        $input = $request->all();
        // dd($input);    
        $ajuan->fill($ajuan->prepare($input))->save();
        return redirect()->back();
    }

    public function store_dokumen(Request $request, $id, $id_rps)
    {
        $this->validate($request, [
            'nm_dok'        => 'string|max:60',
            'ket_dok'       => 'string|max:200|nullable'
        ]);
        $input = $request->all();
        if (is_null(@$input['url']) && is_null(@$input['file_dok'])) {
            alert()->error('File/Url harus terisi')->persistent('OK');
        }
        $id_mk = Crypt::decrypt($id);
        $id_ajuan_rps = Crypt::decrypt($id_rps);
        $mk = Matkul::find($id_mk);
        $ajuan = AjuanRps::find($id_ajuan_rps);
        $file = $request->file('file_dok');
        if (!is_null($file)) {
            $ext = $file->getClientOriginalExtension();
            if ($ext == 'pdf') {
                $data_dokumen = $this->simpan_dokumen([
                    'url' => $request->url,
                    'file' => $file,
                    'nm_dok' => $request->nm_dok,
                    'id_jns_dok' => $request->id_jns_dok,
                    'ket_dok' => $request->ket_dok
                ]);

                $simpan_dok = new DokAjuanRps();
                $simpan_dok->fill($simpan_dok->prepare([
                    'id_dok'            => $data_dokumen,
                    'id_ajuan_rps'      => $ajuan->id_ajuan_rps,
                ]))->save();

                alert()->success('Data Dokumen Pendukung lainnya berhasil disimpan')->persistent('OK');
            } else {
                alert()->error('Dokumen Pendukung harus dalam format .pdf')->persistent('OK');
            }
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function store_permanen(Request $request, $id, $id_rps)
    {
        $id_mk = Crypt::decrypt($id);
        $id_ajuan_rps = Crypt::decrypt($id_rps);
        $mk = Matkul::find($id_mk);
        $ajuan = AjuanRps::find($id_ajuan_rps);
        $ajuan->fill($ajuan->prepare([
            '_method'           => 'PUT',
            'wkt_ajuan'         => currDateTime(),
            'stat_ajuan'        => 1,
        ]))->save();

        $cari_ver = VerAjuanRps::where('id_ajuan_rps',$ajuan->id_ajuan_rps)->where('soft_delete',0)->orderBy('verifikasi_ke','DESC')->first();
        if (is_null($cari_ver) or $cari_ver->status_periksa['T']) {
            $simpan_ver = new VerAjuanRps();
            $simpan_ver->fill($simpan_ver->prepare([
                'id_ajuan_rps' => $ajuan->id_ajuan_rps,
            ]))->save();
        } else {
            if (!in_array($cari_ver->status_periksa,['N','Y'])) {
                $simpan_ver = new VerAjuanRps();
                $simpan_ver->fill($simpan_ver->prepare([
                    'id_ajuan_rps' => $ajuan->id_ajuan_rps,
                    'verifikasi_ke'=> (($cari_ver->verifikasi_ke)+1)
                ]))->save();
            }
        }
        alert()->success('Ajuan RPS berhasil diajukan untuk diverifikasi')->persistent('OK');
        return redirect(route('pelaksanaan_pendidikan.pengajaran.show',Crypt::encrypt($id_mk)));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $id_rps, Sdm $sdm)
    {
        $id_mk = Crypt::decrypt($id);
        $id_ajuan_rps = Crypt::decrypt($id_rps);
        $mk = Matkul::find($id_mk);
        $detail_dosen = $sdm->get_data_user();
        $ajuan = AjuanRps::find($id_ajuan_rps);
        $cek_stat = VerAjuanRps::where('id_ajuan_rps', $ajuan->id_ajuan_rps)->orderBy('level_ver', 'DESC')->first();
        $detail = RincianAjuanRps::where('id_ajuan_rps',$ajuan->id_ajuan_rps)->orderBy('minggu_ke_baru','ASC')->get();
        // dd($detail); 
        $jenis_dok = JenisDokumen::whereNull('expired_date')->orderBy('nm_jns_dok','ASC')->pluck('nm_jns_dok','id_jns_dok')->toArray();
        $dokumen = DB::SELECT("
            SELECT list.id_dok_ajuan_rps, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_rps AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_ajuan_rps='".$ajuan->id_ajuan_rps."'
        ");
        return view('portofolio_dosen.pengajaran.rps.detail',compact('mk','detail_dosen','ajuan','detail','dokumen','jenis_dok','cek_stat'));
    }

    public function show_rincian($id, $id_rps, $id_rincian, Sdm $sdm)
    {
        $id_mk = Crypt::decrypt($id);
        $id_ajuan_rps = Crypt::decrypt($id_rps);
        $id_rincian_ajuan_rps = Crypt::decrypt($id_rincian);
        $mk = Matkul::find($id_mk);
        $detail_dosen = $sdm->get_data_user();
        $ajuan = AjuanRps::find($id_ajuan_rps);
        $detail = RincianAjuanRps::find($id_rincian_ajuan_rps);
        $dapusmk = DaftarPustaka::where('id_mk', $id_mk)->get();
        // dd($dapusmk);        

        return view('__partial.form.edit',[
            'judul_halaman' => 'Edit Rincian',
            'route'         => 'pelaksanaan_pendidikan.pengajaran.rps.update_rincian',
            'backLink'      => 'pelaksanaan_pendidikan.pengajaran.rps.detail',
            'form'          => 'portofolio_dosen.pengajaran.rps.rincian',
            'data'          => $detail,
            'id'            => $id_rincian_ajuan_rps,
            'detail_dosen'  => $detail_dosen,
            'ajuan'         => $ajuan,
            'mk'            => $mk,
            'dapusmk'            => $dapusmk,
            'param_form'    => [$id_mk,$id_ajuan_rps]
        ]);
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


    public function update_rincian($id, $id_rps, $id_rincian, Request $request)
    {
        $id_mk = Crypt::decrypt($id);
        $id_ajuan_rps = Crypt::decrypt($id_rps);
        $id_rincian_ajuan_rps = Crypt::decrypt($id_rincian);
        $mk = Matkul::find($id_mk);
        $ajuan = AjuanRps::find($id_ajuan_rps);
        $detail = RincianAjuanRps::find($id_rincian_ajuan_rps);
        $input = $request->all();
        // dd($input);
        $detail->fill($detail->prepare($input))->save();
        return redirect()->route('pelaksanaan_pendidikan.pengajaran.rps.detail',[Crypt::encrypt($mk->id_mk),Crypt::encrypt($ajuan->id_ajuan_rps)]);
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
