<?php

namespace Database\Seeders;

use App\Models\DentalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $doctor = User::firstOrCreate(
            ['email' => 'dokter@doktergigimu.test'],
            [
                'name' => 'drg. Andi Pratama',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ],
        );

        $patients = [
            ['name' => 'Siti Rahmawati', 'phone' => '081234567890', 'email' => 'siti@email.com', 'gender' => 'female', 'date_of_birth' => '1990-05-15', 'drug_allergies' => 'Amoxicillin, Ibuprofen', 'address' => 'Jl. Merdeka No. 10, Jakarta Pusat'],
            ['name' => 'Ahmad Hidayat', 'phone' => '082112345678', 'email' => 'ahmad@email.com', 'gender' => 'male', 'date_of_birth' => '1985-08-22', 'drug_allergies' => null, 'address' => 'Jl. Sudirman No. 45, Jakarta Selatan'],
            ['name' => 'Dewi Lestari', 'phone' => '081398765432', 'email' => 'dewi@email.com', 'gender' => 'female', 'date_of_birth' => '1995-03-10', 'drug_allergies' => 'Penisilin', 'address' => 'Jl. Gatot Subroto No. 12, Bandung'],
            ['name' => 'Budi Santoso', 'phone' => '085612345678', 'email' => null, 'gender' => 'male', 'date_of_birth' => '1978-12-01', 'drug_allergies' => null, 'address' => 'Jl. Diponegoro No. 8, Surabaya'],
            ['name' => 'Rina Kusuma', 'phone' => '087812345678', 'email' => 'rina@email.com', 'gender' => 'female', 'date_of_birth' => '2000-07-20', 'drug_allergies' => 'Aspirin', 'address' => null],
            ['name' => 'Fajar Pratama', 'phone' => '081512345678', 'email' => null, 'gender' => 'male', 'date_of_birth' => '1992-11-30', 'drug_allergies' => null, 'address' => 'Jl. Ahmad Yani No. 15, Yogyakarta'],
            ['name' => 'Nurul Hidayah', 'phone' => '082298765432', 'email' => 'nurul@email.com', 'gender' => 'female', 'date_of_birth' => '1988-01-25', 'drug_allergies' => null, 'address' => 'Jl. Pemuda No. 30, Semarang'],
            ['name' => 'Irfan Maulana', 'phone' => '085712345678', 'email' => null, 'gender' => 'male', 'date_of_birth' => '2001-04-12', 'drug_allergies' => 'Lidocaine', 'address' => 'Jl. Asia Afrika No. 5, Bandung'],
            ['name' => 'Yuni Astuti', 'phone' => '081612345678', 'email' => 'yuni@email.com', 'gender' => 'female', 'date_of_birth' => '1993-09-08', 'drug_allergies' => null, 'address' => null],
            ['name' => 'Eko Prasetyo', 'phone' => '082312345678', 'email' => null, 'gender' => 'male', 'date_of_birth' => '1982-06-17', 'drug_allergies' => null, 'address' => 'Jl. Pahlawan No. 22, Malang'],
            ['name' => 'Anisa Fitri', 'phone' => '087112345678', 'email' => 'anisa@email.com', 'gender' => 'female', 'date_of_birth' => '1997-02-28', 'drug_allergies' => 'Sulfonamide', 'address' => 'Jl. Veteran No. 7, Medan'],
            ['name' => 'Rizky Ramadhan', 'phone' => '081812345678', 'email' => null, 'gender' => 'male', 'date_of_birth' => '1999-10-05', 'drug_allergies' => null, 'address' => 'Jl. Cendrawasih No. 18, Makassar'],
            ['name' => 'Putri Wulandari', 'phone' => '085312345678', 'email' => 'putri@email.com', 'gender' => 'female', 'date_of_birth' => '1991-08-14', 'drug_allergies' => null, 'address' => null],
            ['name' => 'Hendra Wijaya', 'phone' => '082112345699', 'email' => null, 'gender' => 'male', 'date_of_birth' => '1975-03-01', 'drug_allergies' => 'Metronidazole', 'address' => 'Jl. Mangga Dua No. 55, Jakarta Utara'],
            ['name' => 'Linda Permata', 'phone' => '081998765432', 'email' => 'linda@email.com', 'gender' => 'female', 'date_of_birth' => '1996-12-22', 'drug_allergies' => null, 'address' => 'Jl. Raya Bogor No. 100, Depok'],
        ];

        $conditionOptions = ['healthy', 'caries', 'filling', 'crown', 'missing', 'root_canal', 'implant', 'bridge'];

        foreach ($patients as $data) {
            $patient = Patient::create($data);

            // Create 1-3 dental records per patient
            $recordCount = rand(1, 3);
            for ($r = 0; $r < $recordCount; $r++) {
                $record = DentalRecord::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'examined_at' => now()->subDays(rand(1, 180))->setTime(rand(8, 16), rand(0, 59)),
                    'notes' => $r === 0 ? 'Pemeriksaan awal' : 'Kontrol rutin',
                ]);

                // Add 3-8 random tooth conditions
                $toothNumbers = collect([11,12,13,14,15,16,17,18,21,22,23,24,25,26,27,28,31,32,33,34,35,36,37,38,41,42,43,44,45,46,47,48])
                    ->random(rand(3, 8))
                    ->values();

                foreach ($toothNumbers as $tooth) {
                    $record->toothConditions()->create([
                        'tooth_number' => $tooth,
                        'condition' => $conditionOptions[array_rand($conditionOptions)],
                        'notes' => null,
                    ]);
                }
            }
        }
    }
}
