<?php

namespace App\Models\Ref;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class JalurMasuk extends AbstractionModel
{
    protected $table = 'ref.jalur_masuk';
    protected $primaryKey = 'id_jalur_masuk';
}
