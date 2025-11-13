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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');
            $table->string('payroll_period', 20); // '2025-01', '2025-01-15', '2025-W01'
            $table->enum('period_type', ['daily', 'weekly', 'monthly', 'hourly']);
            $table->date('period_start_date');
            $table->date('period_end_date');
            
            // Hesaplama Bilgileri
            $table->integer('working_days')->default(0);
            $table->decimal('daily_wage', 15, 2)->nullable();
            $table->decimal('calculated_amount', 15, 2)->default(0);
            
            // Bordro Detayları
            $table->decimal('gross_salary', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            
            // Ödeme Durumu
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            
            $table->enum('status', ['pending', 'partial', 'paid', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            
            // Maaş değişikliği takibi
            $table->decimal('base_salary', 15, 2)->nullable();
            $table->date('base_salary_date')->nullable();
            
            $table->timestamps();
            
            $table->unique(['employee_id', 'period_type', 'period_start_date'], 'unique_employee_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
