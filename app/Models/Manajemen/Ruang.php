<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class Ruang extends AbstractionModel
{
    protected $table = 'manajemen.ruang';
    protected $primaryKey = 'id_ruang';

    public $incrementing = false;
}
