<?php

namespace App\Models\Ref;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class Semester extends AbstractionModel
{
    protected $table = 'ref.semester';
    protected $primaryKey = 'id_smt';
}
