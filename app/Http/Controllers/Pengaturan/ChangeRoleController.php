<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use App\Models\ManAkses\Menu;
use App\Models\ManAkses\RolePengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class ChangeRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $daftar_peran = RolePengguna::list_peran_pengguna(auth()->user()->id_pengguna);
        return view('pengaturan.ubah_peran',compact('daftar_peran'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id_role_pengguna = $request->id_role_pengguna;
        $peran = RolePengguna::where('id_role_pengguna',$id_role_pengguna)
            ->where('id_pengguna',Auth::user()->id_pengguna)
            ->where('soft_delete',0)
            ->first();
        $peran->last_active = currDateTime();
        $peran->last_update = currDateTime();
        $peran->id_updater  = Auth::user()->id_pengguna;
        $peran->save();
        Session::put('login.peran', $peran->toArray());
        $menu = Menu::generateMenu($peran->id_peran);
        Session::put('menu_user_manajemen', $menu);
        Session::put('menu_index','dashboard');
        return redirect()->intended('/');
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
        //
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
        $id_peran = Crypt::decrypt($id);
        $menu = Menu::generateMenu($id_peran);
        Session::put('menu_user_manajemen', $menu);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
