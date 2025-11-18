<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('frequency'); // daily, weekly, monthly
            
            // Para frecuencia diaria
            $table->integer('daily_interval')->nullable(); // 1-99 días
            
            // Para frecuencia semanal
            $table->json('weekly_days')->nullable(); // ['monday', 'wednesday', 'friday']
            $table->integer('weekly_interval')->nullable(); // 1-99 semanas
            
            // Para frecuencia mensual
            $table->json('monthly_days')->nullable(); // [1, 15, 30] días del mes
            $table->integer('monthly_interval')->nullable(); // 1-12 meses
            
            $table->date('last_completed_at')->nullable();
            $table->date('next_due_date')->nullable();
            $table->integer('streak')->default(0); // racha actual
            $table->integer('best_streak')->default(0); // mejor racha
            $table->integer('total_completions')->default(0);
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
