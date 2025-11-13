<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddFakePositionsSeeder extends Seeder
{
    public function run(): void
    {
        // Grupları al
        $groups = DB::table('employee_groups')->get();
        
        foreach ($groups as $group) {
            $positions = [];
            
            // Her gruba göre pozisyonlar
            switch($group->name) {
                case 'Mutfak':
                    $positions = ['Şef', 'Aşçı', 'Kalfa', 'Yardımcı', 'Pastacı', 'Sous Şef'];
                    break;
                case 'Servis':
                    $positions = ['Garson', 'Hostes', 'Kasiyer', 'Barista', 'Servis Sorumlusu', 'Müşteri Temsilcisi'];
                    break;
                case 'Kasa':
                    $positions = ['Kasiyer', 'Kasa Sorumlusu', 'Muhasebeci', 'Finans Uzmanı', 'Ödeme Uzmanı'];
                    break;
                case 'Yönetim':
                    $positions = ['Müdür', 'Müdür Yardımcısı', 'Muhasebeci', 'İK Uzmanı', 'Operasyon Müdürü', 'Genel Müdür'];
                    break;
                default:
                    $positions = [];
            }
            
            // Her pozisyonu ekle (eğer yoksa)
            foreach ($positions as $positionName) {
                $exists = DB::table('employee_positions')
                    ->where('group_id', $group->id)
                    ->where('name', $positionName)
                    ->exists();
                
                if (!$exists) {
                    DB::table('employee_positions')->insert([
                        'group_id' => $group->id,
                        'name' => $positionName,
                        'description' => $group->name . ' - ' . $positionName . ' pozisyonu',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        $this->command->info('✅ Fake görevler başarıyla eklendi!');
    }
}

