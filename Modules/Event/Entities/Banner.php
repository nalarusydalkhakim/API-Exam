<?php

namespace Modules\Event\Entities;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'id',
        'name',
        'sequence',
        'photo',
    ];

    public function switchSequence($id)
    {
        $item = $this->findOrFail($id);

        $tempSequence = $this->sequence;
        $this->sequence = $item->sequence;
        $item->sequence = $tempSequence;

        $item->save();
        $this->save();
    }


    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value = null) => $value ? Storage::url($value) : null,
        );
    }

    protected function sequence(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: function ($value) {
                if ($value == 1) {
                    $maxSequence = static::max('sequence');
                    return $maxSequence + 1;
                } else {
                    return $value;
                }
            },
        );
    }
}
