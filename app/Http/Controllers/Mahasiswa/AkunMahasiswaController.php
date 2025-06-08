<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pdrd\PesertaDidik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AkunMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, PesertaDidik $pesertaDidik)
    {
        if ($request->has('nim')) {
            $nim = $request->get('nim');
            $data = $pesertaDidik->cari_daftar_akun_mahasiswa($nim);
            return view('akun_mahasiswa.index',compact('nim','data'));
        } else {
            return view('akun_mahasiswa.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id, PesertaDidik $pesertaDidik)
    {
        $id_pd = Crypt::decrypt($id);
        $data = $pesertaDidik->detail_mahasiswa($id_pd);
        return view('__partial.form.create',[
            'judul_halaman' => 'Buat Akun Mahasiswa Baru',
            'route'         => 'akun_mahasiswa.simpan',
            'backLink'      => 'akun_mahasiswa',
            'form'          => 'akun_mahasiswa.create',
            'data'          => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, PesertaDidik $pesertaDidik)
    {
        $info_pd = $pesertaDidik->detail_mahasiswa($request->id_pd);
        $email = $request->email;
        $this->validate($request,[
            'email'         => 'required | email | unique:pgsql.man_akses.pengguna,username,NULL,id,soft_delete,0'
        ]);
        $waktu_expired = date("Y-m-d H:i:s", strtotime("+30 minutes"));
        $data = [
            'email'     => $email,
            'info'      => $info_pd,
            'created'   => currDateTime(),
            'expired'   => $waktu_expired
        ];
        try
        {
            Mail::send('auth.register_mail', ['data' => $data,'info_pd'=>$info_pd], function ($mail) use ($data, $email, $info_pd) {
                $mail->to($email)->subject('Aktivasi Akun Portal Prodi');
            });
        }
        catch (\Exception $e)
        {
            $err1 = preg_match('/535/', $e->getMessage(), $matches, PREG_OFFSET_CAPTURE); //password/username salah
            $err2 = preg_match('/No such host is known/', $e->getMessage(), $matches, PREG_OFFSET_CAPTURE); //conn

            if($err2)
            {
                $pesan_error = 'Terjadi kegagalan koneksi saat mengirim email';
                alert()->warning('<small><b>Terjadi kegagalan koneksi saat mengirim email.</b></small>')->persistent("OK")->html(true);
            }
            elseif ($err1)
            {
                $pesan_error = 'Username dan password akun mail server tidak diterima';
                alert()->error('Username dan password akun mail server tidak diterima.', 'Email gagal terkirim!')->persistent("OK");
            }
            else
            {
                $pesan_error = 'Terdapat kesalahan pada pengaturan mail server';
                alert()->error('Terdapat kesalahan pada pengaturan mail server.', 'Email gagal terkirim!')->persistent("OK");
            }
            DB::table('log_mail_error')->insert([
                'id_log_mail_error' => guid(),
                'email'             => $email,
                'time_request'      => currDateTime(),
                'message'           => $pesan_error,
                'a_sukses'          => 0
            ]);
            return back();
        }
        DB::table('log_mail_error')->insert([
            'id_log_mail_error' => guid(),
            'email'             => $email,
            'time_request'      => currDateTime(),
            'message'           => 'Mengirimkan email untuk register akun dengan NIM/NPM Mahasiswa '.$nim,
            'a_sukses'          => 1
        ]);
        alert()->success('Buka email <b>'.$email.'</b><br>untuk melakukan aktivasi akun.<br><br><small>*Jika tidak ada pada folder inbox, silahkan periksa pada folder spam</small>','Registrasi berhasil!')->persistent("OK")->html(true);
        return back();
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
