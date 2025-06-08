<?php

namespace App\Models\Dok;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class LargeObject extends AbstractionModel
{
    protected $table = 'dok.large_object';
    protected $primaryKey = 'id_blob';
}
