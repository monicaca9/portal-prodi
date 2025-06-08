<?php

namespace App\Models\ManAkses;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class Aplikasi extends AbstractionModel
{
    protected $table = 'man_akses.aplikasi';
    protected $primaryKey = 'id_aplikasi';

    public $incrementing = false;
}
