<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            CategoryCollectionSeeder::class,

            PaymentMethodSeeder::class,
            PaymentProviderSeeder::class,
            PaymentProviderMethodSeeder::class,

            CouponSeeder::class,
        ]);

        /*
    |--------------------------------------------------------------------------
    | Super Admin User
    |--------------------------------------------------------------------------
    */

        $superAdmin = User::firstOrCreate(
            [
                'email' => 'admin@pricetag.com',
            ],
            [
                'name' => 'PriceTag Super Admin',
                'password' => Hash::make('ChangeMe@12345'),
            ]
        );

        $superAdmin->assignRole('Super Admin');
    }
}
