<?php

namespace App\Models\Validasi;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class AjuanDraftUsul extends AbstractionModel
{
    protected $table = 'validasi.ajuan_draft_usul';
    protected $primaryKey = 'id_ajuan_draft_usul';

    public $incrementing = false;

}
