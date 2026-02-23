<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    private const TREATMENTS = [
        'Pembersihan Karang Gigi',
        'Tambal Gigi',
        'Cabut Gigi',
        'Pemasangan Behel',
        'Kontrol Behel',
        'Bleaching Gigi',
        'Perawatan Saluran Akar',
        'Veneer Gigi',
        'Pasang Crown',
        'Konsultasi',
        'Pemeriksaan Rutin',
        'Perawatan Gusi',
        'Implan Gigi',
    ];

    private const PRICES = [
        'Pembersihan Karang Gigi' => [250_000, 500_000],
        'Tambal Gigi' => [200_000, 600_000],
        'Cabut Gigi' => [300_000, 1_000_000],
        'Pemasangan Behel' => [5_000_000, 15_000_000],
        'Kontrol Behel' => [200_000, 400_000],
        'Bleaching Gigi' => [1_500_000, 3_500_000],
        'Perawatan Saluran Akar' => [1_000_000, 3_000_000],
        'Veneer Gigi' => [2_000_000, 5_000_000],
        'Pasang Crown' => [2_500_000, 6_000_000],
        'Konsultasi' => [100_000, 200_000],
        'Pemeriksaan Rutin' => [150_000, 300_000],
        'Perawatan Gusi' => [500_000, 1_500_000],
        'Implan Gigi' => [8_000_000, 20_000_000],
    ];

    public function definition(): array
    {
        $treatment = fake()->randomElement(self::TREATMENTS);
        $priceRange = self::PRICES[$treatment];

        return [
            'patient_name' => fake('id_ID')->name(),
            'patient_phone' => fake('id_ID')->phoneNumber(),
            'doctor_id' => User::factory(),
            'scheduled_at' => fake()->dateTimeBetween('today 08:00', 'today 17:00'),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60]),
            'treatment' => $treatment,
            'status' => 'pending',
            'amount' => fake()->numberBetween($priceRange[0], $priceRange[1]),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled']);
    }

    public function scheduledAt(string $time): static
    {
        return $this->state(fn () => [
            'scheduled_at' => now()->setTimeFromTimeString($time),
        ]);
    }

    public function tomorrow(): static
    {
        return $this->state(fn () => [
            'scheduled_at' => fake()->dateTimeBetween('tomorrow 08:00', 'tomorrow 17:00'),
        ]);
    }
}
