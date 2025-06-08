<?php

namespace App\Models\Manajemen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CplMk extends Model
{
    protected $table = 'manajemen.cpl_mk';
    protected $primaryKey = 'id_cpl_mk';

    public $incrementing = false;
    public $timestamps = false;

    public function Cpl()
    {
        return $this->belongsTo('App\Models\Manajemen\Cpl','id_cpl','id_cpl');
    }
}
