<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class SkorPerKomponen extends AbstractionModel
{
    protected $table = 'kpta.skor_per_komponen';
    protected $primaryKey = 'id_skor_komponen';
}
