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
        Schema::table('employees', function (Blueprint $table) {
            // Zorunlu alanlar (nullable unique constraint'ler validation'da kontrol edilecek)
            $table->string('tc_no', 11)->nullable()->after('phone');
            $table->string('sgk_no', 20)->nullable()->after('tc_no');
            $table->unsignedBigInteger('group_id')->nullable()->after('sgk_no');
            $table->unsignedBigInteger('position_id')->nullable()->after('group_id');
            $table->string('iban', 255)->nullable()->after('position_id');
            $table->string('bank_name')->nullable()->after('iban');
            
            // Ödeme periyodu
            $table->enum('payment_frequency', ['daily', 'weekly', 'monthly', 'hourly'])->default('monthly')->after('monthly_salary');
            $table->decimal('daily_wage', 15, 2)->nullable()->after('payment_frequency');
            $table->decimal('weekly_wage', 15, 2)->nullable()->after('daily_wage');
            $table->integer('working_days_per_month')->default(30)->after('weekly_wage');
            
            // Opsiyonel
            $table->text('address')->nullable()->after('working_days_per_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'tc_no', 'sgk_no', 'group_id', 'position_id', 
                'iban', 'bank_name', 'payment_frequency', 
                'daily_wage', 'weekly_wage', 'working_days_per_month', 'address'
            ]);
        });
    }
};
