<?php

namespace App\Models\Pdrd;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class RwySeminarPd extends AbstractionModel
{
    protected $table = 'pdrd.rwy_seminar_pd';
    protected $primaryKey = 'id_rwy_seminar';

    public $incrementing = false;

    public function jenisSeminar()
    {
        return $this->belongsTo('App\Models\Ref\JenisSeminar','id_jns_seminar','id_jns_seminar');
    }
}
