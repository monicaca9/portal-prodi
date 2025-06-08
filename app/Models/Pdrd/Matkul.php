<?php

namespace App\Models\Pdrd;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    protected $table='pdrd.matkul';
    protected $primaryKey='id_mk';
    public $incrementing = false;
    public $timestamps = false;

    public function rps()
    {
        return $this->belongsTo('App\Models\Manajemen\MatkulRps','id_mk','id_mk');
    }

    public function Cpl()
    {
        return $this->belongsToMany('App\Models\Manajemen\Cpl','id_cpl','id_cpl');
    }
    public function KelasKuliah()
    {
        return $this->hasMany('App\Models\Pdrd\KelasKuliah','id_kls','id_kls');
    }


}
