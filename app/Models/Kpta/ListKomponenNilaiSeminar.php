<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class ListKomponenNilaiSeminar extends AbstractionModel
{

    protected $table = 'kpta.list_komponen_nilai_seminar';
    protected $primaryKey = 'id_list_komponen_nilai';
    public $incrementing = false;

    public function komponen_nilai()
    {
        return $this->belongsTo('App\Models\Kpta\KomponenNilaiSeminar', 'id_komponen_nilai', 'id_komponen_nilai');
    }
}
