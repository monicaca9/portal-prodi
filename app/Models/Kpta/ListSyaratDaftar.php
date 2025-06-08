<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class ListSyaratDaftar extends AbstractionModel
{
    protected $table = 'kpta.list_syarat_daftar';
    protected $primaryKey = 'id_list_syarat_daftar';

    public $incrementing = false;
}
