<?php

namespace App\Http\Controllers\ManAkses;

use App\Http\Controllers\Controller;
use App\Models\ManAkses\Aplikasi;
use App\Models\ManAkses\Menu;
use App\Models\ManAkses\MenuRole;
use App\Models\ManAkses\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $aplikasi = Aplikasi::whereNull('expired_date')->orderBy('nm_aplikasi','ASC')->pluck('nm_aplikasi','id_aplikasi')->toArray();
        if ($request->has('app_kode')) {
            $app_kode = $request->get('app_kode');
            $data = Menu::whereNull('expired_date')->where('id_aplikasi',$app_kode)->orderBy('nm_menu','ASC')->get();
            return view('man_akses.menu.index',compact('aplikasi','app_kode','data'));
        } else {
            return view('man_akses.menu.index',compact('aplikasi'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->has('app_kode')) {
            $app_kode = $request->get('app_kode');
        } else {
            $app_kode = null;
        }
        $aplikasi = Aplikasi::whereNull('expired_date')->orderBy('nm_aplikasi','ASC')->pluck('nm_aplikasi','id_aplikasi')->toArray();
        $peran = Peran::whereNull('expired_date')->orderBy('nm_peran','ASC')->pluck('nm_peran','id_peran')->toArray();
        $parent_menu = Menu::whereNull('expired_date')->where('a_aktif',1)->where('a_tampil',1)->orderBy('nm_menu','ASC')->pluck('nm_menu','id_menu')->toArray();
        return view('__partial.form.create',[
            'judul_halaman' => 'Tambah Menu Baru',
            'route'         => 'manajemen_akses.menu.simpan',
            'backLink'      => 'manajemen_akses.menu',
            'form'          => 'man_akses.menu.create',
            'aplikasi'      => $aplikasi,
            'peran'         => $peran,
            'parent_menu'   => $parent_menu,
            'app_kode'      => $app_kode
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
        $menu = new Menu();
        $menu->fill($menu->prepare($input));
        $menu->save();

        $input['id_menu'] = $menu->id_menu;
        $role_menu = new MenuRole();
        $role_menu->fill($role_menu->prepare($input));
        $role_menu->save();

        alert()->success('Menu berhasil ditambahkan')->persistent('OK');
        return redirect(route('manajemen_akses.menu').'?app_kode='.$input['id_aplikasi']);
    }

    public function store_hak_menu(Request $request)
    {
        $input = $request->all();
        $role_menu = new MenuRole();
        $role_menu->fill($role_menu->prepare($input));
        $role_menu->save();

        alert()->success('Hak Akses Menu berhasil ditambahkan')->persistent('OK');
        return redirect()->back();
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
        $id_menu = Crypt::decrypt($id);
        $data = Menu::findorfail($id_menu);
        $aplikasi = Aplikasi::whereNull('expired_date')->orderBy('nm_aplikasi','ASC')->pluck('nm_aplikasi','id_aplikasi')->toArray();
        $parent_menu = Menu::whereNull('expired_date')->where('a_aktif',1)->where('a_tampil',1)->orderBy('nm_menu','ASC')->pluck('nm_menu','id_menu')->toArray();
        $list_peran = Peran::whereNull('expired_date')->orderBy('id_peran')->pluck('nm_peran','id_peran')->toArray();
        $list_hak_akses = MenuRole::with('peran')->where('id_menu',$id_menu)->where('soft_delete',0)->orderBy('tgl_create','DESC')->get();
        return view('man_akses.menu.edit',[
            'aplikasi'      => $aplikasi,
            'parent_menu'   => $parent_menu,
            'id'            => $id_menu,
            'data'          => $data,
            'list_peran'    => $list_peran,
            'hak_akses'     => $list_hak_akses
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
        $id_menu = Crypt::decrypt($id);
        $input = $request->all();
        $data = Menu::findorfail($id_menu);
        $data->fill($data->prepare($input))->save();
        alert()->success('Menu berhasil diubah')->persistent('OK');
        return redirect(route('manajemen_akses.menu').'?app_kode='.$data->id_aplikasi);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id_menu = Crypt::decrypt($id);
        $data = Menu::findorfail($id_menu);
        $data->drop();
        alert()->success('Menu berhasil dihapus')->persistent('OK');
        return redirect(route('manajemen_akses.menu').'?app_kode='.$data->id_aplikasi);
    }

    public function update_hak_menu(Request $request, $id,$id_peran)
    {
        $id_menu = Crypt::decrypt($id);
        $id_peran = Crypt::decrypt($id_peran);
        MenuRole::where('id_menu',$id_menu)->where('id_peran',$id_peran)->update([
            'a_boleh_insert'    => $request->has('a_boleh_insert')?1:0,
            'a_boleh_delete'    => $request->has('a_boleh_delete')?1:0,
            'a_boleh_update'    => $request->has('a_boleh_update')?1:0,
            'a_boleh_sanggah'   => $request->has('a_boleh_sanggah')?1:0,
            'last_update'       => currDateTime(),
            'id_updater'        => auth()->user()->id_pengguna
        ]);
        alert()->success('Hak Menu berhasil diupdate')->persistent('OK');
        return redirect()->back();
    }

    public function expired_hak_menu($id,$id_peran)
    {
        $id_menu = Crypt::decrypt($id);
        $id_peran = Crypt::decrypt($id_peran);
        $data = MenuRole::where('id_menu',$id_menu)->where('id_peran',$id_peran)->first();
        $data->drop();
        alert()->success('Hak Menu berhasil dihapus')->persistent('OK');
        return redirect()->back();
    }
}
