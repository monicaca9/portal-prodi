<?php

namespace App\Models\Kpta;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class NomorBaDaftarSeminar extends AbstractionModel
{
    protected $table = 'kpta.nomor_ba_daftar_seminar';
    protected $primaryKey = 'id_no_ba_daftar_seminar';
}
