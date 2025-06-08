<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DokumenTrait;
use App\Models\Dokumen\DokAjuanSeminar;
use App\Models\Pdrd\PesertaDidik;
use App\Models\Ref\JenisDokumen;
use App\Models\Kpta\BeritaAcara;
use App\Models\Ref\JenisSeminar;
use App\Models\Validasi\AjuanPdmSeminar;
use App\Models\Validasi\VerAjuanPdmSeminar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RiwayatSeminarController extends Controller
{
    use DokumenTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('status')) {
            $status = $request->status;
            if ($status=='Ditolak') {
                $status_validasi = 3;
            } elseif ($status=='Disetujui') {
                $status_validasi = 2;
            } else {
                $status_validasi = 4;
            }
        } else {
            $status = 'Diajukan';
            $status_validasi = 1;
        }
        $query = "
            SELECT
                ajuan.id_ajuan_pdm_seminar,
                ajuan.id_jns_seminar_lama,
                ajuan.stat_ajuan,
                jns.nm_jns_seminar,
                ajuan.wkt_ajuan,
                valid.wkt_selesai_ver,
                DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan
            FROM validasi.ajuan_pdm_seminar AS ajuan
            JOIN validasi.ver_ajuan_pdm_seminar AS valid ON valid.id_ajuan_pdm_seminar = ajuan.id_ajuan_pdm_seminar
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar=ajuan.id_jns_seminar_lama
            WHERE ajuan.soft_delete=0
            AND ajuan.stat_ajuan=".$status_validasi."
            AND ajuan.id_pd='".auth()->user()->id_pd_pengguna."'
            ORDER BY ajuan.wkt_ajuan ASC
        ";
        $data = DB::SELECT($query);
        return view('pendaftaran_seminar.ajuan.index',compact('data','status_validasi','status'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(PesertaDidik $pesertaDidik)
    {
        $prodi = GetProdiIndividu();
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $data = AjuanPdmSeminar::where('id_pd',$profil->id_pd)->where('soft_delete',0)->where('jns_ajuan','B')->where('stat_ajuan',0)->first();
        if (is_null($data)) {
            $data = new AjuanPdmSeminar();
            $data->fill($data->prepare([
                'id_pd'     => $profil->id_pd,
                'wkt_create'=> currDateTime(),
                'wkt_ajuan' => currDateTime(),
                'stat_ajuan'=> 0,
                'jns_ajuan' => 'B'
            ]))->save();
        }
        $jenis_seminar = collect(DB::SELECT("
            SELECT jns.nm_jns_seminar, jns.id_jns_seminar FROM kpta.seminar_prodi AS seminar
            JOIN ref.jenis_seminar AS jns ON jns.id_jns_seminar=seminar.id_jns_seminar
            WHERE seminar.soft_delete=0
            AND seminar.id_sms = '".$prodi->id_sms."'
            ORDER BY seminar.urutan ASC
        "))->pluck('nm_jns_seminar', 'id_jns_seminar')->toArray();
        $jenis_dok = JenisDokumen::whereNull('expired_date')->orderBy('nm_jns_dok','ASC')->pluck('nm_jns_dok','id_jns_dok')->toArray();
        $dokumen = DB::SELECT("
            SELECT list.id_ajuan_pdm_seminar, list.id_dok_ajuan_seminar, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_seminar AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_ajuan_pdm_seminar='".$data->id_ajuan_pdm_seminar."'
        ");
        return view('pendaftaran_seminar.ajuan.create',compact('profil','jenis_seminar','prodi','data','jenis_dok','dokumen'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_permanen(Request $request)
    {
        $input = $request->all();
        $input['wkt_ajuan'] = currDateTime();
        $input['stat_ajuan'] = 1;
        $data = AjuanPdmSeminar::find($input['id_ajuan_pdm_seminar']);
        $data->fill($data->prepare($input))->save();

        $cari_ver = VerAjuanPdmSeminar::where('id_ajuan_pdm_seminar',$data->id_ajuan_pdm_seminar)
            ->where('soft_delete',0)->orderBy('verifikasi_ke','DESC')->first();
        if (is_null($cari_ver)) {
            $simpan_ver = new VerAjuanPdmSeminar();
            $simpan_ver->fill($simpan_ver->prepare([
                'id_ajuan_pdm_seminar'  => $data->id_ajuan_pdm_seminar,
            ]))->save();
        } else {
            if ($cari_ver->status_periksa!='N') {
                $simpan_ver = new VerAjuanPdmSeminar();
                $simpan_ver->fill($simpan_ver->prepare([
                    'id_ver_ajuan_sebelum'  => $cari_ver->id_ver_ajuan,
                    'stat_ajuan_sebelum'    => $cari_ver->status_periksa,
                    'id_ajuan_pdm_seminar'  => $data->id_ajuan_pdm_seminar,
                    'verifikasi_ke'         => (($cari_ver->verifikasi_ke)+1)
                ]))->save();
            }
        }
        alert()->success('Ajuan Berhasil diajukan')->persistent('OK');
        return redirect()->route('pendaftaran_seminar.detail_riwayat',Crypt::encrypt($input['id_ajuan_pdm_seminar']));
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $input['nilai_seminar_baru'] = (strpos($input['nilai_seminar_baru'],',')?((float) str_replace(',', '.',($input['nilai_seminar_baru']))):$input['nilai_seminar_baru']);
        $data = AjuanPdmSeminar::where('id_ajuan_pdm_seminar',$input['id_ajuan_pdm_seminar'])->first();
        $data->fill($data->prepare($input))->save();

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, PesertaDidik $pesertaDidik)
    {
        $id_ajuan = Crypt::decrypt($id);
        $prodi = GetProdiIndividu();
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $data = AjuanPdmSeminar::find($id_ajuan);

        $dokumen = DB::SELECT("
            SELECT list.id_ajuan_pdm_seminar, list.id_dok_ajuan_seminar, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_seminar AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_ajuan_pdm_seminar='".$data->id_ajuan_pdm_seminar."'
        ");
        $validasi = VerAjuanPdmSeminar::where('id_ajuan_pdm_seminar',$data->id_ajuan_pdm_seminar)->where('soft_delete',0)->orderBy('verifikasi_ke','DESC')->get();
        return view('pendaftaran_seminar.ajuan.detail',compact('prodi','profil','dokumen','data','validasi'));
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
        $id_ajuan = Crypt::decrypt($id);
        $data = AjuanPdmSeminar::find($id_ajuan);
        $data->fill($data->prepare([
            '_method'   => 'PUT',
            'stat_ajuan'=> 0
        ]))->save();
        alert()->info('Silahkan lengkapi data yang diperlukan','Ajuan berhasil ditarik')->persistent('OK');
        return redirect()->route('pendaftaran_seminar.tambah_riwayat');
    }

    public function store_dokumen(Request $request, PesertaDidik $pesertaDidik)
    {
        $this->validate($request, [
            'nm_dok'        => 'string|max:60',
            'ket_dok'       => 'string|max:200|nullable'
        ]);
        $input = $request->all();
        if (is_null(@$input['url']) && is_null(@$input['file_dok'])) {
            alert()->error('File/Url harus terisi')->persistent('OK');
        }
        $data = AjuanPdmSeminar::find($request->id_ajuan_pdm_seminar);
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

                $simpan_dok = new DokAjuanSeminar();
                $simpan_dok->fill($simpan_dok->prepare([
                    'id_dok'                => $data_dokumen,
                    'id_ajuan_pdm_seminar'  => $data->id_ajuan_pdm_seminar,
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

    public function store_berita_acara(Request $request, PesertaDidik $pesertaDidik)
    {
       // dd($pesertaDidik);
        $this->validate($request, [
            'file_berita_acara'  => 'file',
            'id' => 'string'
        ]);
        $input = $request->all();
        if (is_null(@$input['file_berita_acara'])) {
            alert()->error('File/Url harus terisi')->persistent('OK');
        }
        $data_pdm_seminar = AjuanPdmSeminar::find($request->id_ajuan_pdm_seminar);
        $file = $request->file('file_berita_acara');
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

               BeritaAcara::create([
                    'id_dok' => $data_dokumen,
                    'id_daftar_seminar' => $request->id,
                    'id_creator' => $request->id_jns_dok,
                    'id_updater' => $request->ket_dok,
                    'status' => 0,
                ]);

                alert()->success('Data Dokumen Pendukung lainnya berhasil disimpan')->persistent('OK');
            } else {
                alert()->error('Dokumen Pendukung harus dalam format .pdf')->persistent('OK');
            }
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function hapus_dokumen($id)
    {
        $id_dok = Crypt::decrypt($id);
        $data = DokAjuanSeminar::find($id_dok);
        $data->drop();

        alert()->persistent('Berhasil menghapus dokumen')->persistent('OK');
        return redirect()->back();
    }
}
