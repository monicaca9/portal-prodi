<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class DistribusiNilaiSeminar extends AbstractionModel
{
    protected $table = 'kpta.distribusi_nilai';
    protected $primaryKey = 'id_distribusi_nilai';

    public $incrementing = false;
}
