<?php

namespace App\Models\Dok;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Model;

class DokAjuanDraftUsul extends AbstractionModel
{
    protected $table = 'dok.dok_ajuan_draft_usul';
    protected $primaryKey = 'id_dok_ajuan_draft_usul';
}
