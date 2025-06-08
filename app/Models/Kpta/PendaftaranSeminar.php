<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class PendaftaranSeminar extends AbstractionModel
{
    protected $table = 'kpta.pendaftaran_seminar';
    protected $primaryKey = 'id_daftar_seminar';

    public $incrementing = false;

    public function SeminarProdi()
    {
        return $this->belongsTo('App\Models\Kpta\SeminarProdi','id_seminar_prodi','id_seminar_prodi');
    }

    public function RegPd()
    {
        return $this->belongsTo('App\Models\Pdrd\RegPd','id_reg_pd','id_reg_pd');
    }
}
