<?php

namespace App\Models\Dok;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class DokSyaratDaftar extends AbstractionModel
{
    protected $table = 'dok.dok_syarat_daftar';
    protected $primaryKey = 'id_dok_syarat_daftar';

    public $incrementing = false;
}
