<?php

namespace App\Models\Beasiswa;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class VerDaftarBeasiswa extends AbstractionModel
{
    protected $table = 'beasiswa.ver_daftar_beasiswa';
    protected $primaryKey = 'id_ver_daftar_beasiswa';
    protected $keyType = 'string';
}
