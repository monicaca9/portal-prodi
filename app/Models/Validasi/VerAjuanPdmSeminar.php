<?php

namespace App\Models\Validasi;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class VerAjuanPdmSeminar extends AbstractionModel
{
    protected $table = 'validasi.ver_ajuan_pdm_seminar';
    protected $primaryKey = 'id_ver_ajuan';

    public $timestamps = false;
    public $incrementing = false;

    public function ajuanSeminar()
    {
        return $this->belongsTo('App\Models\Validasi\AjuanPdmSeminar','id_ajuan_pdm_seminar','id_ajuan_pdm_seminar')->where('soft_delete',0);
    }
}
