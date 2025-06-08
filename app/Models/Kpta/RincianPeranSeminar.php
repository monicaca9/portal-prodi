<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class RincianPeranSeminar extends AbstractionModel
{
    protected $table = 'kpta.rincian_peran_seminar';
    protected $primaryKey = 'id_rincian_peran_seminar';

    public $timestamps = false;
}
