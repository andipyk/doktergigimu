<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('patient_phone')->nullable();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(30);
            $table->string('treatment')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'scheduled_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
