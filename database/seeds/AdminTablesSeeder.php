<?php

use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // base tables
        Encore\Admin\Auth\Database\Menu::truncate();
        Encore\Admin\Auth\Database\Menu::insert(
            [
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "title" => "Dashboard",
                    "icon" => "fa-bar-chart",
                    "uri" => "/",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 2,
                    "title" => "系统管理",
                    "icon" => "fa-tasks",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 3,
                    "title" => "管理员管理",
                    "icon" => "fa-users",
                    "uri" => "auth/users",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 4,
                    "title" => "角色管理",
                    "icon" => "fa-user",
                    "uri" => "auth/roles",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 5,
                    "title" => "权限管理",
                    "icon" => "fa-ban",
                    "uri" => "auth/permissions",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 6,
                    "title" => "系统设置",
                    "icon" => "fa-bars",
                    "uri" => "auth/menu",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 7,
                    "title" => "操作日记",
                    "icon" => "fa-history",
                    "uri" => "auth/logs",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 0,
                    "title" => "优惠券管理",
                    "icon" => "fa-bars",
                    "uri" => "coupons",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 0,
                    "title" => "商品管理",
                    "icon" => "fa-bars",
                    "uri" => "products",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 0,
                    "title" => "订单管理",
                    "icon" => "fa-bars",
                    "uri" => "orders",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 0,
                    "title" => "用户管理",
                    "icon" => "fa-bars",
                    "uri" => "users",
                    "permission" => NULL
                ]
            ]
        );

        Encore\Admin\Auth\Database\Permission::truncate();
        Encore\Admin\Auth\Database\Permission::insert(
            [
                [
                    "name" => "All permission",
                    "slug" => "*",
                    "http_method" => "",
                    "http_path" => "*"
                ],
                [
                    "name" => "Dashboard",
                    "slug" => "dashboard",
                    "http_method" => "GET",
                    "http_path" => "/"
                ],
                [
                    "name" => "Login",
                    "slug" => "auth.login",
                    "http_method" => "",
                    "http_path" => "/auth/login\r\n/auth/logout"
                ],
                [
                    "name" => "User setting",
                    "slug" => "auth.setting",
                    "http_method" => "GET,PUT",
                    "http_path" => "/auth/setting"
                ],
                [
                    "name" => "Auth management",
                    "slug" => "auth.management",
                    "http_method" => "",
                    "http_path" => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs"
                ],
                [
                    "name" => "用户管理权限",
                    "slug" => "users permission",
                    "http_method" => "",
                    "http_path" => "/users*"
                ],
                [
                    "name" => "优惠券权限",
                    "slug" => "coupon permission",
                    "http_method" => "",
                    "http_path" => "/coupons*"
                ],
                [
                    "name" => "商品管理权限",
                    "slug" => "products permission",
                    "http_method" => "",
                    "http_path" => "/products*"
                ],
                [
                    "name" => "订单管理权限",
                    "slug" => "orders permission",
                    "http_method" => "",
                    "http_path" => "/orders*"
                ]
            ]
        );

        Encore\Admin\Auth\Database\Role::truncate();
        Encore\Admin\Auth\Database\Role::insert(
            [
                [
                    "name" => "Administrator",
                    "slug" => "administrator"
                ],
                [
                    "name" => "商品专员",
                    "slug" => "products operator"
                ],
                [
                    "name" => "订单专员",
                    "slug" => "orders operator"
                ]
            ]
        );

        // pivot tables
        DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert(
            [
                [
                    "role_id" => 1,
                    "menu_id" => 2
                ]
            ]
        );

        DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert(
            [
                [
                    "role_id" => 1,
                    "permission_id" => 1
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 2
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 3
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 7
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 8
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 2
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 3
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 6
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 7
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 8
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 9
                ]
            ]
        );

        // finish
    }
}
