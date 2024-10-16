<?php

namespace Modules\Event\Entities;

use App\Models\User;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventAdmin extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
