<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ref\TahunAjaranRequest;
use App\Models\Ref\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = TahunAjaran::where('tgl_mulai','<=',currDateTime())->whereNull('expired_date')->orderBy('id_thn_ajaran','DESC')->get();
        return view('data_master.tahun_ajaran.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Tahun Ajaran Baru',
            'route'         => 'data_master.tahun_ajaran.simpan',
            'backLink'      => 'data_master.tahun_ajaran',
            'form'          => 'data_master.tahun_ajaran.create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TahunAjaranRequest $request)
    {
        $input = $request->all();
        $input['id_thn_ajaran'] = substr($input['nm_thn_ajaran'],0,4);
        $cari = TahunAjaran::find($input['id_thn_ajaran']);
        if (is_null($cari)) {
            $data = new TahunAjaran();
            $data->fill($data->prepare($input));
            $data->save();
            alert()->success('Tahun Ajaran '.$input['nm_thn_ajaran'].' berhasil ditambahkan')->persistent('OK');
        } else {
            alert()->error('Tahun Ajaran '.$input['nm_thn_ajaran'].' sudah ada')->persistent('OK');
        }
        return redirect()->route('data_master.tahun_ajaran');
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
        $data = TahunAjaran::find($id);
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah Tahun Ajaran Baru',
            'route'         => 'data_master.tahun_ajaran.update',
            'backLink'      => 'data_master.tahun_ajaran',
            'form'          => 'data_master.tahun_ajaran.edit',
            'data'          => $data,
            'id'            => $id
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TahunAjaranRequest $request, $id)
    {
        $id = Crypt::decrypt($id);
        $input = $request->all();
        $data = TahunAjaran::findorfail($id);
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Tahun Ajaran '.$input['nm_thn_ajaran'].' berhasil diubah')->persistent('OK');
        return redirect()->route('data_master.tahun_ajaran');
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
        $data = TahunAjaran::findorfail($id);
        $data->drop();
        alert()->success('Tahun Ajaran '.$data->nm_thn_ajaran.' berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }

    public function ubah_aktif($id)
    {
        $id_thn_ajaran = Crypt::decrypt($id);
        $data = TahunAjaran::findorfail($id_thn_ajaran);
        $input = [];
        $input['_method']='PUT';
        if($data->a_periode_aktif==0) {
            $input['a_periode_aktif'] = 1;
            $input['pesan'] = 'Tahun Ajaran '.$data->nm_smt.' berhasil di-aktifkan';
        } else {
            $input['a_periode_aktif'] = 0;
            $input['pesan'] = 'Tahun Ajaran '.$data->nm_smt.' berhasil di-non-aktifkan';
        }
        $data->fill($data->prepare($input))->save();
        alert()->success($input['pesan'])->persistent('OK');
        return redirect()->back();
    }
}
