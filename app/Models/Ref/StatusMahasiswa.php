<?php

namespace App\Models\Ref;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class StatusMahasiswa extends AbstractionModel
{
    protected $table = 'ref.status_mahasiswa';
    protected $primaryKey = 'id_stat_mhs';

    public $timestamps = false;
    public $incrementing = false;
}
