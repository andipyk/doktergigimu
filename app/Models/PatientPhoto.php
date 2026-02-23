<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PatientPhoto extends Model
{
    protected $fillable = [
        'patient_id',
        'photo_path',
        'type',
        'description',
        'taken_at',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->photo_path);
    }
}
