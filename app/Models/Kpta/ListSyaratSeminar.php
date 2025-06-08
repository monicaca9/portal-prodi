<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class ListSyaratSeminar extends AbstractionModel
{
    protected $table = 'kpta.list_syarat_seminar';
    protected $primaryKey = 'id_list_syarat';

    public $incrementing = false;

    public function syarat()
    {
        return $this->belongsTo('App\Models\Kpta\SyaratSeminar','id_syarat_seminar','id_syarat_seminar');
    }
}
