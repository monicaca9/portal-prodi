<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class KomponenNilaiSeminar extends AbstractionModel
{
    protected $table = 'kpta.komponen_nilai_seminar';
    protected $primaryKey = 'id_komponen_nilai';
}
