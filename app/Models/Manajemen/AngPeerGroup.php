<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class AngPeerGroup extends AbstractionModel
{
    protected $table = 'manajemen.ang_peer_group';
    protected $primaryKey = 'id_ang_peer_group';

    public $incrementing = false;

    public function dosen()
    {
        return $this->belongsTo('App\Models\Pdrd\Sdm','id_sdm','id_sdm');
    }
}
