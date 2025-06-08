<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AktivasiRequest;
use App\Models\ManAkses\Pengguna;
use App\Models\ManAkses\RolePengguna;
use App\Models\Pdrd\PesertaDidik;
use App\Models\Pdrd\RegPd;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index()
    {
        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(Request $request, PesertaDidik $pesertaDidik)
    {
        $this->validate($request,[
            'nim'           => 'required',
            'email'         => 'required | email | unique:pgsql.man_akses.pengguna,username,NULL,id,soft_delete,0'
        ]);
        $email = $request->email;
        $nim = $request->nim;
        $data = $pesertaDidik->cari_mahasiswa_daftar_akun($nim);
        if (is_null($data)) {
            alert()->warning('NIM/NPM Mahasiswa tidak bisa ditemukan')->persistent("OK")->html(true);
            return back();
        } else {
            if (!is_null($data->id_pengguna)) {
                alert()->warning('Mahasiswa dengan NIM/NPM '.$nim.' sudah pernah membuat akun')->persistent("OK")->html(true);
                return back();
            } else {
                $info_pd = $pesertaDidik->detail_mahasiswa($data->id_pd);
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
        }
    }

    public function show($id)
    {
        $data = Crypt::decrypt($id);
        if ($data['created']<=currDateTime() && $data['expired']>=currDateTime()) {
            $info = $data['info'];
            return view('auth.register_akun',compact('data','info'));
        } else {
            alert()->error('Kode verifikasi sudah expired, silahkan lakukan registrasi kembali melalui Admin Prodi','Proses Aktifasi Gagal')->persistent('OK');
            return redirect()->route('auth.login');
        }
    }

    public function active(AktivasiRequest $request, $id, PesertaDidik $pesertaDidik)
    {
        $data = Crypt::decrypt($id);
        $info = $data['info'];
        if ($data['created']<=currDateTime() && $data['expired']>=currDateTime()) {
            $input = $request->all();
            $pd = $pesertaDidik->id_detail_mahasiswa($info->id_pd);

            $data_pengguna  = [
                'username'      => $input['email'],
                'password'      => $input['password'],
                'nm_pengguna'   => $pd->nm_pd,
                'tempat_lahir'  => $pd->tmpt_lahir,
                'tgl_lahir'     => $pd->tgl_lahir,
                'jenis_kelamin' => $pd->jk,
                'a_aktif'       => 1,
                'id_pd_pengguna'=> $pd->id_pd
            ];

            $pengguna = new Pengguna();
            $pengguna->fill($pengguna->prepare($data_pengguna))->save();

            $data_peran = [
                'id_peran'      => 3005,
                'id_organisasi' => $pd->id_sms,
                'id_pengguna'   => $pengguna->id_pengguna,
                'approval_peran'=> 0
            ];
            $role_pengguna = new RolePengguna();
            $role_pengguna->fill($role_pengguna->prepare($data_peran))->save();

            alert()->success('Silahkan login menggunakan akun yang telah dibuat','Proses Aktifasi Berhasil')->persistent('OK');
            return redirect()->route('auth.login');
        } else {
            alert()->error('Kode verifikasi sudah expired, silahkan lakukan registrasi kembali melalui Admin Prodi','Proses Aktifasi Gagal')->persistent('OK');
            return redirect()->route('auth.login');
        }
    }
}
