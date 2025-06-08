<?php

namespace App\Http\Controllers\Kpta\SeminarProdi;

use Illuminate\Http\Request;
use App\Models\Ref\JenisSeminar;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Kpta\JangkaWaktuPenyusunan;
use App\Models\Ref\JenjangPendidikan;
use Illuminate\Support\Facades\Crypt;

class JangkaWaktuPenyusunanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $satuan_pendidikan = DB::table('pdrd.satuan_pendidikan AS tsp')
            ->where('tsp.soft_delete', 0)->where('tsp.id_sp', session()->get('login.peran.id_organisasi'))
            ->first();

        $data = DB::table('kpta.jangka_waktu_penyusunan as jangka_wkt')
            ->join('ref.jenjang_pendidikan as jenj_didik', 'jenj_didik.id_jenj_didik', '=', 'jangka_wkt.id_jenj_didik')
            ->join('ref.jenis_seminar as jns_seminar', 'jns_seminar.id_jns_seminar', '=', 'jangka_wkt.id_jns_seminar')
            ->where([
                ['jangka_wkt.id_sp', $satuan_pendidikan->id_sp],
                ['jangka_wkt.soft_delete', 0],
            ])
            ->select('jenj_didik.nm_jenj_didik', 'jns_seminar.nm_jns_seminar', 'jangka_wkt.durasi_penyusunan', 'jangka_wkt.durasi_perpanjangan', 'jangka_wkt.id_jangka_wkt')
            ->get();

        return view('kpta.jangka_waktu_penyusunan.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $JenisSeminar = JenisSeminar::select('id_jns_seminar', 'nm_jns_seminar')
            ->where('a_seminar', 1)->wherenull('expired_date')
            ->pluck('nm_jns_seminar', 'id_jns_seminar')->toarray();

        $JenjDidik = JenjangPendidikan::select('id_jenj_didik', 'nm_jenj_didik')
            ->wherenull('expired_date')
            ->pluck('nm_jenj_didik', 'id_jenj_didik')->toarray();

        $IdSp = session()->get('login.peran.id_organisasi');

        return view('__partial.form.create', [
            'judul_halaman' => 'Tambah Jangka Waktu Penyusunan Seminar',
            'route'         => 'seminar_prodi.jangka_waktu_penyusunan.simpan',
            'backLink'      => 'seminar_prodi.jangka_waktu_penyusunan',
            'form'          => 'kpta.jangka_waktu_penyusunan.create',
            'jenis_seminar' => $JenisSeminar,
            'jenj_didik'    => $JenjDidik,
            'id_sp'        => $IdSp,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $data = new JangkaWaktuPenyusunan();
        $data->fill($data->prepare($input))->save();
        alert()->success('Data Berhasil Disimpan')->persistent('ok');
        return redirect()->route('seminar_prodi.jangka_waktu_penyusunan');
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
    public function edit(string $id)
    {
        $id_jangka_wkt = Crypt::decrypt($id);
        $data = DB::table('kpta.jangka_waktu_penyusunan as jangka_wkt')
            ->join('ref.jenjang_pendidikan as jenj_didik', 'jenj_didik.id_jenj_didik', '=', 'jangka_wkt.id_jenj_didik')
            ->join('ref.jenis_seminar as jns_seminar', 'jns_seminar.id_jns_seminar', '=', 'jangka_wkt.id_jns_seminar')
            ->where([
                ['jangka_wkt.id_jangka_wkt', $id_jangka_wkt],
                ['jangka_wkt.soft_delete', 0],
            ])
            ->select('jenj_didik.nm_jenj_didik', 'jns_seminar.nm_jns_seminar', 'jangka_wkt.durasi_penyusunan', 'jangka_wkt.durasi_perpanjangan', 'jangka_wkt.id_jangka_wkt', 'jangka_wkt.id_jns_seminar', 'jangka_wkt.id_jenj_didik')
            ->first();

        $JenisSeminar = JenisSeminar::select('id_jns_seminar', 'nm_jns_seminar')
            ->where('a_seminar', 1)->wherenull('expired_date')
            ->pluck('nm_jns_seminar', 'id_jns_seminar')->toarray();

        $JenjDidik = JenjangPendidikan::select('id_jenj_didik', 'nm_jenj_didik')
            ->wherenull('expired_date')
            ->pluck('nm_jenj_didik', 'id_jenj_didik')->toarray();

        $IdSp = session()->get('login.peran.id_organisasi');
        return view('__partial.form.edit', [
            'judul_halaman'         => 'Ubah Jangka Wkatu Penyusunan' . $data->nm_jns_seminar . '(' . $data->nm_jenj_didik . ')',
            'route'                 => 'seminar_prodi.jangka_waktu_penyusunan.update',
            'backLink'              => 'seminar_prodi.jangka_waktu_penyusunan',
            'form'                  => 'kpta.jangka_waktu_penyusunan.edit',
            'data'                  => $data,
            'id'                    => $data->id_jangka_wkt,
            'jenis_seminar'         => $JenisSeminar,
            'jenj_didik'            => $JenjDidik,
            'id_sp'                 => $IdSp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id_jangka_wkt = Crypt::decrypt($id);
        $data = JangkaWaktuPenyusunan::findorfail($id_jangka_wkt);
        $input = $request->all();
        $data->fill($data->prepare($input))->save();
        // dd($data, $input);
        alert()->success('Data Berhasil Diubah')->persistent('OK');
        return redirect()->route('seminar_prodi.jangka_waktu_penyusunan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id_jangka_wkt = Crypt::decrypt($id);
        $data = JangkaWaktuPenyusunan::findorfail($id_jangka_wkt);
        $data->drop();
        alert()->success('Data Berhasil Dihapus')->persistent('ok');
        return redirect()->back();
    }
}
