<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class ListKategoriNilaiSeminar extends AbstractionModel
{
    protected $table = 'kpta.list_kategori_nilai_seminar';
    protected $primaryKey = 'id_list_kategori_nilai';

    public $incrementing = false;

    public function kategori_nilai()
    {
        return $this->belongsTo('App\Models\Kpta\KategoriNilaiSeminar','id_kategori_nilai','id_kategori_nilai');
    }}
