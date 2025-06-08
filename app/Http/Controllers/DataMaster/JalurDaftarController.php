<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Ref\JalurDaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class JalurDaftarController extends Controller
{
    protected $default_route = 'data_master.jalur_daftar';
    protected $default_view = 'data_master.jalur_daftar';
    protected $default_title = 'Jalur Daftar';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = JalurDaftar::whereNull('expired_date')->orderBy('nm_jalur_daftar','ASC')->get();
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
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah '.$this->default_title.' Baru',
            'route'         => $this->default_route.'.simpan',
            'backLink'      => $this->default_route,
            'form'          => $this->default_view.'.create',
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
        $data = new JalurDaftar();
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success($this->default_title.' '.$input['nm_jalur_daftar'].' berhasil ditambahkan')->persistent('OK');
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
        $data = JalurDaftar::find($id);
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah '.$this->default_title,
            'route'         => $this->default_route.'.update',
            'backLink'      => $this->default_route,
            'form'          => $this->default_view.'.edit',
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
        $data = JalurDaftar::find($id);
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success($this->default_title.' '.$input['nm_jalur_daftar'].' berhasil diubah')->persistent('OK');
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
        $data = JalurDaftar::find($id);
        $data->drop();
        alert()->success($this->default_title.' '.$data->nm_jalur_daftar.' berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }
}
