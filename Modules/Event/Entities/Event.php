<?php

namespace Modules\Event\Entities;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    protected $fillable = [
        'id',
        'name',
        'description',
        'announcement',
        'photo',
        'start_at',
        'end_at',
        'status',
        'quota',
        'is_visible',
        'price',
        'discount'
    ];

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value = null) => $value ? Storage::url($value) : null,
        );
    }

    public function banners()
    {
        return $this->hasMany(EventBanner::class);
    }

    public function sponsors()
    {
        return $this->hasMany(EventSponsor::class);
    }

    public function admins()
    {
        return $this->hasMany(EventAdmin::class);
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function payment()
    {
        return $this->hasOne(EventPayment::class);
    }
}
