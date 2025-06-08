<?php

namespace App\Models\Validasi;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class AjuanRps extends AbstractionModel
{
    protected $table='validasi.ajuan_rps';
    protected $primaryKey='id_ajuan_rps';

    public $incrementing = false;
}
