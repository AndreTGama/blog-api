<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Coding',
                'slug' => 'coding',
            ],
            [
                'name' => 'Devops',
                'slug' => 'devops',
            ],
        ];
        
        foreach ($data as $item) {
            \App\Models\Category::create($item);
        }
    }
}
