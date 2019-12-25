<?php

use Illuminate\Database\Seeder;
use App\Model\Config;
class ConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::create([
        	'name' => 'so_default_wh',
        	'value' => '1',
        ]);
        Config::create([
            'name' => 'so_default_pl',
            'value' => '1',
        ]);
    }
}
