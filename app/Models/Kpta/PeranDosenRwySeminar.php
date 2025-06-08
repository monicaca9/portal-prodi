<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class PeranDosenRwySeminar extends AbstractionModel
{
    protected $table = 'kpta.peran_dosen_rwy_seminar';
    protected $primaryKey = 'id_peran_dosen';

    public $timestamps = false;
}
