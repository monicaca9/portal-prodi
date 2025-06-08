<?php

namespace App\Models\Ref;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class JenisBeasiswa extends AbstractionModel
{
    protected $table = 'ref.jenis_beasiswa';
    protected $primaryKey = 'id_jns_beasiswa';

    public function SumberDana()
    {
        return $this->belongsTo('App\Models\Ref\SumberDana','id_sumber_dana','id_sumber_dana');
    }
}
