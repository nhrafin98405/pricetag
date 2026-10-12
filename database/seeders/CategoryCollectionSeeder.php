<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryCollectionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $bags = Category::firstOrCreate(
            ['slug' => 'bags'],
            [
                'name' => 'Bags',
                'description' => 'Premium bags collection.',
                'sort_order' => 1,
                'status' => true,
            ]
        );

        $jewellery = Category::firstOrCreate(
            ['slug' => 'jewellery'],
            [
                'name' => 'Jewellery',
                'description' => 'Premium jewellery collection.',
                'sort_order' => 2,
                'status' => true,
            ]
        );

        $bagCategories = [
            'Hand Bags',
            'Shoulder Bags',
            'Crossbody Bags',
        ];

        foreach ($bagCategories as $index => $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'parent_id' => $bags->id,
                    'name' => $name,
                    'sort_order' => $index + 1,
                    'status' => true,
                ]
            );
        }

        $jewelleryCategories = [
            'Earrings',
            'Necklaces',
            'Bracelets',
        ];

        foreach ($jewelleryCategories as $index => $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'parent_id' => $jewellery->id,
                    'name' => $name,
                    'sort_order' => $index + 1,
                    'status' => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Collections
        |--------------------------------------------------------------------------
        */

        $collections = [
            [
                'name' => 'New Arrivals',
                'slug' => 'new-arrivals',
                'description' => 'Discover our latest products.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Best Sellers',
                'slug' => 'best-sellers',
                'description' => 'Our most popular products.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium Collection',
                'slug' => 'premium-collection',
                'description' => 'A curated selection of premium products.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Limited Edition',
                'slug' => 'limited-edition',
                'description' => 'Exclusive limited edition products.',
                'sort_order' => 4,
            ],
        ];

        foreach ($collections as $collection) {
            Collection::firstOrCreate(
                ['slug' => $collection['slug']],
                [
                    'name' => $collection['name'],
                    'description' => $collection['description'],
                    'sort_order' => $collection['sort_order'],
                    'status' => true,
                ]
            );
        }
    }
}