<?php

namespace App\Models\Dokumen;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class DokAjuanSeminar extends AbstractionModel
{
    protected $table = 'dok.dok_ajuan_seminar';
    protected $primaryKey = 'id_dok_ajuan_seminar';

    public $timestamps = false;
    public $incrementing = false;
}
