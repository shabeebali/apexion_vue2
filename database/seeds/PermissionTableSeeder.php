<?php

use Illuminate\Database\Seeder;
use \Spatie\Permission\Models\Permission;
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name'=>'create_user','guard_name'=>'api','model'=>'User','model_slug'=>'user']);
        Permission::create(['name'=>'edit_user','guard_name'=>'api','model'=>'User','model_slug'=>'user']);
        Permission::create(['name'=>'delete_user','guard_name'=>'api','model'=>'User','model_slug'=>'user']);
        Permission::create(['name'=>'view_user','guard_name'=>'api','model'=>'User','model_slug'=>'user']);

        Permission::create(['name'=>'create_product','guard_name'=>'api','model'=>'Product','model_slug'=>'product']);
        Permission::create(['name'=>'edit_product','guard_name'=>'api','model'=>'Product','model_slug'=>'product']);
        Permission::create(['name'=>'delete_product','guard_name'=>'api','model'=>'Product','model_slug'=>'product']);
        Permission::create(['name'=>'view_product','guard_name'=>'api','model'=>'Product','model_slug'=>'product']);
        Permission::create(['name'=>'approve_product','guard_name'=>'api','model'=>'Product','model_slug'=>'product']);

        Permission::create(['name'=>'create_customer','guard_name'=>'api','model'=>'Customer','model_slug'=>'customer']);
        Permission::create(['name'=>'edit_customer','guard_name'=>'api','model'=>'Customer','model_slug'=>'customer']);
        Permission::create(['name'=>'delete_customer','guard_name'=>'api','model'=>'Customer','model_slug'=>'customer']);
        Permission::create(['name'=>'view_customer','guard_name'=>'api','model'=>'Customer','model_slug'=>'customer']);
        Permission::create(['name'=>'approve_customer','guard_name'=>'api','model'=>'Customer','model_slug'=>'customer']);

        Permission::create(['name'=>'create_taxonomy','guard_name'=>'api','model'=>'Taxonomy','model_slug'=>'taxonomy']);
        Permission::create(['name'=>'edit_taxonomy','guard_name'=>'api','model'=>'Taxonomy','model_slug'=>'taxonomy']);
        Permission::create(['name'=>'delete_taxonomy','guard_name'=>'api','model'=>'Taxonomy','model_slug'=>'taxonomy']);
        Permission::create(['name'=>'view_taxonomy','guard_name'=>'api','model'=>'Taxonomy','model_slug'=>'taxonomy']);

        Permission::create(['name'=>'create_category','guard_name'=>'api','model'=>'Category','model_slug'=>'category']);
        Permission::create(['name'=>'edit_category','guard_name'=>'api','model'=>'Category','model_slug'=>'category']);
        Permission::create(['name'=>'delete_category','guard_name'=>'api','model'=>'Category','model_slug'=>'category']);
        Permission::create(['name'=>'view_category','guard_name'=>'api','model'=>'Category','model_slug'=>'category']);

        Permission::create(['name'=>'create_warehouse','guard_name'=>'api','model'=>'Warehouse','model_slug'=>'warehouse']);
        Permission::create(['name'=>'edit_warehouse','guard_name'=>'api','model'=>'Warehouse','model_slug'=>'warehouse']);
        Permission::create(['name'=>'delete_warehouse','guard_name'=>'api','model'=>'Warehouse','model_slug'=>'warehouse']);
        Permission::create(['name'=>'view_warehouse','guard_name'=>'api','model'=>'Warehouse','model_slug'=>'warehouse']);

        Permission::create(['name'=>'create_pricelist','guard_name'=>'api','model'=>'Pricelist','model_slug'=>'pricelist']);
        Permission::create(['name'=>'edit_pricelist','guard_name'=>'api','model'=>'Pricelist','model_slug'=>'pricelist']);
        Permission::create(['name'=>'delete_pricelist','guard_name'=>'api','model'=>'Pricelist','model_slug'=>'pricelist']);
        Permission::create(['name'=>'view_pricelist','guard_name'=>'api','model'=>'Pricelist','model_slug'=>'pricelist']);

        Permission::create(['name'=>'create_sale','guard_name'=>'api','model'=>'Sale Order','model_slug'=>'sale']);
        Permission::create(['name'=>'edit_sale','guard_name'=>'api','model'=>'Sale Order','model_slug'=>'sale']);
        Permission::create(['name'=>'delete_sale','guard_name'=>'api','model'=>'Sale Order','model_slug'=>'sale']);
        Permission::create(['name'=>'view_sale','guard_name'=>'api','model'=>'Sale Order','model_slug'=>'sale']);
        Permission::create(['name'=>'approve_sale','guard_name'=>'api','model'=>'Sale Order','model_slug'=>'sale']);

        Permission::create(['name'=>'create_user_role','guard_name'=>'api','model'=>'User Role','model_slug'=>'user_role']);
        Permission::create(['name'=>'edit_user_role','guard_name'=>'api','model'=>'User Role','model_slug'=>'user_role']);
        Permission::create(['name'=>'delete_user_role','guard_name'=>'api','model'=>'User Role','model_slug'=>'user_role']);
        Permission::create(['name'=>'view_user_role','guard_name'=>'api','model'=>'User Role','model_slug'=>'user_role']);
    }
}
