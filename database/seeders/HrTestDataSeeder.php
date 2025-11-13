<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HrTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Örnek personeller oluştur (eğer yoksa)
        
        // Personel 1: Aylık maaşlı
        $employee1 = DB::table('employees')->where('email', 'ahmet@example.com')->first();
        if (!$employee1) {
            $employee1Id = DB::table('employees')->insertGetId([
            'name' => 'Ahmet Yılmaz',
            'phone' => '5551112233',
            'email' => 'ahmet@example.com',
            'tc_no' => '12345678901',
            'sgk_no' => 'SGK001',
            'hire_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            'group_id' => 1, // Mutfak
            'position_id' => 1, // Şef
            'payment_frequency' => 'monthly',
            'monthly_salary' => 25000.00,
            'working_days_per_month' => 26,
            'iban' => 'TR330006100519786457841326',
            'bank_name' => 'Ziraat Bankası',
            'address' => 'İstanbul, Kadıköy',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        } else {
            $employee1Id = $employee1->id;
        }
        
        // Personel 2: Günlük ücretli
        $employee2 = DB::table('employees')->where('email', 'mehmet@example.com')->first();
        if (!$employee2) {
            $employee2Id = DB::table('employees')->insertGetId([
            'name' => 'Mehmet Demir',
            'phone' => '5552223344',
            'email' => 'mehmet@example.com',
            'tc_no' => '23456789012',
            'sgk_no' => 'SGK002',
            'hire_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
            'group_id' => 2, // Servis
            'position_id' => 5, // Garson
            'payment_frequency' => 'daily',
            'daily_wage' => 500.00,
            'working_days_per_month' => 26,
            'iban' => 'TR330006100519786457841327',
            'bank_name' => 'İş Bankası',
            'address' => 'İstanbul, Beşiktaş',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        } else {
            $employee2Id = $employee2->id;
        }
        
        // Personel 3: Haftalık ücretli
        $employee3 = DB::table('employees')->where('email', 'ayse@example.com')->first();
        if (!$employee3) {
            $employee3Id = DB::table('employees')->insertGetId([
            'name' => 'Ayşe Kaya',
            'phone' => '5553334455',
            'email' => 'ayse@example.com',
            'tc_no' => '34567890123',
            'sgk_no' => 'SGK003',
            'hire_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'group_id' => 3, // Kasa
            'position_id' => 7, // Kasiyer
            'payment_frequency' => 'weekly',
            'weekly_wage' => 3000.00,
            'working_days_per_month' => 26,
            'iban' => 'TR330006100519786457841328',
            'bank_name' => 'Garanti BBVA',
            'address' => 'İstanbul, Şişli',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        } else {
            $employee3Id = $employee3->id;
        }
        
        // Örnek bordrolar oluştur
        $this->createSamplePayrolls($employee1Id, $employee2Id, $employee3Id);
        
        $this->command->info('✅ HR test verileri başarıyla oluşturuldu!');
        $this->command->info('📊 Oluşturulan personeller: ' . $employee1Id . ', ' . $employee2Id . ', ' . $employee3Id);
    }
    
    private function createSamplePayrolls($employee1Id, $employee2Id, $employee3Id)
    {
        $payrollService = new \App\Services\PayrollService();
        
        // Bordro 1: Aylık maaşlı personel için
        try {
            $payroll1 = $payrollService->createPayroll([
                'employee_id' => $employee1Id,
                'period_type' => 'monthly',
                'period_start_date' => Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
                'period_end_date' => Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d'),
                'payroll_period' => Carbon::now()->subMonth()->format('Y-m'),
            ]);
            
            // Örnek ödeme ekle
            $payrollService->addPartialPayment($payroll1->id, [
                'amount' => 15000.00,
                'payment_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'bank_name' => 'Ziraat Bankası',
                'account_number' => 'TR330006100519786457841326',
                'description' => 'İlk ödeme - Banka transferi',
            ]);
            
            // İkinci ödeme ekle
            $payrollService->addPartialPayment($payroll1->id, [
                'amount' => 5000.00,
                'payment_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'payment_method' => 'cash',
                'description' => 'Kısmi ödeme - Nakit',
            ]);
        } catch (\Exception $e) {
            // Bordro zaten varsa devam et
        }
        
        // Bordro 2: Günlük ücretli personel için
        try {
            $payroll2 = $payrollService->createPayroll([
                'employee_id' => $employee2Id,
                'period_type' => 'daily',
                'period_start_date' => Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d'),
                'period_end_date' => Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d'),
                'payroll_period' => Carbon::now()->subWeek()->format('Y-m-d'),
            ]);
            
            // Örnek ödeme ekle
            $payrollService->addPartialPayment($payroll2->id, [
                'amount' => 2000.00,
                'payment_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'bank_name' => 'İş Bankası',
                'account_number' => 'TR330006100519786457841327',
                'description' => 'Haftalık ödeme',
            ]);
        } catch (\Exception $e) {
            // Bordro zaten varsa devam et
        }
        
        // Bordro 3: Haftalık ücretli personel için (tamamı ödenmiş)
        try {
            $payroll3 = $payrollService->createPayroll([
                'employee_id' => $employee3Id,
                'period_type' => 'weekly',
                'period_start_date' => Carbon::now()->subWeeks(2)->startOfWeek()->format('Y-m-d'),
                'period_end_date' => Carbon::now()->subWeeks(2)->endOfWeek()->format('Y-m-d'),
                'payroll_period' => Carbon::now()->subWeeks(2)->format('Y') . '-W' . str_pad(Carbon::now()->subWeeks(2)->week, 2, '0', STR_PAD_LEFT),
            ]);
            
            // Tam ödeme ekle
            $payroll = DB::table('payrolls')->where('id', $payroll3->id)->first();
            if ($payroll) {
                $payrollService->addPartialPayment($payroll3->id, [
                    'amount' => $payroll->net_salary,
                    'payment_date' => Carbon::now()->subWeeks(1)->format('Y-m-d'),
                    'payment_method' => 'bank_transfer',
                    'bank_name' => 'Garanti BBVA',
                    'account_number' => 'TR330006100519786457841328',
                    'description' => 'Haftalık maaş - Tam ödeme',
                ]);
            }
        } catch (\Exception $e) {
            // Bordro zaten varsa devam et
        }
    }
}

