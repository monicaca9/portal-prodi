<?php

namespace App\Models\manajemen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cpl extends Model
{
    protected $table = 'manajemen.cpl';
    protected $primaryKey = 'id_cpl';

    public $incrementing = false;
    
    public function CplMk()
    {
        return $this->belongsToMany('App\Models\Pdrd\Matkul','id_mk','id_mk');
    }
}
