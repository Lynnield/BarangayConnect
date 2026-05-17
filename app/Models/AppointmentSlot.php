<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSlot extends Model
{
    protected $fillable = [
        'slot_date', 'slot_time', 'max_appointments', 'is_available',
    ];

    protected $casts = [
        'slot_date' => 'date',
        'is_available' => 'boolean',
    ];
}
