<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $file           = file_get_contents(base_path('.env'));
        $pos_driver     = strpos(file_get_contents(base_path('.env')), 'MAIL_DRIVER=');
        $pos_host       = strpos(file_get_contents(base_path('.env')), 'MAIL_HOST=');
        $pos_port       = strpos(file_get_contents(base_path('.env')), 'MAIL_PORT=');
        $pos_username   = strpos(file_get_contents(base_path('.env')), 'MAIL_USERNAME=');
        $pos_password   = strpos(file_get_contents(base_path('.env')), 'MAIL_PASSWORD=');
        $pos_encryption = strpos(file_get_contents(base_path('.env')), 'MAIL_ENCRYPTION=');
        $mail_server    = array(
            'driver'        => str_replace('MAIL_DRIVER=', '', substr($file, $pos_driver, (strpos($file, PHP_EOL, $pos_driver))-$pos_driver)),
            'host'          => str_replace('MAIL_HOST=', '', substr($file, $pos_host, (strpos($file, PHP_EOL, $pos_host))-$pos_host)),
            'port'          => str_replace('MAIL_PORT=', '', substr($file, $pos_port, (strpos($file, PHP_EOL, $pos_port))-$pos_port)),
            'username'      => str_replace('MAIL_USERNAME=', '', substr($file, $pos_username, (strpos($file, PHP_EOL, $pos_username))-$pos_username)),
            'password'      => str_replace('MAIL_PASSWORD=', '', substr($file, $pos_password, (strpos($file, PHP_EOL, $pos_password))-$pos_password)),
            'encryption'    => str_replace('MAIL_ENCRYPTION=', '', substr($file, $pos_encryption, (strpos($file, PHP_EOL, $pos_encryption))-$pos_encryption)),
        );
        return view('pengaturan.mail_server',compact('mail_server'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->input('driver'))
        {
            $env_drv    = file_get_contents(base_path('.env'));
            file_put_contents(base_path('.env'), str_replace('MAIL_DRIVER='.env('MAIL_DRIVER'), 'MAIL_DRIVER='.$request->input('driver'), $env_drv));
        }
        if($request->input('host'))
        {
            $env_hst    = file_get_contents(base_path('.env'));
            file_put_contents(base_path('.env'), str_replace('MAIL_HOST='.env('MAIL_HOST'), 'MAIL_HOST='.$request->input('host'), $env_hst));
        }
        if($request->input('port'))
        {
            $env_prt    = file_get_contents(base_path('.env'));
            file_put_contents(base_path('.env'), str_replace('MAIL_PORT='.env('MAIL_PORT'), 'MAIL_PORT='.$request->input('port'), $env_prt));
        }
        if($request->input('username'))
        {
            $env_usr    = file_get_contents(base_path('.env'));
            file_put_contents(base_path('.env'), str_replace('MAIL_USERNAME='.env('MAIL_USERNAME'), 'MAIL_USERNAME='.$request->input('username'), $env_usr));
        }
        if($request->input('password'))
        {
            $env_pss    = file_get_contents(base_path('.env'));
            file_put_contents(base_path('.env'), str_replace('MAIL_PASSWORD='.env('MAIL_PASSWORD'), 'MAIL_PASSWORD='.$request->input('password'), $env_pss));
        }
        if($request->input('encryption'))
        {
            $env_enc    = file_get_contents(base_path('.env'));
            file_put_contents(base_path('.env'), str_replace('MAIL_ENCRYPTION='.env('MAIL_ENCRYPTION'), 'MAIL_ENCRYPTION='.$request->input('encryption'), $env_enc));
        }

        flash()->success('Perubahan data mail server berhasil dilakukan.');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function testing(Request $request)
    {
        $email      = $request->email_target;
        try
        {
            Mail::send('pengaturan.test_mail_server', ['email' => $email], function ($mail) use ($email) {
                $mail->to($email)->subject('Testing Mail Server PORTAL PRODI');
            });
        }
        catch (\Exception $e)
        {

            var_dump($e->getMessage());die;
            $err1 = preg_match('/535/', $e->getMessage(), $matches, PREG_OFFSET_CAPTURE); //password/username salah
            $err2 = preg_match('/No such host is known/', $e->getMessage(), $matches, PREG_OFFSET_CAPTURE); //conn

            if($err2)
            {
                alert()->warning('<small><b>Terjadi kegagalan koneksi saat mengirim email.</b></small>')->persistent("OK")->html(true);
                return back();
            }
            elseif ($err1)
            {
                alert()->error('Username dan password akun mail server tidak diterima.', 'Email gagal terkirim!')->persistent("OK");
                return back();
            }
            else
            {
                alert()->error('Terdapat kesalahan pada pengaturan mail server.', 'Email gagal terkirim!')->persistent("OK");
                return back();
            }
        }
        alert()->success('Buka email <b>'.$email.'</b><br>untuk melihat hasil tes mail server.<br><br><small>*Jika tidak ada pada folder inbox, silahkan periksa pada folder spam</small>','Registrasi berhasil!')->persistent("OK")->html(true);
        return back();
    }
}
