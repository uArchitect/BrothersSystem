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
        Schema::create('employee_daily_wages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('staff_shift_id')->nullable();
            $table->unsignedBigInteger('payroll_id')->nullable();
            $table->date('calculation_date');
            $table->date('work_date');
            $table->decimal('daily_wage', 15, 2);
            $table->decimal('worked_hours', 5, 2)->nullable();
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_overtime')->default(false);
            $table->decimal('multiplier', 4, 2)->default(1.0);
            $table->decimal('calculated_amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_daily_wages');
    }
};
