<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class MatkulRps extends AbstractionModel
{
    protected $table='manajemen.matkul_rps';
    protected $primaryKey='id_mk_rps';
    public $incrementing = false;
}
