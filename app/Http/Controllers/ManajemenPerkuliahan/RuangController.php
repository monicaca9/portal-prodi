<?php

namespace App\Http\Controllers\ManajemenPerkuliahan;

use App\Http\Controllers\Controller;
use App\Models\Manajemen\Gedung;
use App\Models\Manajemen\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class RuangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $id = Crypt::decrypt($id);
        $gedung = Gedung::findorfail($id);
        $data = Ruang::where('id_gedung',$gedung->id_gedung)->where('soft_delete',0)->orderBy('nm_ruang','ASC')->get();
        return view('manajemen_matakuliah.gedung.ruang.index',compact('gedung','data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $id = Crypt::decrypt($id);
        $gedung = Gedung::findorfail($id);
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Ruang Baru',
            'route'         => 'gedung_ruang.detail_ruang.simpan',
            'backLink'      => 'gedung_ruang.detail_ruang',
            'form'          => 'manajemen_matakuliah.gedung.ruang.create',
            'param_form'    => $gedung->id_gedung,
            'gedung'        => $gedung,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $id = Crypt::decrypt($id);
        $gedung = Gedung::findorfail($id);
        $input = $request->all();
        $data = new Ruang();
        $data->fill($data->prepare($input))->save();
        alert()->success('Ruang baru ditambahkan')->persistent('OK');
        return redirect()->route('gedung_ruang.detail_ruang',Crypt::encrypt($gedung->id_gedung));
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
    public function edit($id,$id_ruang)
    {
        $id = Crypt::decrypt($id);
        $id_ruang = Crypt::decrypt($id_ruang);
        $gedung = Gedung::findorfail($id);
        $data = Ruang::findorfail($id_ruang);
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah Ruang',
            'route'         => 'gedung_ruang.detail_ruang.update',
            'backLink'      => 'gedung_ruang.detail_ruang',
            'form'          => 'manajemen_matakuliah.gedung.ruang.edit',
            'id'            => $data->id_ruang,
            'param_form'    => $gedung->id_gedung,
            'gedung'        => $gedung,
            'data'          => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $id_ruang)
    {
        $id = Crypt::decrypt($id);
        $id_ruang = Crypt::decrypt($id_ruang);
        $gedung = Gedung::findorfail($id);
        $data = Ruang::findorfail($id_ruang);
        $input = $request->all();
        $data->fill($data->prepare($input))->save();
        alert()->success('Gedung berhasil diubah')->persistent('OK');
        return redirect()->route('gedung_ruang.detail_ruang',Crypt::encrypt($gedung->id_gedung));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $id_ruang)
    {
        $id = Crypt::decrypt($id);
        $id_ruang = Crypt::decrypt($id_ruang);
        $gedung = Gedung::findorfail($id);
        $data = Ruang::findorfail($id_ruang);
        $data->drop();
        alert()->success('Ruang berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }
}
