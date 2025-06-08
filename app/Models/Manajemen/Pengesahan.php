<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class Pengesahan extends AbstractionModel
{
    protected $table='manajemen.pengesahan';
    protected $primaryKey='id_pengesahan';
    public $incrementing = false;
}
