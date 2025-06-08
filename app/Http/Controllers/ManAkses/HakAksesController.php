<?php

namespace App\Http\Controllers\ManAkses;

use App\Http\Controllers\Controller;
use App\Models\ManAkses\Menu;
use App\Models\ManAkses\MenuRole;
use App\Models\ManAkses\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HakAksesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list_peran = Peran::whereNull('expired_date')->orderBy('id_peran','ASC')
            ->pluck('nm_peran','id_peran')->toArray();
        if ($request->has('peran_pilih')) {
            $peran_pilih = $request->get('peran_pilih');
            $data = MenuRole::menu_role_list($peran_pilih);
            return view('man_akses.hak_akses.index',compact('list_peran','peran_pilih','data'));
        } else {
            return view('man_akses.hak_akses.index',compact('list_peran'));
        }
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
        //
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
        //
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
