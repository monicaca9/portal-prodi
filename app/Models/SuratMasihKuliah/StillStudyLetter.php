<?php

namespace App\Models\SuratMasihKuliah;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AbstractionModel;
use App\Models\Pdrd\PesertaDidik;
use App\Models\SuratMasihKuliah\Letter_Signature;
use App\Models\SuratMasihKuliah\Letter_Number;
use BcMath\Number;

class StillStudyLetter extends AbstractionModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_masih.still_study_letter';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'student_number',
        'department',
        'study_program',
        'semester',
        'academic_year',
        'phone_number',
        'address',
        'purpose',
        'parent_name',
        'parent_nip',
        'parent_grade',
        'parent_job',
        'parent_institution',
        'parent_address',
        'signature',
        'academic_advisor',
        'supporting_document',
        'supporting_document2',
        'status',
        'admin_validation_id',
        'advisor_signature_id',
        'head_of_program_signature_id',
        'head_of_department_signature_id',
        'letter_number',
        
        'created_by',        // id_creator
        'created_at',        // tgl_create
        'updated_by',        // id_updater
        'updated_at',        // last_update
        'soft_delete',       // soft_delete
        'last_sync',         // last_sync
    ];

    const STATUS_PENDING = 'menunggu';
    const STATUS_APPROVED = 'disetujui';
    const STATUS_REJECTED = 'ditolak';
    const STATUS_PROCESSED = 'proses';
    const STATUS_DONE = 'selesai';

    public function createdBy()
    {
        return $this->belongsTo(PesertaDidik::class, 'created_by', 'id_pd');
    }

    public function numberLetter()
    {
        return $this->belongsTo(NumberLetter::class, 'letter_number', 'id');
    }


    public function adminValidation()
    {
        return $this->hasOne(SignatureLetter::class, 'submission_id', 'id')
            ->where('role', 6);
    }

    public function advisorSignature()
    {
        return $this->hasOne(SignatureLetter::class, 'submission_id', 'id')
            ->where('role', 46);
    }

    public function headOfProgramSignature()
    {
        return $this->hasOne(SignatureLetter::class, 'submission_id', 'id')
            ->where('role', 3000);
    }

    public function headOfDepartmentSignature()
    {
        return $this->hasOne(SignatureLetter::class, 'submission_id', 'id')
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
        if (!$this->created_at) return '-';

        $createdAt = \Carbon\Carbon::parse($this->created_at);
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
            return $this->updated_at ? \Carbon\Carbon::parse($this->updated_at)->format('Y-m-d H:i:s') : '-';
        }

        return '-';
    }



    protected static function booted()
    {
        static::creating(function ($model) {
            $pesertaDidik = PesertaDidik::where('id_pd', auth()->user()->id_pd_pengguna)->first();
            $model->created_by = $pesertaDidik ? $pesertaDidik->id_pd : null;
            $model->created_at = now();
            $model->last_sync = now();
        });

        static::updating(function ($model) {
            $pesertaDidik = PesertaDidik::where('id_pd', auth()->user()->id_pd_pengguna)->first();
            $model->updated_by = $pesertaDidik ? $pesertaDidik->id_pd : null;
            $model->updated_at = now();
            $model->last_sync = now();
        });
    }
}
