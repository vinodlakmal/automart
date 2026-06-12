<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // parent => [type, icon, [children...]]
        $tree = [
            'Real Estate' => ['real_estate', '🏠', [
                'Houses for Sale', 'Houses for Rent', 'Apartments', 'Land',
                'Commercial Property', 'Rooms & Annexes', 'Holiday Rentals',
            ]],
            'Vehicles' => ['vehicles', '🚗', [
                'Cars', 'Motorbikes', 'Three Wheelers', 'Vans', 'SUVs / Jeeps',
                'Lorries & Trucks', 'Buses', 'Auto Parts & Accessories',
            ]],
            'Education' => ['education', '🎓', [
                'Tuition & Classes', 'Online Courses', 'Books & Stationery',
                'Educational Services', 'Other Education',
            ]],
            'Shopping' => ['shopping', '🛍️', [
                'Clothing & Fashion', 'Footwear', 'Watches & Jewellery',
                'Health & Beauty', 'Bags & Accessories', 'Toys & Games',
            ]],
            'Jobs' => ['jobs', '💼', [
                'IT & Software', 'Sales & Marketing', 'Accounting & Finance',
                'Healthcare', 'Hospitality & Tourism', 'Customer Service',
            ]],
            'Services' => ['services', '🛠️', [
                'Home & Garden Services', 'Repair & Maintenance', 'Event Services',
                'Health & Beauty Services', 'Professional Services',
            ]],
            'Electronics' => ['electronics', '📱', [
                'Mobile Phones', 'Computers & Tablets', 'TVs & Audio',
                'Cameras', 'Home Appliances', 'Gaming',
            ]],
        ];

        $order = 0;
        foreach ($tree as $parentName => [$type, $icon, $children]) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($parentName)],
                ['name' => $parentName, 'type' => $type, 'icon' => $icon, 'sort_order' => $order++, 'is_active' => true]
            );

            $childOrder = 0;
            foreach ($children as $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($parentName.'-'.$childName)],
                    ['name' => $childName, 'parent_id' => $parent->id, 'type' => $type, 'sort_order' => $childOrder++, 'is_active' => true]
                );
            }
        }
    }
}
