<?php

namespace Modules\Event\Entities;

use App\Models\User;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventPayment extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'event_id',
        'user_id',
        'code',
        'price',
        'status',
        'token'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function createEventParticipant()
    {
        $this->event->participants()->create([
            'user_id' => $this->user_id
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
