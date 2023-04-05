<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::truncate();

        Category::insert([
            [
                "name" => "entertainment"
            ],
            [
                "name" => "travel"
            ],
            [
                "name" => "movie"
            ],
            [
                "name" => "music"
            ],
            [
                "name" => "sport"
            ],
            [
                "name" => "health"
            ],
            [
                "name" => "economy"
            ],
            [
                "name" => "politic"
            ],
            [
                "name" => "science"
            ],
            [
                "name" => "blockchain"
            ]
        ]);
    }
}
