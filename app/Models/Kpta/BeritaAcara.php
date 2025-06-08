<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends AbstractionModel
{
    protected $table = 'kpta.berita_acara';
    protected $primaryKey = 'id_berita_acara';

}
