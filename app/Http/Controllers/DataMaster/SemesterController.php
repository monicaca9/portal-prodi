<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Ref\Semester;
use App\Models\Ref\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Semester::where('tgl_mulai','<=',currDateTime())->whereNull('expired_date')->orderBy('tgl_mulai','DESC')->get();
        return view('data_master.semester.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tahun_ajaran = TahunAjaran::where('tgl_mulai','<=',currDateTime())->whereNull('expired_date')->orderBy('id_thn_ajaran','DESC')->pluck('nm_thn_ajaran','id_thn_ajaran')->toArray();
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Semester Baru',
            'route'         => 'data_master.semester.simpan',
            'backLink'      => 'data_master.semester',
            'form'          => 'data_master.semester.create',
            'tahun_ajaran'  => $tahun_ajaran
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
        $cari_ta = TahunAjaran::find($input['id_thn_ajaran']);
        $input['id_smt'] = $input['id_thn_ajaran'].$input['smt'];
        $input['nm_smt'] = $cari_ta->nm_thn_ajaran.' '.config('mp.data_master.smt.'.$input['smt']);
        $cari_smt = Semester::find($input['id_smt']);
        if (is_null($cari_smt)) {
            $data = new Semester();
            $data->fill($data->prepare($input));
            $data->save();
            alert()->success('Semester '.$input['nm_smt'].' berhasil ditambahkan')->persistent('OK');
        } else {
            alert()->error('Semester '.$input['nm_smt'].' sudah ada')->persistent('OK');
        }
        return redirect()->route('data_master.semester');
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
        $data = Semester::find($id);
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah Semester',
            'route'         => 'data_master.semester.update',
            'backLink'      => 'data_master.semester',
            'form'          => 'data_master.semester.edit',
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
    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $input = $request->all();
        $data = Semester::findorfail($id);
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Semester '.$input['nm_smt'].' berhasil diubah')->persistent('OK');
        return redirect()->route('data_master.semester');
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
        $data = Semester::findorfail($id);
        $data->drop();
        alert()->success('Semester '.$data->nm_smt.' berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }

    public function ubah_aktif($id)
    {
        $id_thn_ajaran = Crypt::decrypt($id);
        $data = Semester::findorfail($id_thn_ajaran);
        $input = [];
        $input['_method']='PUT';
        if($data->a_periode_aktif==0) {
            $input['a_periode_aktif'] = 1;
            $input['pesan'] = 'Semester '.$data->nm_thn_ajaran.' berhasil di-aktifkan';
        } else {
            $input['a_periode_aktif'] = 0;
            $input['pesan'] = 'Semester '.$data->nm_thn_ajaran.' berhasil di-non-aktifkan';
        }
        $data->fill($data->prepare($input))->save();
        alert()->success($input['pesan'])->persistent('OK');
        return redirect()->back();
    }
}
