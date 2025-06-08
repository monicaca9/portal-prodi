<?php

namespace App\Models\Pdrd;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class SatuanPendidikan extends AbstractionModel
{
    protected $table = 'pdrd.satuan_pendidikan';
    protected $primaryKey = 'id_sp';

    public function list_sp_form_ptn()
    {
        $data = $this->where('soft_delete',0)->where('stat_sp','A')
            ->where('id_pembina','dee10ab0-e4de-42e1-b3f5-9d1e723761ac')
            ->orderBy('nm_lemb','ASC')->pluck('nm_lemb','id_sp')->toArray();
        return $data;
    }
}
