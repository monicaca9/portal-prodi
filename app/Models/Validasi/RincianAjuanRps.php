<?php

namespace App\Models\Validasi;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class RincianAjuanRps extends AbstractionModel
{
    protected $table='validasi.rincian_ajuan_rps';
    protected $primaryKey='id_rincian_ajuan_rps';

    public $incrementing = false;
}
