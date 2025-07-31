<?php

namespace App\Models\SuratAktifKuliah;

use App\Models\AbstractionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SuratAktifKuliah\StudentActiveLetter;
use App\Models\ManAkses\Peran;
use App\Models\Pdrd\Sdm;

class LetterSignature extends AbstractionModel
{
    use HasFactory;

    protected $table = 'surat_aktif.letter_signatures';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'submission_id',
        'role',
        'notes',
        'status',
        'short_code',
        'created_by',        // id_creator
        'tgl_create',        // tgl_create
        'updated_by',        // id_updater
        'updated_at',        // last_update
        'soft_delete',       // soft_delete
        'last_sync',         // last_sync
    ];


    public function studentActiveLetter()
    {
        return $this->belongsTo(StudentActiveLetter::class, 'submission_id', 'id');
    }

    protected $casts = [
        'created_by' => 'string',
        'updated_by' => 'string',
    ];

    public function createdBySdm()
{
    return $this->belongsTo(Sdm::class, 'created_by', 'id_sdm');
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
                $model->created_by = $sdm->id_sdm;
            }
            $model->tgl_create = now();
            $model->last_sync = now();
        });

        static::updating(function ($model) {
            $sdm = Sdm::where('id_sdm', auth()->user()->id_sdm_pengguna)->first();

            if ($sdm) {
                $model->updated_by = $sdm->id_sdm;
            }
            $model->updated_at = now();
            $model->last_sync = now();
        });
    }
}
