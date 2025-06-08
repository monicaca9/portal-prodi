<?php

namespace App\Models\Ref;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class KelompokBidang extends AbstractionModel
{
    protected $table = 'ref.kelompok_bidang';
    protected $primaryKey = 'id_kel_bidang';
}
