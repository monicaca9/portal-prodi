<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class AngTpmps extends AbstractionModel
{
    protected $table = 'manajemen.ang_tpmps';
    protected $primaryKey = 'id_ang_tpmps';

    public $incrementing = false;

    public function dosen()
    {
        return $this->belongsTo('App\Models\Pdrd\Sdm','id_sdm','id_sdm');
    }
}
