<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class NomorBaSeminar extends AbstractionModel
{
    protected $table = 'kpta.nomor_ba_seminar';
    protected $primaryKey = 'id_no_ba_seminar';

    public function nomorBa()
    {
        return $this->belongsTo('App\Models\Kpta\NomorBa', 'id_no_ba', 'id_no_ba');
    }
}

