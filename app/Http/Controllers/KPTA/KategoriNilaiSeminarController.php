<?php

namespace App\Http\Controllers\Kpta;

use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use App\Models\Kpta\SeminarProdi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Models\Kpta\KategoriNilaiSeminar;
use App\Models\Kpta\KomponenNilaiSeminar;
use App\Models\Kpta\ListKategoriNilaiSeminar;
use App\Models\Kpta\ListKomponenNilaiSeminar;

class KategoriNilaiSeminarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store_kategori($id, Request $request)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $input = $request->all();
        $input['id_seminar_prodi'] = $data->id_seminar_prodi;
        if ($request->a_input_baru == 1) {
            $kategori_nilai = [
                'nm_kategori_nilai' => $input['nm_kategori_nilai'],
                'keterangan'        => $input['keterangan']
            ];
            $simpan_kategori_nilai = new KategoriNilaiSeminar();
            $simpan_kategori_nilai->fill($simpan_kategori_nilai->prepare($kategori_nilai))->save();
            $input['id_kategori_nilai'] = $simpan_kategori_nilai->id_kategori_nilai;
        }

        $simpan_data = new ListKategoriNilaiSeminar();
        $simpan_data->fill($simpan_data->prepare($input))->save();

        alert()->success('Data Berhasil Disimpan')->persistent('OK');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit_kategori($id, $id_kategori_nilai)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_kategori_nilai = Crypt::decrypt($id_kategori_nilai);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_kategori_nilai = ListKategoriNilaiSeminar::findorfail($id_list_kategori_nilai);
        $kategori_nilai = DB::SELECT("
        SELECT
            list.id_list_kategori_nilai,
            list.urutan,
            kategori.id_kategori_nilai,
            kategori.nm_kategori_nilai,
            kategori.keterangan
        FROM kpta.list_kategori_nilai_seminar AS list
        JOIN kpta.kategori_nilai_seminar AS kategori ON kategori.id_kategori_nilai = list.id_kategori_nilai
        WHERE list.soft_delete=0
        AND list.id_seminar_prodi = '" . $data->id_seminar_prodi . "'
        ORDER BY list.urutan ASC
        ");

        $list_exclude = [];
        foreach ($kategori_nilai as $each_kategori) {
            $list_exclude[] = $each_kategori->id_kategori_nilai;
        }
        $list_kategori_nilai = KategoriNilaiSeminar::select(
            'id_kategori_nilai',
            DB::RAW("(CASE WHEN keterangan IS NULL THEN nm_kategori_nilai ELSE CONCAT(nm_kategori_nilai,' (',keterangan,')') END) AS nm_kategori")
        )->where('soft_delete', 0)->where(function ($w) use ($list_exclude, $data_kategori_nilai) {
            $w->whereNotIn('id_kategori_nilai', $list_exclude)
                ->orwhere('id_kategori_nilai', $data_kategori_nilai->id_kategori_nilai);
        })->orderBy('nm_kategori_nilai')
            ->pluck('nm_kategori', 'id_kategori_nilai')->toArray();
        return view('__partial.form.edit', [
            'judul_halaman'         => 'Ubah Kategori Nilai Seminar' . $data->jenisSeminar->nm_jns_seminar,
            'route'                 => 'daftar_seminar_prodi.detail_kategori.update',
            'backLink'              => 'daftar_seminar_prodi.detail_kategori',
            'form'                  => 'kpta.daftar_seminar_prodi.kategori_nilai.edit',
            'data'                  => $data_kategori_nilai,
            'id'                    => $data_kategori_nilai->id_list_kategori_nilai,
            'list_kategori_nilai'   => $list_kategori_nilai,
            'param_form'            => $id_seminar_prodi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_kategori(Request $request, $id, $id_kategori_nilai)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_kategori_nilai = Crypt::decrypt($id_kategori_nilai);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_kategori_nilai = ListKategoriNilaiSeminar::findorfail($id_list_kategori_nilai);
        $input = $request->all();
        $data_kategori_nilai->fill($data_kategori_nilai->prepare($input))->save();
        alert()->success('Data Berhasil Diubah')->persistent('OK');
        return redirect()->route('daftar_seminar_prodi.detail_kategori', Crypt::encrypt($data->id_seminar_prodi));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy_kategori($id, $id_kategori_nilai)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_kategori_nilai = Crypt::decrypt($id_kategori_nilai);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_kategori_nilai = ListKategoriNilaiSeminar::findorfail($id_list_kategori_nilai);
        $data_kategori_nilai->drop();
        alert()->success('Data Berhasil Dihapus')->persistent('OK');
        return redirect()->back();
    }

    public function komponen_nilai($id, $id_kategori_nilai)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_kategori_nilai = Crypt::decrypt($id_kategori_nilai);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_kategori_nilai = ListKategoriNilaiSeminar::select('urutan', 'nm_kategori_nilai', 'keterangan', 'id_list_kategori_nilai')
            ->join('kpta.kategori_nilai_seminar AS kategori', 'kategori.id_kategori_nilai', '=', 'list_kategori_nilai_seminar.id_kategori_nilai')
            ->where('list_kategori_nilai_seminar.soft_delete', 0)
            ->where('list_kategori_nilai_seminar.id_seminar_prodi', $id_seminar_prodi)
            ->where('list_kategori_nilai_seminar.id_list_kategori_nilai', $id_list_kategori_nilai)
            ->first();

        $komponen_nilai = DB::SELECT("
        SELECT
            list.id_list_komponen_nilai,
            list.urutan,
            list.id_list_kategori_nilai,
            komponen.id_komponen_nilai,
            komponen.nm_komponen_nilai,
            komponen.keterangan
        FROM kpta.list_komponen_nilai_seminar AS list
        JOIN kpta.komponen_nilai_seminar AS komponen ON komponen.id_komponen_nilai = list.id_komponen_nilai
        WHERE list.soft_delete=0
        AND list.id_seminar_prodi = '" .  $id_seminar_prodi . "'
        AND list.id_list_kategori_nilai = '" .  $data_kategori_nilai->id_list_kategori_nilai . "'
        ORDER BY list.urutan ASC
        ");
        $list_exclude = [];
        foreach ($komponen_nilai as $each_komponen) {
            $list_exclude[] = $each_komponen->id_komponen_nilai;
        }
        $list_komponen_nilai = KomponenNilaiSeminar::select(
            'id_komponen_nilai',
            DB::RAW("(CASE WHEN keterangan IS NULL THEN nm_komponen_nilai ELSE CONCAT(nm_komponen_nilai,' (',keterangan,')') END) AS nm_komponen")
        )->where('soft_delete', 0)->whereNotIn('id_komponen_nilai', $list_exclude)
            ->orderBy('nm_komponen_nilai')
            ->pluck('nm_komponen', 'id_komponen_nilai')->toArray();

        return view('kpta.daftar_seminar_prodi.kategori_nilai.komponen_nilai.detail', compact('data', 'data_kategori_nilai', 'komponen_nilai', 'list_komponen_nilai'));
    }

    public function store_komponen($id, $id_kategori_nilai, Request $request)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_kategori_nilai = Crypt::decrypt($id_kategori_nilai);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_kategori_seminar = ListKategoriNilaiSeminar::findorfail($id_list_kategori_nilai);
        // dd($data, $data_kategori_seminar);
        $input = $request->all();
        $input['id_seminar_prodi'] = $data->id_seminar_prodi;
        $input['id_list_kategori_nilai'] = $data_kategori_seminar->id_list_kategori_nilai;
        if ($request->a_input_baru == 1) {
            $komponen_nilai = [
                'nm_komponen_nilai' => $input['nm_komponen_nilai'],
                'keterangan'        => $input['keterangan']
            ];
            $simpan_komponen_nilai = new KomponenNilaiSeminar();
            $simpan_komponen_nilai->fill($simpan_komponen_nilai->prepare($komponen_nilai))->save();
            $input['id_komponen_nilai'] = $simpan_komponen_nilai->id_komponen_nilai;
        }

        $simpan_data = new ListKomponenNilaiSeminar();
        $simpan_data->fill($simpan_data->prepare($input))->save();

        alert()->success('Data Berhasil Disimpan')->persistent('OK');
        return redirect()->back();
    }

    public function edit_komponen($id, $id_kategori_nilai, $id_komponen)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_kategori_nilai = Crypt::decrypt($id_kategori_nilai);
        $id_list_komponen_nilai = Crypt::decrypt($id_komponen);
        $data = SeminarProdi::findorfail($id_seminar_prodi);
        $data_komponen_nilai = ListKomponenNilaiSeminar::findorfail($id_list_komponen_nilai);
        $komponen_nilai = DB::SELECT("
        SELECT
            list.id_list_komponen_nilai,
            list.urutan,
            komponen.id_komponen_nilai,
            komponen.nm_komponen_nilai,
            komponen.keterangan
        FROM kpta.list_komponen_nilai_seminar AS list
        JOIN kpta.komponen_nilai_seminar AS komponen ON komponen.id_komponen_nilai = list.id_komponen_nilai
        WHERE list.soft_delete=0
        AND list.id_seminar_prodi = '" .  $id_seminar_prodi . "'
        AND list.id_list_kategori_nilai = '" . $id_list_kategori_nilai . "'
        ORDER BY list.urutan ASC
        ");
        // dd($komponen_nilai);

        $list_exclude = [];
        foreach ($komponen_nilai as $each_komponen) {
            $list_exclude[] = $each_komponen->id_komponen_nilai;
        }
        $list_komponen_nilai = KomponenNilaiSeminar::select(
            'id_komponen_nilai',
            DB::RAW("(CASE WHEN keterangan IS NULL THEN nm_komponen_nilai ELSE CONCAT(nm_komponen_nilai,' (',keterangan,')') END) AS nm_komponen")
        )->where('soft_delete', 0)->where(function ($w) use ($list_exclude, $data_komponen_nilai) {
            $w->whereNotIn('id_komponen_nilai', $list_exclude)
                ->orwhere('id_komponen_nilai', $data_komponen_nilai->id_komponen_nilai);
        })->orderBy('nm_komponen_nilai')
            ->pluck('nm_komponen', 'id_komponen_nilai')->toArray();
        // dd($list_komponen_nilai);
        return view('__partial.form.edit', [
            'judul_halaman'         => 'Ubah Kategori Nilai Seminar' . $data->jenisSeminar->nm_jns_seminar,
            'route'                 => 'daftar_seminar_prodi.detail_kategori.daftar_komponen_nilai.update',
            'backLink'              => 'daftar_seminar_prodi.detail_kategori.daftar_komponen_nilai',
            'form'                  => 'kpta.daftar_seminar_prodi.kategori_nilai.komponen_nilai.edit',
            'data'                  => $data_komponen_nilai,
            'id'                    => $data_komponen_nilai->id_list_kategori_nilai,
            'list_komponen_nilai'   => $list_komponen_nilai,
            'param_form'            => [$id_seminar_prodi, $id_list_kategori_nilai]
        ]);
    }

    public function update_komponen(Request $request, $id, $id_kategori_nilai, $id_komponen)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_kategori_nilai = Crypt::decrypt($id_kategori_nilai);
        $id_list_komponen_nilai = Crypt::decrypt($id_komponen);
        $data_komponen_nilai = ListKomponenNilaiSeminar::findorfail($id_list_komponen_nilai);
        $input = $request->all();
        $data_komponen_nilai->fill($data_komponen_nilai->prepare($input))->save();
        alert()->success('Data Berhasil Diubah')->persistent('OK');
        return redirect()->route('daftar_seminar_prodi.detail_kategori.daftar_komponen_nilai', [Crypt::encrypt($id_seminar_prodi), Crypt::encrypt($id_list_kategori_nilai)]);
    }

    public function destroy_komponen($id, $id_kategori_nilai, $id_komponen)
    {
        $id_seminar_prodi = Crypt::decrypt($id);
        $id_list_kategori_nilai = Crypt::decrypt($id_kategori_nilai);
        $id_list_komponen_nilai = Crypt::decrypt($id_komponen);
        $data_komponen_nilai = ListKomponenNilaiSeminar::findorfail($id_list_komponen_nilai);
        $data_komponen_nilai->drop();
        alert()->success('Data Berhasil Dihapus')->persistent('OK');
        return redirect()->back();
    }
}
