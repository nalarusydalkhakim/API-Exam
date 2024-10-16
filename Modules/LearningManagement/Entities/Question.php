<?php

namespace Modules\LearningManagement\Entities;

use App\Models\User;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Question extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'owner_id',
        'answer_type',
        'question',
        'file',
        'file_name',
        'explanation',
        'level',
        'visibility',
        'class',
        'subject_id',
    ];

    const answer_types = [
        'Pilihan Ganda',
        'Esai',
        'Unggah Berkas'
    ];

    const levels = [
        'C1-Pengetahuan',
        'C2-Pemahaman',
        'C3-Penerapan',
        'C4-Analisis',
        'C5-Sintesis',
        'C6-Evaluasi'
    ];

    const visibilities = [
        'Hanya Saya',
        'Publik'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id')
            ->orderBy('key', 'asc');
    }

    protected function file(): Attribute
    {
        return Attribute::make(
            get: fn (string $value = null) => $value ? Storage::url($value) : null,
        );
    }
}
