<?php

namespace Modules\LearningManagement\Entities;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'name',
    ];
}
