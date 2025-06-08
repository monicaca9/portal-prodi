<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class Rps extends AbstractionModel
{
    protected $table='manajemen.rps';
    protected $primaryKey='id_rps';
    public $incrementing = false;
}
