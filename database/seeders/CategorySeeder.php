<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'user_id' => null,
                'name' => 'Salary', 
                'type' => 'income', 
                'icon' => 'salary-icon'
            ],
            [
                'user_id' => null,
                'name' => 'Investments', 
                'type' => 'income', 
                'icon' => 'investments-icon'
            ],
            [
                'user_id' => null,
                'name' => 'Investments', 
                'type' => 'expense', 
                'icon' => 'investments-icon'
            ],
            [
                'user_id' => null,
                'name' => 'Food & Beverages', 
                'type' => 'expense', 
                'icon' => 'foodbeverage-icon'
            ],
            [
                'user_id' => null,
                'name' => 'Transport', 
                'type' => 'expense', 
                'icon' => 'transport-icon'
            ],
            [
                'user_id' => null,
                'name' => 'Entertainment', 
                'type' => 'expense', 
                'icon' => 'entertainment-icon'
            ],
            [
                'user_id' => null,
                'name' => 'Groceries', 
                'type' => 'expense', 
                'icon' => 'groceries-icon'
            ],
            [
                'user_id' => null,
                'name' => 'education', 
                'type' => 'expense', 
                'icon' => 'education-icon'
            ],
            [
                'user_id' => null,
                'name' => 'gifts', 
                'type' => 'expense', 
                'icon' => 'gifts-icon'
            ],
            [
                'user_id' => null,
                'name' => 'travel', 
                'type' => 'expense', 
                'icon' => 'travel-icon'
            ],
            [
                'user_id' => null,
                'name' => 'healthcare', 
                'type' => 'expense', 
                'icon' => 'healthcare-icon'
            ],
            [
                'user_id' => null,
                'name' => 'others', 
                'type' => 'expense', 
                'icon' => 'others-icon'
            ],
            ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
