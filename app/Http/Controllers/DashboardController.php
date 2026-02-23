<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today = Carbon::today();

        $todayAgenda = Appointment::query()
            ->whereDate('scheduled_at', $today)
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn (Appointment $a) => [
                'id' => $a->id,
                'patient_name' => $a->patient_name,
                'patient_phone' => $a->patient_phone,
                'scheduled_at' => $a->scheduled_at->toIso8601String(),
                'duration_minutes' => $a->duration_minutes,
                'treatment' => $a->treatment,
                'status' => $a->status,
                'notes' => $a->notes,
            ]);

        $stats = [
            'newBookings' => Appointment::whereDate('scheduled_at', $today)
                ->where('status', 'pending')
                ->count(),
            'totalRevenue' => (float) Appointment::whereDate('scheduled_at', $today)
                ->where('status', 'completed')
                ->sum('amount'),
            'cancellations' => Appointment::whereDate('scheduled_at', $today)
                ->where('status', 'cancelled')
                ->count(),
        ];

        $pendingApprovals = Appointment::query()
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn (Appointment $a) => [
                'id' => $a->id,
                'patient_name' => $a->patient_name,
                'patient_phone' => $a->patient_phone,
                'scheduled_at' => $a->scheduled_at->toIso8601String(),
                'treatment' => $a->treatment,
                'created_at' => $a->created_at->toIso8601String(),
            ]);

        return Inertia::render('Dashboard', [
            'todayAgenda' => $todayAgenda,
            'stats' => $stats,
            'pendingApprovals' => $pendingApprovals,
        ]);
    }
}
