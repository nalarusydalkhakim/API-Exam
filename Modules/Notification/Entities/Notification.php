<?php

namespace Modules\Notification\Entities;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'notifiable_type',
        'notifiable_id',
        'read_at'
    ];
}
