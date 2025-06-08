<?php

namespace App\Models\Beasiswa;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class SyaratBeasiswa extends AbstractionModel
{
    protected $table = 'beasiswa.syarat_beasiswa';
    protected $primaryKey = 'id_syarat_beasiswa';
    protected $keyType = 'string';
}
