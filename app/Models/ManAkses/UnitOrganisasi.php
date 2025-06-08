<?php

namespace App\Models\ManAkses;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class UnitOrganisasi extends AbstractionModel
{
    protected $table = 'man_akses.unit_organisasi';
    protected $primaryKey = 'id_organisasi';
}
