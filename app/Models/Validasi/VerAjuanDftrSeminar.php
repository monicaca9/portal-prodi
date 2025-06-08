<?php

namespace App\Models\Validasi;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class VerAjuanDftrSeminar extends AbstractionModel
{
    protected $table = 'validasi.ver_ajuan_dft_seminar';
    protected $primaryKey = 'id_ver_ajuan';

    public $incrementing = false;
}
