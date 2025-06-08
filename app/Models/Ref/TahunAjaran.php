<?php

namespace App\Models\Ref;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends AbstractionModel
{
    protected $table = 'ref.tahun_ajaran';
    protected $primaryKey = 'id_thn_ajaran';
}
