<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class RincianRps extends AbstractionModel
{
    protected $table='manajemen.rincian_rps';
    protected $primaryKey='id_rincian_rps';
    public $incrementing = false;
}
