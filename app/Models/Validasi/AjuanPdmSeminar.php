<?php

namespace App\Models\Validasi;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class AjuanPdmSeminar extends AbstractionModel
{
    protected $table = 'validasi.ajuan_pdm_seminar';
    protected $primaryKey = 'id_ajuan_pdm_seminar';

    public $timestamps = false;
    public $incrementing = false;

    public function jenis_seminar()
    {
        return $this->belongsTo('App\Models\Ref\JenisSeminar','id_jns_seminar_lama','id_jns_seminar');
    }
}
