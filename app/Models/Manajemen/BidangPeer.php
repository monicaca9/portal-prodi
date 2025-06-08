<?php

namespace App\Models\Manajemen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class BidangPeer extends AbstractionModel
{
    protected $table = 'manajemen.bidang_peer';
    protected $primaryKey = 'id_bidang_peer';

    public $incrementing = false;

    public function kelbid()
    {
        return $this->belongsTo('App\Models\Ref\KelompokBidang','id_kel_bidang','id_kel_bidang');
    }
}
