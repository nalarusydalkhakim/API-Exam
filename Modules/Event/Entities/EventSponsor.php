<?php

namespace Modules\Event\Entities;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class EventSponsor extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'name',
        'photo',
        'link'
    ];

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value = null) => $value ? Storage::url($value) : null,
        );
    }
}
