<?php

use Illuminate\Database\Seeder;

use App\Models\Currency;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::create(['name' => 'Bolivar', 'rate' => 28926]);
        Currency::create(['name' => 'Dollar', 'rate' => 1]);
        Currency::create(['name' => 'Euro', 'rate' => 0.9]);
    }
}
