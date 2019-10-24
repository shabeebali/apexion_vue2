<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CityStateCountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(Storage::disk('db')->get('countries.sql')); 
        $this->command->info('Countries tables seeded!');
        DB::unprepared(Storage::disk('db')->get('states.sql')); 
        $this->command->info('States tables seeded!');
        DB::unprepared(Storage::disk('db')->get('cities.sql')); 
        $this->command->info('Cities tables seeded!');
    }
}
