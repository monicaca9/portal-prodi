<?php

namespace App\Models\Pdrd;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class RegPd extends AbstractionModel
{
    protected $table = 'pdrd.reg_pd';
    protected $primaryKey = 'id_reg_pd';
    public $incrementing=false;

    public function prodi()
    {
        return $this->belongsTo('App\Models\Pdrd\Sms','id_sms','id_sms');
    }

    public function jenis_keluar()
    {
        return $this->belongsTo('App\Models\Ref\JenisKeluar','id_jns_keluar','id_jns_keluar');
    }
}
