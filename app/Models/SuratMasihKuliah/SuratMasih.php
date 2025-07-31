<?php

namespace App\Models\SuratMasihKuliah;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AbstractionModel;
use App\Models\Pdrd\PesertaDidik;
use App\Models\SuratMasihKuliah\ValidasiSurat;
use App\Models\SuratMasihKuliah\NomorSurat;

class SuratMasih extends AbstractionModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_masih.surat_masih';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama',
        'npm',
        'jurusan',
        'prodi',
        'semester',
        'thn_akademik',
        'no_hp',
        'alamat',
        'tujuan',
        'nama_ortu',
        'nip_ortu',
        'pangkat_ortu',
        'pekerjaan_ortu',
        'instansi_ortu',
        'alamat_ortu',
        'validasi',
        'dosen_pa',
        'dokumen',
        'dokumen2',
        'status',
        'id_validasi_admin',
        'id_validasi_pa',
        'id_validasi_kaprodi',
        'id_validasi_kajur',
        'no_surat',
        
        'id_creator',        // id_creator
        'tgl_create',        // tgl_create
        'last_update',        
        'updated_at',  
        'updated_by',     
        'soft_delete',       // soft_delete
        'last_sync',         // last_sync
    ];
    const CREATED_AT = 'tgl_create';

    const STATUS_PENDING = 'menunggu';
    const STATUS_APPROVED = 'disetujui';
    const STATUS_REJECTED = 'ditolak';
    const STATUS_PROCESSED = 'proses';
    const STATUS_DONE = 'selesai';

    public function createdBy()
    {
        return $this->belongsTo(PesertaDidik::class, 'id_creator', 'id_pd');
    }

    public function letterNumber()
    {
        return $this->belongsTo(NomorSurat::class, 'no_surat', 'id');
    }


    public function adminValidation()
    {
        return $this->hasOne(ValidasiSurat::class, 'submission_id', 'id')
            ->where('role', 6);
    }

    public function advisorSignature()
    {
        return $this->hasOne(ValidasiSurat::class, 'submission_id', 'id')
            ->where('role', 46);
    }

    public function headOfProgramSignature()
    {
        return $this->hasOne(ValidasiSurat::class, 'submission_id', 'id')
            ->where('role', 3000);
    }

    public function headOfDepartmentSignature()
    {
        return $this->hasOne(ValidasiSurat::class, 'submission_id', 'id')
            ->where('role', 3001);
    }

    protected $appends = ['disable_validation_button'];

    public function getDisableValidationButtonAttribute()
    {
        return $this->attributes['disable_validation_button'] ?? false;
    }
    
    public function updateStatusBasedOnSignatures()
    {
        $signatures = [
            $this->adminValidation()->first(),
            $this->advisorSignature()->first(),
            $this->headOfProgramSignature()->first(),
            $this->headOfDepartmentSignature()->first(),
        ];


        $allApproved = true;
        $anyApproved = false;
        $anyRejected = false;
        $hasSignature = false;

        foreach ($signatures as $signature) {
            if ($signature) {
                $hasSignature = true;

                if ($signature->status === self::STATUS_REJECTED) {
                    $anyRejected = true;
                }

                if ($signature->status === self::STATUS_APPROVED) {
                    $anyApproved = true;
                }

                if ($signature->status !== self::STATUS_APPROVED) {
                    $allApproved = false;
                }
            } else {
                $allApproved = false;
            }
        }

        if ($anyRejected) {
            $this->status = self::STATUS_REJECTED;
        } elseif ($hasSignature && $allApproved) {
            $this->status = self::STATUS_DONE;
        } elseif ($anyApproved) {
            $this->status = self::STATUS_PROCESSED;
        } else {
            $this->status = self::STATUS_PENDING;
        }

        $this->save();
    }


    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getTimeDiffAttribute()
    {
        if (!$this->tgl_create) return '-';

        $createdAt = \Carbon\Carbon::parse($this->tgl_create);
        $diffInSeconds = now()->diffInSeconds($createdAt);

        if ($diffInSeconds < 3600) {
            return now()->diffInMinutes($createdAt) . ' menit';
        } elseif ($diffInSeconds < 86400) {
            return now()->diffInHours($createdAt) . ' jam';
        } else {
            return now()->startOfDay()->diffInDays($createdAt->startOfDay()) . ' hari';
        }
    }

    public function getStatusUpdatedAtAttribute()
    {
        if ($this->status === self::STATUS_APPROVED || $this->status === self::STATUS_REJECTED) {
            return $this->last_update ? \Carbon\Carbon::parse($this->last_update)->format('Y-m-d H:i:s') : '-';
        }

        return '-';
    }



    protected static function booted()
    {
        static::creating(function ($model) {
            $pesertaDidik = PesertaDidik::where('id_pd', auth()->user()->id_pd_pengguna)->first();
            $model->id_creator = $pesertaDidik ? $pesertaDidik->id_pd : null;
            $model->tgl_create = now();
            $model->last_sync = now();
        });

        static::updating(function ($model) {
            $pesertaDidik = PesertaDidik::where('id_pd', auth()->user()->id_pd_pengguna)->first();
            $model->updated_by = $pesertaDidik ? $pesertaDidik->id_pd : null;
            $model->last_update = now();
            $model->last_sync = now();
        });
    }
}
