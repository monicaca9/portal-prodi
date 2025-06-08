<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class SyaratSeminar extends AbstractionModel
{
    protected $table = 'kpta.syarat_seminar';
    protected $primaryKey = 'id_syarat_seminar';
}
