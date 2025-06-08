<?php

namespace App\Models\Pdrd;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends AbstractionModel
{
    protected $table='pdrd.kurikulum';
    protected $primaryKey='id_kurikulum';

    public $timestamps = false;
    public $incrementing = false;

    public function matkul()
    {
        return $this->hasMany('App\Models\Pdrd\Matkul','id_kurikulum','id_kurikulum');
    }

    public function semester()
    {
        return $this->belongsTo('App\Models\Ref\Semester','id_smt','id_smt');
    }
}
