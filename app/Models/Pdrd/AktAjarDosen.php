<?php

namespace App\Models\pdrd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AbstractionModel;

class AktAjarDosen extends AbstractionModel
{
    protected $table='pdrd.akt_ajar_dosen';
    protected $primaryKey='id_ajar';

    public function KelasKuliah()
    {
        return $this->belongsTo('App\Models\Pdrd\KelasKuliah','id_kls','id_kls');
    }
}
