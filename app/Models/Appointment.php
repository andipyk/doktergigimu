<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_name',
        'patient_phone',
        'doctor_id',
        'scheduled_at',
        'duration_minutes',
        'treatment',
        'status',
        'amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'amount' => 'decimal:2',
            'duration_minutes' => 'integer',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
