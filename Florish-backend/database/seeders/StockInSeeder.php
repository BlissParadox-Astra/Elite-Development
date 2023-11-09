<?php

namespace Database\Seeders;

use App\Models\StockIn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StockIn::factory()->count(30)->create();
    }
}
