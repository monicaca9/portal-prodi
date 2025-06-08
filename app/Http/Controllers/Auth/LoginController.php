<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\ManAkses\Menu;
use App\Models\ManAkses\RolePengguna;
use App\Models\ManAkses\UnitOrganisasi;
use App\Models\ManAkses\Pengguna;
use App\Models\Pdrd\PesertaDidik;
use App\Models\Pdrd\RegPd;
use App\Models\Pdrd\Sdm;
use App\Models\Pdrd\Sms;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use SSO\SSO;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function authenticate_sso()
    {
        if(SSO::authenticate()) {
            $username = SSO::getUser()->username;
            $nm_pengguna = SSO::getUser()->nm_pengguna;
            $email = SSO::getUser()->email;
            $a_aktif = SSO::getUser()->a_aktif;
            $emailrole = explode('@', $email);
            $cari = Login::where('username',$username)->where('soft_delete',0)->first();

            if (!is_null($cari)) {
                // dd(SSO::getUser());
                return $this->login_process($cari);
            } else {
                
                return view('auth.claim');
            }
        }
    }

    public function claim(request $request, PesertaDidik $pesertaDidik){
        $this->validate($request, [
            'nim' => 'required'
        ]);
        $url = ENV('URL_WS_ONEDATA');
        $token = $this->generate_token();
        $nim = $request->nim;
        $data = $pesertaDidik->cari_mahasiswa_daftar_akun($nim);
        
        if(!is_null($data)){
            
            $prodi = $data->id_sms;
            if($prodi == 'c4b67b31-fd42-4670-bcf0-541ff1c20ff7' || $prodi == 'fc4fc29a-85ca-47b3-8e61-3a9e9e129a88'){
                $param = json_encode([
                    'id_peserta_didik' => $data->id_pd
                ]);
                $response = curlApi('GET', $url . '/mahasiswa/detail', $param, $token);
                Session::put('responses', $response);
                $datas = $response['data'][0];

                return view('auth.verifysso', compact('datas'));
            }else{
                alert()->warning('NPM Mahasiswa Tersebut bukan mahasiswa Teknik Elektro')->persistent("OK");
                return redirect()->route('auth.claim');
                
            }
            
    }else {
        alert()->warning('NPM Mahasiswa Tersebut tidak ditemukan')->persistent("OK");
        return redirect()->route('auth.claim');
    }
        }
    

    public function verifysso() {
        if(SSO::authenticate()){
            // dd($datas);
            $username = SSO::getUser()->username;
            $nm_pengguna = SSO::getUser()->nm_pengguna;
            $email = SSO::getUser()->email;
            $a_aktif = SSO::getUser()->a_aktif;
            $emailrole = explode('@', $email);
            $cari = Login::where('username',$username)->where('soft_delete',0)->first();

            $url = ENV('URL_WS_ONEDATA');
            $token = $this->generate_token();
            
            
            // $param = json_encode([
            //     'id_peserta_didik' => $data->id_pd
            // ]);
            // $response = curlApi('GET', $url . '/mahasiswa/detail', $param, $token);
            $response = Session::get('responses');
            // dd($response);
            // dd(SSO::getUser()); 
            $id = guid();
            if ($response['status'] == true) {
                foreach($response['data'] as $each_data){

                        Pengguna::updateOrInsert([
                            'id_pengguna' => $id,
                        ],[
                            'username' => $username,
                            'password' => SHA1('unilajaya'),
                            'nm_pengguna' => $nm_pengguna,
                            'tempat_lahir' => $each_data['tmpt_lahir'],
                            'tgl_lahir' => $each_data['tgl_lahir'],
                            'no_tel' => $each_data['tlpn_rumah'],
                            'no_hp' => $each_data['tlpn_hp'],
                            'id_pd_pengguna' => $each_data['id_pd'],
                            'approval_pengguna' => 1,
                            'a_aktif' => $a_aktif,
                            'jenis_kelamin' => $each_data['jk'],
                            'tgl_create' => currDateTime(),
                            'last_update' => currDateTime(),
                            'last_sync' => currDateTime(),
                            'soft_delete' => 0,
                            'id_updater' => $id
                            ]);
      
                }
                RolePengguna::updateOrInsert([
                    'id_role_pengguna' => guid(),
                ],[
                    'id_peran' => $emailrole[1] == 'students.unila.ac.id' ? 3005 : 46,
                    'id_organisasi' => 'e2b705a7-173e-464a-9fac-509128709515', //
                    'id_pengguna' => $id,
                    'approval_peran' => 1,
                    'last_active' => currDateTime(),
                    'tgl_create' => currDateTime(),
                    'last_update' =>currDateTime(),
                    'id_updater' => $id,
                    'soft_delete' => 0,
                    'last_sync' => currDateTime()
        ]);

                return $this->login_process($cari);
        } elseif ($response['message'] == 'Gagal Otentikasi') {
            $token = $this->generate_token();
        } else {
            echo "Error Pesan = " . $response['message'] . "\n";
            
        }
                   
    }}
    
    
    function generate_token()
    {
        $url = ENV('URL_WS_ONEDATA');
        $token = generate_token_onedata('POST', $url . '/auth/login');

        $this->token = $token;
        return  $token;
    }

    public function authenticate(Request $input){
        $username   = $input['username'];
        $password   = $input['password'];
        $cari = Login::where('username',$username)->where('password',password_gen($password))->where('soft_delete',0)->first();
        if (!is_null($cari)) {
            return $this->login_process($cari);
        } else {
            alert()->error('Username dan Password tidak ditemukan','Silahkan coba kembali')->persistent('Coba lagi');
            return redirect()->back()->withInput(['username'=>$username]);
        }
    }

    private function login_process($cari)
    {
        if ($cari->a_aktif==1) {
            if (Auth::loginUsingId($cari->id_pengguna)) {
                $peran = RolePengguna::where('id_pengguna',Auth::user()->id_pengguna)->where('soft_delete',0)->orderBy('last_active','DESC')->first();
                $peran->last_active = currDateTime();
                $peran->last_update = currDateTime();
                $peran->id_updater  = Auth::user()->id_pengguna;
                $peran->save();

                Session::put('login.log_address', get_client_ip());
                Session::put('login.peran', $peran->toArray());
                $menu = Menu::generateMenu($peran->id_peran);
                Session::put('menu_user_manajemen', $menu);
                Session::put('menu_index','dashboard');

                $unit_organisasi = UnitOrganisasi::find($peran->id_organisasi);
                Session::put('login.unit_organisasi', $unit_organisasi->toArray());

                if (!is_null(Auth::user()->id_pd_pengguna)) {
                    $cari_pd = PesertaDidik::find(Auth::user()->id_pd_pengguna);
                    Session::put('login.info_pd', $cari_pd->toArray());
                    Session::put('login.a_mahasiswa', 1);
                } else {
                    Session::put('login.a_mahasiswa', 0);
                }

                if (!is_null(Auth::user()->id_sdm_pengguna)) {
                    $cari_sdm = Sdm::find(Auth::user()->id_sdm_pengguna);
                    Session::put('login.info_sdm', $cari_sdm->toArray());
                    Session::put('login.a_sdm', 1);
                } else {
                    Session::put('login.a_sdm', 0);
                }

                if (is_null(Auth::user()->id_pd_pengguna) && is_null(Auth::user()->id_sdm_pengguna)) {
                    Session::put('login.a_operator', 1);
                } else {
                    Session::put('login.a_operator', 0);
                }

                if ($peran->id_peran==3005 && $peran->approval_peran==0) {
                    alert()->success('Silahkan lengkapi biodata anda terlebih dahulu','Selamat Datang '.Auth::user()->nm_pengguna)->persistent('OK');
                    return redirect()->route('biodata.ubah');
                } else {
                    alert()->success(Auth::user()->nm_pengguna, 'Selamat Datang')->persistent("OK");
                    return redirect()->intended('/');
                }
            } else {
                alert()->error('Login gagal')->persistent('Coba lagi');
                return redirect()->back()->withInput(['username'=>$username]);
            }
        } else {
            alert()->error('Harap hubungi administrator untuk mengaktifkannya kembali','Pengguna tidak aktif')->persistent('Coba lagi');
            return redirect()->back();
        }
    }

    public function logout(){
        if(auth()->check()) {
            SSO::cookieClear(); //CLEAR COOKIE LARAVEL
            // SSO::ciCookieClear(); //CLEAR COOKIE CI
            Session::flush();
            Auth::logout();
            alert()->success('Berhasil logout');
            return redirect('auth/login')->with('pesan', 'berhasil logout');
        } else {
            return redirect('auth/login');
        }
    }
}
