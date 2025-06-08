<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class AvgSkorKomponen extends  AbstractionModel
{
    protected $table = 'kpta.avg_skor_komponen';
    protected $primaryKey = 'id_avg_skor_komponen';
}
