<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Patient::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query
            ->withCount(['dentalRecords', 'appointments'])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('patients/Index', [
            'patients' => $patients,
            'filters' => [
                'search' => $search ?? '',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('patients/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', 'in:male,female'],
            'address' => ['nullable', 'string'],
            'drug_allergies' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
        ]);

        $patient = Patient::create($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function show(Patient $patient): Response
    {
        $patient->load([
            'dentalRecords' => fn ($q) => $q->with(['doctor', 'toothConditions'])->orderByDesc('examined_at'),
            'photos' => fn ($q) => $q->orderByDesc('taken_at'),
        ]);

        return Inertia::render('patients/Show', [
            'patient' => $patient,
        ]);
    }

    public function edit(Patient $patient): Response
    {
        return Inertia::render('patients/Edit', [
            'patient' => $patient,
        ]);
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', 'in:male,female'],
            'address' => ['nullable', 'string'],
            'drug_allergies' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Pasien berhasil dihapus.');
    }
}
