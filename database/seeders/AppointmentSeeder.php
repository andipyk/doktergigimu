<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada minimal 1 dokter
        $doctor = User::firstOrCreate(
            ['email' => 'dokter@doktergigimu.test'],
            [
                'name' => 'drg. Andi Pratama',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ],
        );

        // ── Agenda hari ini ──────────────────────────────
        // Confirmed appointments (sudah dikonfirmasi)
        Appointment::factory()
            ->count(3)
            ->confirmed()
            ->sequence(
                ['scheduled_at' => now()->setTime(9, 0), 'treatment' => 'Pembersihan Karang Gigi'],
                ['scheduled_at' => now()->setTime(10, 30), 'treatment' => 'Tambal Gigi'],
                ['scheduled_at' => now()->setTime(13, 0), 'treatment' => 'Kontrol Behel'],
            )
            ->create(['doctor_id' => $doctor->id]);

        // Pending appointments (menunggu konfirmasi) — hari ini
        Appointment::factory()
            ->count(2)
            ->sequence(
                ['scheduled_at' => now()->setTime(14, 30), 'treatment' => 'Cabut Gigi'],
                ['scheduled_at' => now()->setTime(15, 45), 'treatment' => 'Konsultasi'],
            )
            ->create(['doctor_id' => $doctor->id]);

        // Completed (selesai hari ini — untuk stats pendapatan)
        Appointment::factory()
            ->count(2)
            ->completed()
            ->sequence(
                ['scheduled_at' => now()->setTime(8, 0), 'treatment' => 'Pemeriksaan Rutin', 'amount' => 250_000],
                ['scheduled_at' => now()->setTime(8, 30), 'treatment' => 'Tambal Gigi', 'amount' => 450_000],
            )
            ->create(['doctor_id' => $doctor->id]);

        // Cancelled (untuk stats pembatalan)
        Appointment::factory()
            ->cancelled()
            ->create([
                'doctor_id' => $doctor->id,
                'scheduled_at' => now()->setTime(11, 0),
                'treatment' => 'Bleaching Gigi',
            ]);

        // ── Pending approvals dari hari lain ─────────────
        Appointment::factory()
            ->count(3)
            ->sequence(
                ['scheduled_at' => now()->addDays(1)->setTime(9, 0), 'treatment' => 'Perawatan Saluran Akar'],
                ['scheduled_at' => now()->addDays(2)->setTime(10, 0), 'treatment' => 'Veneer Gigi'],
                ['scheduled_at' => now()->addDays(3)->setTime(14, 0), 'treatment' => 'Implan Gigi'],
            )
            ->create(['doctor_id' => $doctor->id]);
    }
}
