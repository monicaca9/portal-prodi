<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\ManAkses\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pengaturan.password');
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
        $id_pengguna = Crypt::decrypt($id);
        $cari_pengguna = Login::findorfail($id_pengguna);
        if ($cari_pengguna->password!=sha1($request->pass_lama)) {
            alert()->error('Password yang anda masukkan salah','Silahkan coba lagi')->persistent('OK');
            return redirect()->back();
        }
        $this->validate($request, [
            'password'  => 'required|min:6|confirmed'
        ]);
        $input = $request->all();
        unset($input['pass_lama']);
        unset($input['password_confirmation']);
        Pengguna::where('id_pengguna',$id_pengguna)->update([
            'password'  => sha1($input['password'])
        ]);
        $request->session()->regenerate();

//        $cari_pengguna->fill($cari_pengguna->prepare($input))->save();

        alert()->success('Password berhasil diubah')->persistent('OK');
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
