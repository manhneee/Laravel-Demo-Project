<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Priority;
class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Priority::exists()) {
            Priority::create([
                'name' => 'Low',
            ]);
            Priority::create([
                'name' => 'Medium',
            ]);
            Priority::create([
                'name' => 'High',
            ]);
        }
    }
}
