<?php

namespace App\Models\SuratAktifKuliah;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SuratAktifKuliah\SuratAktif;
use App\Models\ManAkses\Peran;
use App\Models\Pdrd\Sdm;

class ValidasiSurat extends AbstractionModel
{
    use HasFactory;

    protected $table = 'surat_aktif.validasi_surat';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'submission_id',
        'role',
        'komentar',
        'status',
        'short_code',
        'id_creator',        // id_creator
        'tgl_create',        // tgl_create
        'last_update',        
        'updated_at',
        'updated_by',
        'soft_delete',       // soft_delete
        'last_sync',         // last_sync
    ];

    const CREATED_AT = 'tgl_create';
    public function studentActiveLetter()
    {
        return $this->belongsTo(SuratAktif::class, 'submission_id', 'id');
    }

    protected $casts = [
        'id_creator' => 'string',
        // 'updated_by' => 'string',
    ];

    public function createdBySdm()
{
    return $this->belongsTo(Sdm::class, 'id_creator', 'id_sdm');
}


    public function roleDetails()
    {
        return $this->belongsTo(Peran::class, 'role', 'id_peran');
    }
    protected static function booted()
    {
        static::creating(function ($model) {
            $sdm = Sdm::where('id_sdm', auth()->user()->id_sdm_pengguna)->first();

            if ($sdm) {
                $model->id_creator = $sdm->id_sdm;
            }
            $model->tgl_create = now();
            $model->last_sync = now();
        });

        static::updating(function ($model) {
            $sdm = Sdm::where('id_sdm', auth()->user()->id_sdm_pengguna)->first();

            if ($sdm) {
                $model->updated_by = $sdm->id_sdm;
            }
            $model->last_update = now();
            $model->last_sync = now();
        });
    }
}
