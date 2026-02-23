<?php

namespace App\Http\Controllers;

use App\Models\DentalRecord;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DentalRecordController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'examined_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'teeth' => ['required', 'array'],
            'teeth.*.tooth_number' => ['required', 'integer', 'between:11,48'],
            'teeth.*.condition' => ['required', 'in:healthy,caries,filling,crown,missing,root_canal,implant,bridge'],
            'teeth.*.surface' => ['nullable', 'string', 'max:10'],
            'teeth.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $record = $patient->dentalRecords()->create([
            'doctor_id' => Auth::id(),
            'examined_at' => $validated['examined_at'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['teeth'] as $tooth) {
            $record->toothConditions()->create($tooth);
        }

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Rekam gigi berhasil disimpan.');
    }

    public function update(Request $request, DentalRecord $dentalRecord): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
            'teeth' => ['required', 'array'],
            'teeth.*.tooth_number' => ['required', 'integer', 'between:11,48'],
            'teeth.*.condition' => ['required', 'in:healthy,caries,filling,crown,missing,root_canal,implant,bridge'],
            'teeth.*.surface' => ['nullable', 'string', 'max:10'],
            'teeth.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $dentalRecord->update(['notes' => $validated['notes'] ?? null]);

        // Replace all tooth conditions
        $dentalRecord->toothConditions()->delete();
        foreach ($validated['teeth'] as $tooth) {
            $dentalRecord->toothConditions()->create($tooth);
        }

        return redirect()->route('patients.show', $dentalRecord->patient)
            ->with('success', 'Rekam gigi berhasil diperbarui.');
    }
}
