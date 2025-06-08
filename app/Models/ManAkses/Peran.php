<?php

namespace App\Models\ManAkses;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class Peran extends AbstractionModel
{
    protected $table = 'man_akses.peran';
    protected $primaryKey = 'id_peran';
    public $incrementing = false;
}
