<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\DB;
use Auth, Alert;

class MahasiswaMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect('/');
            }
        }
        // dd(session()->get('login.peran.approval_peran'));
        if (session()->get('login.peran.id_peran')==3005 && session()->get('login.peran.approval_peran')==1) {

            return $next($request);
        } elseif(session()->get('login.peran.id_peran')==3005 && session()->get('login.peran.approval_peran')==0) {
            alert()->error('Silahkan lengkapi biodata anda terlebih dahulu','Maaf')->persistent('OK');
            return redirect()->route('biodata.ubah');
        } else {
            alert()->error('Maaf, Anda tidak mempunyai akses untuk membuka halaman tersebut.');
            return redirect()->back();
        }
    }
}
