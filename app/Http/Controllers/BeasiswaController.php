<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeasiswaController extends Controller
{
    public function __construct()
    {
        $profil = DB::table('pdrd.peserta_didik AS pd')
            ->join('pdrd.reg_pd AS tr','tr.id_pd','=','pd.id_pd')
            ->join('pdrd.sms AS tprodi','tprodi.id_sms','=','tr.id_sms')
            ->where('pd.soft_delete',0)
            ->where('pd.id_pd',config('mp.data_master.default_mhs'))
            ->first();
        return $this->profil = $profil;
    }

    public function biodata() {
        $profil = $this->profil;
        return view('beasiswa.profil',compact('profil'));
    }

    public function daftar_beasiswa() {
        $profil = $this->profil;
        $stat_beasiswa = 1;
        $beasiswa = [
            'PPA',
            'Beasiswa Djarum'
        ];
        return view('beasiswa.daftar_beasiswa',compact('profil','stat_beasiswa','beasiswa'));
    }

    public function daftar() {
        $profil = $this->profil;
        return view('beasiswa.daftar',compact('profil'));
    }
}
