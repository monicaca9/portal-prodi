<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class NilaiAkhirSeminar extends AbstractionModel
{
    protected $table = 'kpta.nilai_akhir_seminar';
    protected $primaryKey = 'id_nilai_akhir_seminar';
}
