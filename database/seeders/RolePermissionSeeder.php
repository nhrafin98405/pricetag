<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // Dashboard
            'dashboard.view',

            // Products
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'products.manage',

            // Categories
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'categories.manage',

            // Collections
            'collections.view',
            'collections.create',
            'collections.edit',
            'collections.delete',
            'collections.manage',

            // Inventory
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            'inventory.manage',

            // Orders
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.cancel',
            'orders.manage',

            // Payments
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.refund',
            'payments.manage',

            // Shipments
            'shipments.view',
            'shipments.create',
            'shipments.edit',
            'shipments.delete',
            'shipments.manage',

            // Returns & Refunds
            'returns.view',
            'returns.create',
            'returns.edit',
            'returns.approve',
            'returns.manage',

            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.manage',

            // Coupons
            'coupons.view',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',
            'coupons.manage',

            // Reviews
            'reviews.view',
            'reviews.create',
            'reviews.edit',
            'reviews.delete',
            'reviews.approve',
            'reviews.manage',

            // CMS
            'cms.view',
            'cms.create',
            'cms.edit',
            'cms.delete',
            'cms.manage',

            // Blog / Journal
            'blog.view',
            'blog.create',
            'blog.edit',
            'blog.delete',
            'blog.manage',

            // Short Videos
            'short-videos.view',
            'short-videos.create',
            'short-videos.edit',
            'short-videos.delete',
            'short-videos.manage',

            // Notifications
            'notifications.view',
            'notifications.create',
            'notifications.edit',
            'notifications.delete',
            'notifications.manage',

            // Settings
            'settings.view',
            'settings.edit',
            'settings.manage',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage',

            // Roles & Permissions
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.manage',

            // Activity Logs
            'activity-logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $customer = Role::firstOrCreate([
            'name' => 'Customer',
            'guard_name' => 'web',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdmin->syncPermissions(
            Permission::all()
        );

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin->syncPermissions([
            'dashboard.view',

            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'products.manage',

            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'categories.manage',

            'collections.view',
            'collections.create',
            'collections.edit',
            'collections.delete',
            'collections.manage',

            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.manage',

            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.cancel',
            'orders.manage',

            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.refund',
            'payments.manage',

            'shipments.view',
            'shipments.create',
            'shipments.edit',
            'shipments.manage',

            'returns.view',
            'returns.create',
            'returns.edit',
            'returns.approve',
            'returns.manage',

            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.manage',

            'coupons.view',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',
            'coupons.manage',

            'reviews.view',
            'reviews.edit',
            'reviews.approve',
            'reviews.manage',

            'cms.view',
            'cms.create',
            'cms.edit',
            'cms.manage',

            'blog.view',
            'blog.create',
            'blog.edit',
            'blog.delete',
            'blog.manage',

            'short-videos.view',
            'short-videos.create',
            'short-videos.edit',
            'short-videos.delete',
            'short-videos.manage',

            'notifications.view',
            'notifications.create',
            'notifications.edit',
            'notifications.manage',

            'settings.view',
            'settings.edit',

            'users.view',
            'users.create',
            'users.edit',

            'activity-logs.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Customer
        |--------------------------------------------------------------------------
        */

        $customer->syncPermissions([]);
    }
}