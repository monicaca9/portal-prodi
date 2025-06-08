<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class NomorBa extends AbstractionModel
{
    protected $table = 'kpta.nomor_ba';
    protected $primaryKey = 'id_no_ba';
}
