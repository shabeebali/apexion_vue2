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
            'title'=>'Products',
            'target'=>'/products',
            'icon'=>'mdi-inbox-multiple',
            'children'=>[
                [
                  'title'=>'Products',
                  'target'=>'/products',
                ],
                [
                  'title'=>'Pending',
                  'target'=>'/products/pending',
                ],
                [
                  'title'=>'Tally',
                  'target'=>'/products/tally',
                ],
            ]
        ];
        $menu[] =[
          'title'=>'Categories',
          'target'=>'/categories',
          'icon'=>'mdi-book-variant',
          'children'=>[
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
          'title'=>'Inventory',
          'target'=>'/inventory',
          'icon'=>'mdi-warehouse',
          'children'=>[
            [
              'title'=>'Inventory',
              'target'=>'/inventory',
            ],
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
              'title'=>'Sales',
              'target'=>'/sales',
            ],
            [
              'title'=>'Pricelist',
              'target'=>'/pricelists',
            ],
          ]
        ];
        $menu[] = [
          'title'=>'Users',
          'target'=>'/users',
          'icon'=>'mdi-account',
          'children'=>[
            [
              'title'=>'Users',
              'target'=>'/users',
            ],
            [
              'title'=>'Roles',
              'target'=>'/users/roles',
            ],
          ]
        ];
        return $menu;
    }
}
