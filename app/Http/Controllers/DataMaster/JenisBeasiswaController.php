<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Ref\JenisBeasiswa;
use App\Models\Ref\SumberDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class JenisBeasiswaController extends Controller
{
    protected $default_route = 'data_master.jenis_beasiswa';
    protected $default_view = 'data_master.jenis_beasiswa';
    protected $default_title = 'Jenis Beasiswa';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = JenisBeasiswa::whereNull('expired_date')->orderBy('nm_jns_beasiswa','ASC')->get();
        $route_default = $this->default_route;
        $title_default = $this->default_title;
        return view($this->default_view.'.index',compact('data','route_default','title_default'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sumber_dana = SumberDana::where('u_beasiswa',1)->whereNull('expired_date')->pluck('nm_sumber_dana','id_sumber_dana')->toArray();
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah '.$this->default_title.' Baru',
            'route'         => $this->default_route.'.simpan',
            'backLink'      => $this->default_route,
            'form'          => $this->default_view.'.create',
            'sumber_dana'   => $sumber_dana
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
        $data = new JenisBeasiswa();
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success($this->default_title.' '.$input['nm_jns_beasiswa'].' berhasil ditambahkan')->persistent('OK');
        return redirect()->route($this->default_route);
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
        $id = Crypt::decrypt($id);
        $data = JenisBeasiswa::find($id);
        $sumber_dana = SumberDana::where('u_beasiswa',1)->whereNull('expired_date')->pluck('nm_sumber_dana','id_sumber_dana')->toArray();
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah '.$this->default_title,
            'route'         => $this->default_route.'.update',
            'backLink'      => $this->default_route,
            'form'          => $this->default_view.'.edit',
            'data'          => $data,
            'id'            => $id,
            'sumber_dana'   => $sumber_dana
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
        $data = JenisBeasiswa::find($id);
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success($this->default_title.' '.$input['nm_jns_beasiswa'].' berhasil diubah')->persistent('OK');
        return redirect()->route($this->default_route);
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
        $data = JenisBeasiswa::find($id);
        $data->drop();
        alert()->success($this->default_title.' '.$data->nm_jns_beasiswa.' berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }
}
