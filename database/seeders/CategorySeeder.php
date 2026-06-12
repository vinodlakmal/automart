<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // parent => [type, [children...]]
        $tree = [
            'Vehicles'            => ['vehicles',    ['Cars', 'Motorbikes', 'Three Wheelers', 'Vans', 'Lorries', 'Auto Parts']],
            'Electronics'         => ['electronics', ['Mobile Phones', 'Computers & Tablets', 'TVs', 'Cameras', 'Home Appliances']],
            'Property'            => ['property',    ['Land', 'Houses', 'Apartments', 'Rooms & Annexes', 'Commercial Property']],
            'Home & Garden'       => [null,          ['Furniture', 'Home Decor', 'Kitchen Items', 'Garden']],
            'Jobs'                => [null,          ['IT', 'Sales & Marketing', 'Accounting', 'Healthcare']],
            'Services'            => [null,          ['Education', 'Repair', 'Event Services', 'Health & Beauty']],
            'Fashion & Beauty'    => [null,          ['Clothing', 'Watches', 'Footwear', 'Bags']],
            'Animals'             => [null,          ['Pets', 'Farm Animals', 'Pet Food & Accessories']],
        ];

        $order = 0;
        foreach ($tree as $parentName => [$type, $children]) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                ['name' => $parentName, 'type' => $type, 'sort_order' => $order++]
            );

            $childOrder = 0;
            foreach ($children as $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($parentName.'-'.$childName)],
                    ['name' => $childName, 'parent_id' => $parent->id, 'type' => $type, 'sort_order' => $childOrder++]
                );
            }
        }
    }
}
