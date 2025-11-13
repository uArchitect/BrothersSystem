<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users - Admin kullanıcısı
        if (DB::table('users')->count() === 0) {
        DB::table('users')->insert([
            'name' => 'Sistem Yöneticisi',
            'email' => 'admin@siparismasanda.com',
                'password' => Hash::make('123456789'),
            'phone' => '5078928490',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        }

        // 2. Expense Types (Gider Tipleri)
        if (DB::table('expense_types')->count() === 0) {
            DB::table('expense_types')->insert([
                ['name' => 'Malzeme', 'description' => 'Malzeme giderleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Elektrik', 'description' => 'Elektrik faturaları', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Kira', 'description' => 'Kira giderleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Personel', 'description' => 'Personel maaşları', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 3. Expense Categories (Gider Kalemleri)
        if (DB::table('expense_categories')->count() === 0) {
            DB::table('expense_categories')->insert([
                ['name' => 'Operasyonel', 'description' => 'Operasyonel giderler', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Yatırım', 'description' => 'Yatırım giderleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Personel', 'description' => 'Personel giderleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 4. Income Types (Gelir Tipleri)
        if (DB::table('income_types')->count() === 0) {
            DB::table('income_types')->insert([
                ['name' => 'Nakit', 'description' => 'Nakit gelir', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Kart', 'description' => 'Kredi kartı geliri', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Havale', 'description' => 'Havale geliri', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Çek', 'description' => 'Çek geliri', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'EFT', 'description' => 'EFT geliri', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 5. Income Categories (Gelir Kalemleri)
        if (DB::table('income_categories')->count() === 0) {
            DB::table('income_categories')->insert([
                ['name' => 'Satış', 'description' => 'Satış gelirleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Hizmet', 'description' => 'Hizmet gelirleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Diğer', 'description' => 'Diğer gelirler', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Faiz', 'description' => 'Faiz gelirleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 6. Employee Groups and Positions
        $this->call([
            \Database\Seeders\EmployeeGroupsAndPositionsSeeder::class,
        ]);

        // 7. HR Test Data (Personel, Bordro, Ödeme örnekleri)
        // Not: Test için seeder'ı çalıştırmak için: php artisan db:seed --class=HrTestDataSeeder
        // $this->call([
        //     \Database\Seeders\HrTestDataSeeder::class,
        // ]);

        $this->command->info('✅ Seeding completed successfully!');
        $this->command->info('📊 Seeded tables: users, expense_types, expense_categories, income_types, income_categories, employee_groups, employee_positions');
        $this->command->info('💡 HR test verileri için: php artisan db:seed --class=HrTestDataSeeder');
    }
}
