<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dental_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('examined_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'examined_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dental_records');
    }
};
