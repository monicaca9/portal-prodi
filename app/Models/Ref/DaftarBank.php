<?php

namespace App\Models\Ref;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class DaftarBank extends AbstractionModel
{
    protected $table = 'ref.daftar_bank';
    protected $primaryKey = 'id_bank';
}
