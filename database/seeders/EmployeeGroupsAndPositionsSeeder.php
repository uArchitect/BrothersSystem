<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeGroupsAndPositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gruplar
        $groups = [
            ['name' => 'Mutfak', 'description' => 'Mutfak personeli'],
            ['name' => 'Servis', 'description' => 'Servis personeli'],
            ['name' => 'Kasa', 'description' => 'Kasa personeli'],
            ['name' => 'Yönetim', 'description' => 'Yönetim personeli'],
        ];
        
        foreach ($groups as $group) {
            $groupId = DB::table('employee_groups')->insertGetId([
                'name' => $group['name'],
                'description' => $group['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Görevler
            $positions = $this->getPositionsForGroup($group['name']);
            foreach ($positions as $position) {
                DB::table('employee_positions')->insert([
                    'group_id' => $groupId,
                    'name' => $position,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
    private function getPositionsForGroup($groupName) {
        return match($groupName) {
            'Mutfak' => ['Şef', 'Aşçı', 'Kalfa', 'Yardımcı'],
            'Servis' => ['Garson', 'Hostes', 'Kasiyer'],
            'Kasa' => ['Kasiyer', 'Kasa Sorumlusu'],
            'Yönetim' => ['Müdür', 'Müdür Yardımcısı', 'Muhasebeci'],
            default => [],
        };
    }
}
