<?php

namespace App\Models\Validasi;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class VerAjuanDraftUsul extends AbstractionModel
{
    protected $table = 'validasi.ver_ajuan_draft_usul';
    protected $primaryKey = 'id_ver_ajuan';

    public $timestamps = false;
    public $incrementing = false;

    public function ajuanDraftUsul()
    {
        return $this->belongsTo('App\Models\Validasi\AjuanDraftUsul','id_ajuan_draft_usul','id_ajuan_draft_usul')->where('soft_delete',0);
    }

}
