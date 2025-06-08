<?php

namespace App\Http\Controllers\ManajemenLab;

use App\Http\Controllers\Controller;
use App\Models\Manajemen\Gedung;
use App\Models\ManLab\Laboratorium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class DaftarLabController extends Controller
{
    public function __construct()
    {
        $this->judul_page = 'Laboratorium';
        $this->route_base = 'manajemen_lab.daftar_lab';
        $this->view_base = 'manajemen_lab.daftar_lab';
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Laboratorium::getAllLaboratorium();
        return view($this->view_base.'.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $prodi = GetProdiIndividu();
        $gedung = Gedung::get_list_gedung();
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Laboratorium Baru',
            'route'         => $this->route_base.'.simpan',
            'backLink'      => $this->route_base,
            'form'          => $this->view_base.'.create',
            'prodi'         => $prodi,
            'list_gedung'   => $gedung
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $data = new Laboratorium();
        $data->fill($data->prepare($input))->save();
        return redirect()->route($this->route_base);
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
        $id_lab = Crypt::decrypt($id);
        $prodi = GetProdiIndividu();
        $gedung = Gedung::get_list_gedung();
        $data = Laboratorium::find($id_lab);
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah Laboratorium Baru',
            'route'         => $this->route_base.'.update',
            'backLink'      => $this->route_base,
            'form'          => $this->view_base.'.edit',
            'prodi'         => $prodi,
            'list_gedung'   => $gedung,
            'data'          => $data,
            'id'            => $data->id_lab
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id_lab = Crypt::decrypt($id);
        $input = $request->all();
        $data = Laboratorium::find($id_lab);
        $data->fill($data->prepare($input))->save();
        return redirect()->route($this->route_base);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
