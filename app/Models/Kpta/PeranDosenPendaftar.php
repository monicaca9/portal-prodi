<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class PeranDosenPendaftar extends AbstractionModel
{
    protected $table = 'kpta.peran_dosen_pendaftar';
    protected $primaryKey = 'id_peran_dosen_pendaftar';

    public $incrementing = false;
}
