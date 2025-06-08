<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class JangkaWaktuPenyusunan extends AbstractionModel
{
    protected $table = 'kpta.jangka_waktu_penyusunan';
    protected $primaryKey = 'id_jangka_wkt';
}
