<?php

namespace App\Models\Beasiswa;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class DokDaftarBeasiswa extends AbstractionModel
{
    protected $table = 'beasiswa.dok_daftar_beasiswa';
    protected $primaryKey = 'id_dok_daftar_beasiswa';
    protected $keyType = 'string';
}
