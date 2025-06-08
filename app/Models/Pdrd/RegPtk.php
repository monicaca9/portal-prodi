<?php

namespace App\Models\Pdrd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegPtk extends Model
{
    protected $table = 'pdrd.reg_ptk';
    protected $primaryKey = 'id_reg_ptk';

    public $incrementing = false;
    public $timestamps = false;

    public function Sdm()
    {
        return $this->belongsTo('App\Models\Pdrd\Sdm','id_sdm','id_sdm');
    }
}