<?php

namespace App\Models\Beasiswa;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class PendaftarBeasiswa extends AbstractionModel
{
    protected $table = 'beasiswa.pendaftar_beasiswa';
    protected $primaryKey = 'id_daftar_beasiswa';
    protected $keyType = 'string';
}
