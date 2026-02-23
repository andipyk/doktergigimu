<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientPhotoController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'photos' => ['required', 'array', 'max:10'],
            'photos.*' => ['required', 'image', 'max:5120'], // 5MB max each
            'type' => ['required', 'in:before,after,progress,xray'],
            'description' => ['nullable', 'string', 'max:255'],
            'taken_at' => ['nullable', 'date'],
        ]);

        foreach ($validated['photos'] as $photo) {
            $path = $photo->store("patients/{$patient->id}/photos", 'public');

            $patient->photos()->create([
                'photo_path' => $path,
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
                'taken_at' => $validated['taken_at'] ?? now()->toDateString(),
            ]);
        }

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Foto berhasil diupload.');
    }

    public function destroy(PatientPhoto $photo): RedirectResponse
    {
        $patientId = $photo->patient_id;

        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return redirect()->route('patients.show', $patientId)
            ->with('success', 'Foto berhasil dihapus.');
    }
}
