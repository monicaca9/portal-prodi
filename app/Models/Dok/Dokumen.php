<?php

namespace App\Models\Dok;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends AbstractionModel
{
    protected $table = 'dok.dokumen';
    protected $primaryKey = 'id_dok';
    protected $keyType = 'string';
}
