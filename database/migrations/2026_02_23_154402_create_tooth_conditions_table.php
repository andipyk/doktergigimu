<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tooth_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dental_record_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('tooth_number'); // FDI notation: 11-18, 21-28, 31-38, 41-48
            $table->enum('condition', [
                'healthy',
                'caries',
                'filling',
                'crown',
                'missing',
                'root_canal',
                'implant',
                'bridge',
            ])->default('healthy');
            $table->string('surface')->nullable(); // M, D, O, B, L combinations
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['dental_record_id', 'tooth_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tooth_conditions');
    }
};
