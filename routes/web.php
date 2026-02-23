<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DentalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientPhotoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('patients', PatientController::class);
    Route::post('patients/{patient}/dental-records', [DentalRecordController::class, 'store'])->name('dental-records.store');
    Route::put('dental-records/{dentalRecord}', [DentalRecordController::class, 'update'])->name('dental-records.update');
    Route::post('patients/{patient}/photos', [PatientPhotoController::class, 'store'])->name('patient-photos.store');
    Route::delete('patient-photos/{photo}', [PatientPhotoController::class, 'destroy'])->name('patient-photos.destroy');
});

require __DIR__.'/settings.php';
