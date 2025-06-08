<?php

namespace App\Http\Controllers\ManAkses;

use App\Http\Controllers\Controller;
use App\Models\ManAkses\Aplikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AplikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Aplikasi::whereNull('expired_date')->OrderBy('nm_aplikasi','ASC')->get();
        return view('man_akses.aplikasi.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Aplikasi Baru',
            'route'         => 'manajemen_akses.aplikasi.simpan',
            'backLink'      => 'manajemen_akses.aplikasi',
            'form'          => 'man_akses.aplikasi.create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $data = new Aplikasi();
        $data->fill($data->prepare($input))->save();
        alert()->success('Berhasil menambahan aplikasi '.$data->nm_aplikasi)->persistent('OK');
        return redirect()->route('manajemen_akses.aplikasi');
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
    public function edit($id)
    {
        $id_aplikasi = Crypt::decrypt($id);
        $data = Aplikasi::findorfail($id_aplikasi);
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah Aplikasi',
            'route'         => 'manajemen_akses.aplikasi.update',
            'backLink'      => 'manajemen_akses.aplikasi',
            'form'          => 'man_akses.aplikasi.edit',
            'data'          => $data,
            'id'            => $id_aplikasi
        ]);
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
        $id = Crypt::decrypt($id);
        $input = $request->all();
        $data = Aplikasi::findorfail($id);
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Aplikasi '.$input['nm_aplikasi'].' berhasil diubah')->persistent('OK');
        return redirect()->route('manajemen_akses.aplikasi');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $data = Aplikasi::findorfail($id);
        $data->drop();
        alert()->success('Aplikasi '.$data->nm_aplikasi.' berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }
}
