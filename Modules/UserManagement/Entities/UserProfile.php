<?php

namespace Modules\UserManagement\Entities;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'phone',
        'organization',
        'gender',
        'village_id',
        'address',
        'class'
    ];
}
