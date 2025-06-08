<?php

namespace App\Models\Kpta\SeminarProdi;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class SisaWaktuPenyusunan extends AbstractionModel
{
    protected $table = 'kpta.sisa_waktu_penyusunan';
    protected $primaryKey = 'id_sisa_wkt';
}
