<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Manajemen\Gedung;
use App\Models\Manajemen\Ruang;
use App\Models\Kpta\PendaftaranSeminar;
use Illuminate\Support\Facades\Crypt;
use App\Models\Pdrd\PesertaDidik;
use App\Models\Pdrd\RegPd;
use Carbon\Carbon;

class JadwalSeminarController extends Controller
{
    public function index(){
        $prodi = GetProdiIndividu();
        $datajadwal =  PendaftaranSeminar::all();
    //    $data = Gedung::where('id_sms',$prodi->id_induk_sms)->where('soft_delete',0)->orderBy('nm_gedung','ASC')->get();
       // $datajadwal =  '<pre>'.print_r($datajadwal).'</pre>'
        $dataTanggal = [];
  //      dd(PesertaDidik::where('id_pd','75211589-ff05-4fc5-867a-fbdb062846ac')->get());
        //$dataMaster = ;
        foreach($datajadwal as $jadwal){
            $ruang = Ruang::where('id_ruang',$jadwal->id_ruang)->first();
            if($ruang != null){
                $reg_pd = RegPd::where('id_reg_pd',$jadwal->id_reg_pd)->first();
              //  dd($datajadwal,$jadwal,["perserta"=>$pd]);
                if($reg_pd != null){
                    $pd = PesertaDidik::where('id_pd',$reg_pd->id_pd)->first();
                    $gedung = Gedung::where('id_gedung',$ruang->id_gedung)->first();
                    $dataseminar = [
                        "title" => $pd->nm_pd ." ".$gedung->nm_gedung ." ".$ruang->nm_ruang,
                        "start" => Carbon::parse($jadwal->tgl_mulai." ".config('mp.data_master.waktu')[$jadwal->waktu]),
                        "end" => Carbon::parse($jadwal->tgl_mulai." ".config('mp.data_master.waktu')[$jadwal->waktu + 36])
                    ];
                    array_push($dataTanggal,$dataseminar);
                }
            }
        }
        return  json_encode($dataTanggal);
    }
}
