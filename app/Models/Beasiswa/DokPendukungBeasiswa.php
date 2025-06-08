<?php

namespace App\Models\Beasiswa;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class DokPendukungBeasiswa extends AbstractionModel
{
    protected $table = 'beasiswa.dok_pendukung_beasiswa';
    protected $primaryKey = 'id_dok_pendukung_beasiswa';
    protected $keyType = 'string';
}
