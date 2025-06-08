<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class KonsentrasiProdi extends AbstractionModel
{
    protected $table = 'kpta.konsentrasi_prodi';
    protected $primaryKey = 'id_konsentrasi_prodi';
}
