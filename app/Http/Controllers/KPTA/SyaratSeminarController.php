<?php

namespace App\Http\Controllers\KPTA;

use Illuminate\Http\Request;
use App\Models\Ref\JenisDokumen;
use App\Models\Kpta\SeminarProdi;
use App\Models\Kpta\SyaratSeminar;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DokumenTrait;
use App\Models\Dok\DokSyaratSeminar;
use App\Models\Dok\Dokumen;
use App\Models\Kpta\ListSyaratDaftar;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\ListSyaratSeminar;

class SyaratSeminarController extends Controller
{
    use DokumenTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request, $id)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $input = $request->all();
        $input['id_seminar_prodi'] = $data->id_seminar_prodi;
        if ($request->a_input_baru == 1) {
            $syarat = [
                'nm_syarat_seminar' => $input['nm_syarat_seminar'],
                'keterangan'        => $input['keterangan']
            ];
            $simpan_syarat = new SyaratSeminar();
            $simpan_syarat->fill($simpan_syarat->prepare($syarat))->save();
            $input['id_syarat_seminar'] = DB::getPDO()->lastInsertId();
        }

        $simpan_data = new ListSyaratSeminar();
        $simpan_data->fill($simpan_data->prepare($input))->save();

        alert()->success('Data Berhasil Disimpan')->persistent('OK');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $id_syarat)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_syarat = Crypt::decrypt($id_syarat);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_syarat = ListSyaratSeminar::findorfail($id_list_syarat);
        $syarat = DB::SELECT("
            SELECT
                list.id_list_syarat,
                syarat.id_syarat_seminar,
                syarat.nm_syarat_seminar,
                syarat.keterangan,
                list.urutan,
                list.min_syarat,
                list.maks_syarat
            FROM kpta.list_syarat_seminar AS list
            JOIN kpta.syarat_seminar AS syarat ON syarat.id_syarat_seminar = list.id_syarat_seminar
            WHERE list.soft_delete=0
            AND list.id_seminar_prodi = '" . $data->id_seminar_prodi . "'
            ORDER BY list.urutan ASC
        ");
        $list_exclude = [];
        foreach ($syarat as $each_syarat) {
            $list_exclude[] = $each_syarat->id_syarat_seminar;
        }
        $list_syarat = SyaratSeminar::select(
            'id_syarat_seminar',
            DB::RAW("(CASE WHEN keterangan IS NULL THEN nm_syarat_seminar ELSE CONCAT(nm_syarat_seminar,' (',keterangan,')') END) AS nm_syarat")
        )->where('soft_delete', 0)->where(function ($w) use ($list_exclude, $data_syarat) {
            $w->whereNotIn('id_syarat_seminar', $list_exclude)
                ->orwhere('id_syarat_seminar', $data_syarat->id_syarat_seminar);
        })->orderBy('nm_syarat_seminar')
            ->pluck('nm_syarat', 'id_syarat_seminar')->toArray();
        return view('__partial.form.edit', [
            'judul_halaman' => 'Ubah Syarat Seminar ' . $data->jenisSeminar->nm_jns_seminar,
            'route'         => 'daftar_seminar_prodi.detail.update',
            'backLink'      => 'daftar_seminar_prodi.detail',
            'form'          => 'kpta.daftar_seminar_prodi.edit_syarat',
            'data'          => $data_syarat,
            'id'            => $data_syarat->id_list_syarat,
            'list_syarat'   => $list_syarat,
            'param_form'    => $id_seminar_prodi
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $id_syarat)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_syarat = Crypt::decrypt($id_syarat);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_syarat = ListSyaratSeminar::findorfail($id_list_syarat);
        $input = $request->all();
        $data_syarat->fill($data_syarat->prepare($input))->save();

        alert()->success('Data Berhasil Diubah')->persistent('OK');
        return redirect()->route('daftar_seminar_prodi.detail', Crypt::encrypt($data->id_seminar_prodi));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $id_syarat)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_syarat = Crypt::decrypt($id_syarat);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_syarat = ListSyaratSeminar::findorfail($id_list_syarat);
        $data_syarat->drop();
        alert()->success('Berhasil menghapus persyaratan seminar')->persistent('OK');
        return redirect()->back();
    }

    public function daftar_dokumen($id, $id_syarat)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_syarat = Crypt::decrypt($id_syarat);
        $list_syarat_seminar = ListSyaratSeminar::findorfail($id_list_syarat);
        $data = SeminarProdi::findorfail($id_seminar_prodi);

        $jenis_dok = JenisDokumen::whereNull('expired_date')->orderBy('nm_jns_dok', 'ASC')->pluck('nm_jns_dok', 'id_jns_dok')->toArray();
        $dokumen = DB::SELECT("
        SELECT 
            dok_syarat.id_dok_syarat_seminar,
            dok_syarat.id_seminar_prodi,
            dok_syarat.id_list_syarat,
            dok_syarat.id_dok,
            jns.nm_jns_dok,
            dok.nm_dok,
            dok.ket_dok,
            dok.file_dok,
            dok.wkt_unggah
        FROM dok.dok_syarat_seminar AS dok_syarat
        JOIN dok.dokumen AS dok ON dok.id_dok = dok_syarat.id_dok
        JOIN ref.jenis_dokumen AS jns ON jns.id_jns_dok = dok.id_jns_dok
        WHERE dok_syarat.soft_delete=0
        AND dok_syarat.id_seminar_prodi ='" . $id_seminar_prodi . "'
        AND dok_syarat.id_list_syarat ='" . $id_list_syarat . "'
        ORDER BY dok.wkt_unggah ASC
       ");

        return view('kpta.daftar_seminar_prodi.dokumen', compact('list_syarat_seminar', 'data', 'jenis_dok', 'dokumen', 'id_seminar_prodi'));
    }

    public function store_dokumen(Request $request, $id, $id_syarat)
    {
        $this->validate($request, [
            'nm_dok' => 'string|max:60',
            'ket_dok' => 'string|max:200|nullable',
        ]);

        $input = $request->all();
        if (is_null(@$input['file_dok'])) {
            alert()->error('File harus terisi')->persistent('OK');
        }

        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_syarat = Crypt::decrypt($id_syarat);
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

                $simpan_dok = new DokSyaratSeminar();
                $simpan_dok->fill($simpan_dok->prepare([
                    'id_dok'                => $data_dokumen,
                    'id_seminar_prodi' => $id_seminar_prodi,
                    'id_list_syarat' => $id_list_syarat,
                ]))->save();

                alert()->success('Data Dokumen Syarat Seminar berhasil disimpan')->persistent('OK');
            } else {
                alert()->error('Dokumen Syarat Seminar harus dalam format .pdf')->persistent('OK');
            }
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function hapus_dokumen($id, $id_syarat, $id_dok){
        $id_dok = Crypt::decrypt($id_dok);
        // dd($id_dok);
        $data = DokSyaratSeminar::find($id_dok);
        $data->drop();
        alert()->success('Data berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }
}
