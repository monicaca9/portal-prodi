<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Ref\JenisSeminar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class JenisSeminarController extends Controller
{
    protected $default_route = 'data_master.jenis_seminar';
    protected $default_view = 'data_master.jenis_seminar';
    protected $default_title = 'Jenis Seminar';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = JenisSeminar::where('a_seminar',1)->whereNull('expired_date')->orderBy('nm_jns_seminar','ASC')->get();
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
        // $jenis_seminar = JenisSeminar::whereNull('expired_date')->where('a_seminar',0)->orderBy('nm_jns_seminar','ASC')->pluck('nm_jns_seminar','id_jns_seminar')->toArray();
        $jenis_seminar = JenisSeminar::select('id_jns_seminar','nm_jns_seminar')->where('a_seminar',1)->whereNull('expired_date')->orderBy('nm_jns_seminar','ASC')->pluck('nm_jns_seminar','id_jns_seminar')->toarray();
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah '.$this->default_title.' Baru',
            'route'         => $this->default_route.'.simpan',
            'backLink'      => $this->default_route,
            'form'          => $this->default_view.'.create',
            'jenis_seminar' => $jenis_seminar
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
        $search_all = JenisSeminar::count();
        $input['id_jns_seminar']    = $search_all+1;
        $data = new JenisSeminar();
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success($this->default_title.' '.$input['nm_jns_seminar'].' berhasil ditambahkan')->persistent('OK');
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
        $data = JenisSeminar::find($id);
        $jenis_seminar = JenisSeminar::select('id_jns_seminar','nm_jns_seminar')->where('a_seminar',1)->whereNull('expired_date')->orderBy('nm_jns_seminar','ASC')->pluck('nm_jns_seminar','id_jns_seminar')->toarray();
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah '.$this->default_title,
            'route'         => $this->default_route.'.update',
            'backLink'      => $this->default_route,
            'form'          => $this->default_view.'.edit',
            'data'          => $data,
            'id'            => $id,
            'jenis_seminar' => $jenis_seminar
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
        $data = JenisSeminar::find($id);
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success($this->default_title.' '.$input['nm_jns_seminar'].' berhasil diubah')->persistent('OK');
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
        $data = JenisSeminar::find($id);
        $data->drop();
        alert()->success($this->default_title.' '.$data->nm_jns_seminar.' berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }
}
