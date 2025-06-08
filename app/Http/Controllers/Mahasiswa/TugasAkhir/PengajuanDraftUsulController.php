<?php

namespace App\Http\Controllers\Mahasiswa\TugasAkhir;

use Illuminate\Http\Request;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Dok\DokAjuanDraftUsul;
use App\Models\Ref\JenisDokumen;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\DokumenTrait;
use App\Models\Validasi\AjuanDraftUsul;
use App\Models\Validasi\VerAjuanDraftUsul;

class PengajuanDraftUsulController extends Controller
{
    use DokumenTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PesertaDidik $pesertaDidik)
    {
        $prodi = GetProdiIndividu();
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $jenis_seminar_ta = DB::table('kpta.seminar_prodi as seminar')
            ->join('ref.jenis_seminar as jns', 'jns.id_jns_seminar', '=', 'seminar.id_jns_seminar')
            ->join('ref.jenis_seminar as induk_jns', 'induk_jns.id_jns_seminar', '=', 'jns.id_induk_jns_seminar')
            ->where([
                ['seminar.soft_delete', 0],
                ['seminar.id_sms', $profil->id_sms],
                ['jns.a_tugas_akhir', 1],
            ])
            ->whereNull('jns.expired_date')
            ->whereNull('induk_jns.expired_date')
            ->select('induk_jns.id_jns_seminar', 'induk_jns.nm_jns_seminar')
            ->first();
        // dd($jenis_seminar_ta);
        
        $data = DB::select("
            SELECT
                ajuan.id_ajuan_draft_usul,
                ajuan.id_jns_seminar,
                ajuan.judul_draft_usul_baru,
                ajuan.stat_ajuan,
                ajuan.jns_ajuan,
                ajuan.wkt_ajuan,
                ajuan.tgl_create,
                ajuan.id_creator,
                ajuan.last_update,
                DATE_PART('day', NOW() - ajuan.wkt_ajuan::timestamp) AS umur_ajuan
            FROM validasi.ajuan_draft_usul AS ajuan
            WHERE ajuan.soft_delete = 0
            AND ajuan.id_pd='" . $profil->id_pd . "'
        ");

        $pengajuan = collect($data)->first();

        return view('pengajuan_draft_usul.index', compact('prodi', 'jenis_seminar_ta', 'profil', 'data', 'pengajuan',));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create( PesertaDidik $pesertaDidik, Request $request)
    {
        $jenis_seminar = $request->input('id_jns_seminar');
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $data = AjuanDraftUsul::where('id_pd', $profil->id_pd)->where('soft_delete', 0)->where('jns_ajuan', 'B')->where('stat_ajuan', 0)->first();
        if (is_null($data)) {
            $data = new AjuanDraftUsul();
            $data->fill($data->prepare([
                'id_pd' => $profil->id_pd,
                'id_jns_seminar' => $jenis_seminar,
                'wkt_create' => currDateTime(),
                'stat_ajuan' => 0,
                'jns_ajuan' => 'B',
            ]))->save();
            alert()->info('Silahkan lengkapi persyaratan berikut')->persistent('OK');
        }

        $jenis_dok = JenisDokumen::whereNull('expired_date')->orderBy('nm_jns_dok', 'ASC')->pluck('nm_jns_dok', 'id_jns_dok')->toArray();
        $dokumen = DB::SELECT("
            SELECT list.id_ajuan_draft_usul, list.id_dok_ajuan_draft_usul, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_draft_usul AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_ajuan_draft_usul= '" . $data->id_ajuan_draft_usul . "'
        ");

        //dd($dokumen);
        return view('pengajuan_draft_usul.create', compact('profil', 'data', 'jenis_dok', 'dokumen'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store_permanen(Request $request)
    {
        $input = $request->all();
        $input['wkt_ajuan'] = currDateTime();
        $input['stat_ajuan'] = 1;
        $data = AjuanDraftUsul::find($input['id_ajuan_draft_usul']);
        $data->fill($data->prepare($input))->save();

        $cari_ver = VerAjuanDraftUsul::where('id_ajuan_draft_usul', $data->id_ajuan_draft_usul)->where('soft_delete', 0)->orderBy('verifikasi_ke', 'DESC')->first();
        if (is_null($cari_ver)) {
            $simpan_ver = new VerAjuanDraftUsul();
            $simpan_ver->fill($simpan_ver->prepare([
                'id_ajuan_draft_usul' => $data->id_ajuan_draft_usul
            ]))->save();
        } else {
            if ($cari_ver->status_periksa != 'N') {
                $simpan_ver = new VerAjuanDraftUsul();
                $simpan_ver->fill($simpan_ver->prepare([
                    'id_ver_ajuan_sebelum'  => $cari_ver->id_ver_ajuan,
                    'stat_ajuan_sebelum'    => $cari_ver->status_periksa,
                    'id_ajuan_draft_usul'  => $data->id_ajuan_draft_usul,
                    'verifikasi_ke'         => (($cari_ver->verifikasi_ke) + 1)
                ]))->save();
            }
        }
        alert()->success('Ajuan Berhasil diajukan')->persistent('OK');
        return redirect()->route('tugas_akhir.pengajuan_draft_usul.detail', Crypt::encrypt($input['id_ajuan_draft_usul']));
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $data = AjuanDraftUsul::where('id_ajuan_draft_usul', $input['id_ajuan_draft_usul'])->first();
        $data->fill($data->prepare($input))->save();
        alert()->success('Data Berhasil Disimpan')->persistent('OK');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */

    public function store_dokumen(Request $request, PesertaDidik $pesertaDidik)
    {
        $this->validate($request, [
            'nm_dok'        => 'string|max:60',
            'ket_dok'       => 'string|max:200|nullable'
        ]);
        $input = $request->all();
        //dd($input);
        if (is_null(@$input['file_dok'])) {
            alert()->error('File harus terisi')->persistent('OK');
        }
        $data = AjuanDraftUsul::find($request->id_ajuan_draft_usul);
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

                $simpan_dok = new DokAjuanDraftUsul();
                $simpan_dok->fill($simpan_dok->prepare([
                    'id_dok'                => $data_dokumen,
                    'id_ajuan_draft_usul'  => $data->id_ajuan_draft_usul,
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

    public function show($id, PesertaDidik $pesertaDidik)
    {
        $ajuan_draft_usul = Crypt::decrypt($id);
        $profil = $pesertaDidik->id_detail_mahasiswa(auth()->user()->id_pd_pengguna);
        $data = AjuanDraftUsul::find($ajuan_draft_usul);

        $jenis_dok = JenisDokumen::whereNull('expired_date')->orderBy('nm_jns_dok', 'ASC')->pluck('nm_jns_dok', 'id_jns_dok')->toArray();
        $dokumen = DB::SELECT("
            SELECT list.id_ajuan_draft_usul, list.id_dok_ajuan_draft_usul, list.id_dok, dok.nm_dok, jns.nm_jns_dok, dok.wkt_unggah
            FROM dok.dok_ajuan_draft_usul AS list
            JOIN dok.dokumen AS dok ON dok.id_dok = list.id_dok AND dok.soft_delete=0
            JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
            WHERE list.soft_delete=0
            AND list.id_ajuan_draft_usul= '" . $data->id_ajuan_draft_usul . "'
        ");

        $validasi = VerAjuanDraftUsul::where('id_ajuan_draft_usul', $data->id_ajuan_draft_usul)->where('soft_delete', 0)->orderBy('verifikasi_ke', 'DESC')->get();

        //dd($dokumen);
        return view('pengajuan_draft_usul.detail', compact('profil', 'data', 'jenis_dok', 'dokumen', 'validasi'));
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
    public function destroy($id)
    {
        $id_ajuan = Crypt::decrypt($id);
        $data = AjuanDraftUsul::find($id_ajuan);
        $data->fill($data->prepare([
            '_method' => 'PUT',
            'stat_ajuan' => 0
        ]))->save();
        alert()->info('Silahkan lengkapi data yang diperlukan', 'Ajuan berhasil ditarik')->persistent('OK');
        return redirect()->route('tugas_akhir.pengajuan_draft_usul.detail', Crypt::encrypt($id_ajuan));
    }

    public function hapus_dokumen($id)
    {
        $id_dok = Crypt::decrypt($id);
        $data = DokAjuanDraftUsul::find($id_dok);
        $data->drop();

        alert()->success('Berhasil menghapus dokumen')->persistent('OK');
        return redirect()->back();
    }
}
