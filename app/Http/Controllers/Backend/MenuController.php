<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $menu = [];
        $menu[] =[
          'title'=>'Dashboard',
          'target'=>'/',
          'icon'=>'mdi-gauge'
        ];
        $menu[] = [
            'title'=>'Catalog',
            'target'=>'/products',
            'icon'=>'mdi-inbox-multiple',
            'children'=>[
                [
                  'title'=>'Products',
                  'target'=>'/products',
                ],
                [
                  'title'=>'Products(Pending)',
                  'target'=>'/products/pending',
                ],
                [
                  'title'=>'Products(Tally)',
                  'target'=>'/products/tally',
                ],
                [
                  'title'=>'Categories',
                  'target'=>'/categories',
                ],
                [
                  'title'=>'Taxonomy',
                  'target'=>'/taxonomies',
                ],
            ]
        ];
        $menu[] = [
            'title'=>'CRM',
            'target'=>'/customers',
            'icon'=>'mdi-handshake',
            'children'=>[
                [
                  'title'=>'Customers',
                  'target'=>'/customers',
                ],
                [
                  'title'=>'Customers(Pending)',
                  'target'=>'/customers/pending',
                ],
                [
                  'title'=>'Customers(Tally)',
                  'target'=>'/customers/tally',
                ],
            ]
        ];
        $menu[] = [
          'title'=>'Inventory',
          'target'=>'/inventory',
          'icon'=>'mdi-warehouse',
          'children'=>[
            [
              'title'=>'Warehouse',
              'target'=>'/warehouses',
            ],
          ]
        ];
        $menu[] =[
          'title'=>'Sales',
          'target'=>'/sales',
          'icon'=>'mdi-printer-pos',
          'children'=>[
            [
              'title'=>'Sale Order',
              'target'=>'/sales',
            ],
            [
              'title'=>'Pricelist',
              'target'=>'/pricelists',
            ],
          ]
        ];
        $menu[] = [
          'title'=>'Settings',
          'target'=>'/users',
          'icon'=>'mdi-account',
          'children'=>[
            [
              'title'=>'Users',
              'target'=>'/users',
            ],
            [
              'title'=>'Roles',
              'target'=>'/user_roles',
            ],
            [
              'title'=>'Configuration',
              'target'=>'/config',
            ],
          ]
        ];
        return $menu;
    }
}
