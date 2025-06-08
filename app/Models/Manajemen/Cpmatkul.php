<?php

namespace App\Models\Manajemen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpmatkul extends Model
{
    protected $table = 'manajemen.cpmatkul';
    protected $primaryKey = 'id_cpmk';

    public $incrementing = false;

    public function matkul()
    {
        return $this->belongsTo('App\Models\Pdrd\Matkul','id_mk','id_mk');
    }
}
