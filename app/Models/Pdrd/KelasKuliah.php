<?php

namespace App\Models\Pdrd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AbstractionModel;

class KelasKuliah extends Model
{
    protected $table='pdrd.kelas_kuliah';
    protected $primaryKey='id_kls';
    public $incrementing = false;   

    public function matkul()
    {
        return $this->belongsTo('App\Models\Pdrd\Matkul','id_mk','id_mk');
    }

    public function AktAjarDosen()
    {
        return $this->hasMany('App\Models\Pdrd\AktAjarDosen','id_ajar','id_ajar');
    }
}
