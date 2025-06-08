<?php

namespace App\Http\Controllers\Kpta;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kpta\KonsentrasiProdi;
use Illuminate\Support\Facades\Crypt;

class KonsentrasiProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prodi = GetProdiIndividu();
        $data = KonsentrasiProdi::where('id_sms', session()->get('login.peran.id_organisasi'))->where('soft_delete', 0)->get();
        return view('kpta.konsentrasi_prodi.index', compact('data', 'prodi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $idSMS = session()->get('login.peran.id_organisasi');
        return view('__partial.form.create', [
            'judul_halaman' => 'Tambah Konsentrasi Prodi Baru',
            'route'         => 'konsentrasi_prodi.simpan',
            'backLink'      => 'konsentrasi_prodi',
            'form'          => 'kpta.konsentrasi_prodi.create',
            'id_sms'        => $idSMS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $data = new KonsentrasiProdi();
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Data Berhasil Disimpan')->persistent('ok');
        return redirect()->route('konsentrasi_prodi');
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
    public function edit($id)
    {
        $id_konsentrasi_prodi = Crypt::decrypt($id);
        $data = KonsentrasiProdi::findorfail($id_konsentrasi_prodi);

        return view('__partial.form.edit', [
            'judul_halaman'         => 'Ubah Konsentrasi Prodi',
            'route'                 => 'konsentrasi_prodi.update',
            'backLink'              => 'konsentrasi_prodi',
            'form'                  => 'kpta.konsentrasi_prodi.edit',
            'data'                  => $data,
            'id'                    => $data->id_konsentrasi_prodi,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id_konsentrasi_prodi = Crypt::decrypt($id);
        $data = KonsentrasiProdi::findorfail($id_konsentrasi_prodi);
        $input = $request->all();
        $data->fill($data->prepare($input))->save();
        alert()->success('Data Berhasil Diubah')->persistent('OK');
        return redirect()->route('konsentrasi_prodi');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id_konsentasi_prodi = Crypt::decrypt($id);
        $data = KonsentrasiProdi::findorfail($id_konsentasi_prodi);
        $data->drop();
        alert()->success('Data Berhasil Dihapus')->persistent('ok');
        return redirect()->back();
    }
}
