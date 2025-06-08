<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KonsentrasiProdiPd extends AbstractionModel
{
    protected $table = 'kpta.konsentrasi_prodi_pd';
    protected $primaryKey = 'id_konsentrasi_prodi_pd';
}
