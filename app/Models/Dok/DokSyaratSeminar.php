<?php

namespace App\Models\Dok;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class DokSyaratSeminar extends AbstractionModel
{
    protected $table = 'dok.dok_syarat_seminar';
    protected $primaryKey = 'id_dok_syarat_seminar';

    public $incrementing = false;
}
