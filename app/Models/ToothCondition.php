<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToothCondition extends Model
{
    protected $fillable = [
        'dental_record_id',
        'tooth_number',
        'condition',
        'surface',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'tooth_number' => 'integer',
        ];
    }

    public function dentalRecord(): BelongsTo
    {
        return $this->belongsTo(DentalRecord::class);
    }
}
