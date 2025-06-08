<?php

namespace App\Models\Pdrd;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sms extends AbstractionModel
{
    protected $table = 'pdrd.sms';
    protected $primaryKey = 'id_sms';

    public function jenjang()
    {
        return $this->belongsTo('App\Models\Ref\JenjangPendidikan','id_jenj_didik','id_jenj_didik');
    }
}
