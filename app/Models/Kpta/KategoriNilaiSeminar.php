<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class KategoriNilaiSeminar extends AbstractionModel
{
    protected $table = 'kpta.kategori_nilai_seminar';
    protected $primaryKey = 'id_kategori_nilai';
}
