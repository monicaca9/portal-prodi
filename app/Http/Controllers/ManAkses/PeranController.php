<?php

namespace App\Http\Controllers\ManAkses;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManAkses\PeranRequest;
use App\Models\ManAkses\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PeranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Peran::whereNull('expired_date')->OrderBy('id_peran','ASC')->get();
        return view('man_akses.peran.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Peran Baru',
            'route'         => 'manajemen_akses.peran.simpan',
            'backLink'      => 'manajemen_akses.peran',
            'form'          => 'man_akses.peran.create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PeranRequest $request)
    {
        $input = $request->all();
        $data = new Peran();
        $data->fill($data->prepare($input))->save();
        alert()->success('Berhasil menambahan peran '.$data->nm_peran)->persistent('OK');
        return redirect()->route('manajemen_akses.peran');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id_peran = Crypt::decrypt($id);
        $data = Peran::findorfail($id_peran);
        $menu = DB::SELECT("
            SELECT
                m.id_menu,
                m.nm_menu,
                m.nm_file,
                parent.nm_menu AS parent_menu,
                a.nm_aplikasi
            FROM man_akses.menu_role AS mr
            JOIN man_akses.menu AS m ON m.id_menu = mr.id_menu AND m.expired_date IS NULL
            JOIN man_akses.aplikasi AS a ON a.id_aplikasi = m.id_aplikasi AND a.expired_date IS NULL
            LEFT JOIN man_akses.menu AS parent ON parent.id_menu = m.id_group_menu AND parent.expired_date IS NULL
            WHERE mr.soft_delete=0
            AND mr.id_peran = '".$id_peran."'
            ORDER BY m.nm_file, m.urutan_menu ASC
        ");
        return view('man_akses.peran.show',compact('data','menu'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id_peran = Crypt::decrypt($id);
        $data = Peran::findorfail($id_peran);
        return view('__partial.form.edit',[
            'judul_halaman' => 'Ubah Peran',
            'route'         => 'manajemen_akses.peran.update',
            'backLink'      => 'manajemen_akses.peran',
            'form'          => 'man_akses.peran.edit',
            'data'          => $data,
            'id'            => $id_peran
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PeranRequest $request, $id)
    {
        $id = Crypt::decrypt($id);
        $input = $request->all();
        $data = Peran::findorfail($id);
        $data->fill($data->prepare($input));
        $data->save();
        alert()->success('Peran '.$input['nm_peran'].' berhasil diubah')->persistent('OK');
        return redirect()->route('manajemen_akses.peran');
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
        $data = Peran::findorfail($id);
        $data->drop();
        alert()->success('Peran '.$data->nm_peran.' berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }
}
