<?php

namespace App\Models\Manajemen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarPustaka extends Model
{
    protected $table = 'manajemen.daftar_pustaka_mk';
    protected $primaryKey = 'id_daftar_pustaka_mk';

    public $incrementing = false;

}
