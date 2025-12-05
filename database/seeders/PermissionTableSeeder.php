<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    
    public function run(): void
    {
        $permissions = [
            // Roles
            'role-list', 'role-create', 'role-edit', 'role-delete',

            // Users
            'user-list', 'user-create', 'user-edit', 'user-delete',

            // Brands
            'brand-list', 'brand-create', 'brand-edit', 'brand-delete',

            // Categories
            'category-list', 'category-create', 'category-edit', 'category-delete',

            // Subcategories
            'subcategory-list', 'subcategory-create', 'subcategory-edit', 'subcategory-delete',

            // Products
            'product-list', 'product-create', 'product-edit', 'product-delete',

            // Coupons
            'coupon-list', 'coupon-create', 'coupon-edit', 'coupon-delete',

            // Carts
            'cart-list', 'cart-create', 'cart-edit', 'cart-delete',

            // Cart Items
            'cartitem-list', 'cartitem-create', 'cartitem-edit', 'cartitem-delete',

            // Orders
            'order-list', 'order-create', 'order-edit', 'order-delete',

            // Order Items
            'orderitem-list', 'orderitem-create', 'orderitem-edit', 'orderitem-delete',

            // Notifications
            'notification-list', 'notification-create', 'notification-edit', 'notification-delete',

            // Activity Logs
            'activitylog-list', 'activitylog-create', 'activitylog-edit', 'activitylog-delete',

            // Coupons
            'coupon-list', 'coupon-create', 'coupon-edit', 'coupon-delete',

            // Carts
            'cart-list', 'cart-create', 'cart-edit', 'cart-delete',

            // Cart Items
            'cartitem-list', 'cartitem-create', 'cartitem-edit', 'cartitem-delete',

            // Orders
            'order-list', 'order-create', 'order-edit', 'order-delete',

            // Order Items
            'orderitem-list', 'orderitem-create', 'orderitem-edit', 'orderitem-delete',

            // Notifications
            'notification-list', 'notification-create', 'notification-edit', 'notification-delete',

            // Activity Logs
            'activitylog-list', 'activitylog-create', 'activitylog-edit', 'activitylog-delete',

            // Product Skus
            'product-sku-list', 'product-sku-create', 'product-sku-edit', 'product-sku-delete',   
            ];
            

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}