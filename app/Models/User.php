<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\UserManagement\Entities\UserProfile;
use App\Traits\HasRoles;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Modules\Event\Entities\EventParticipant;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Uuid;
    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'email',
        'password',
        'name',
        'photo',
        'status',
        'level',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'string';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function eventParticipants()
    {
        return $this->hasMany(EventParticipant::class, 'user_id', 'id');
    }

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value = null) => $value ? Storage::url($value) : null,
        );
    }
}
