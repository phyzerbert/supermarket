<?php

use Illuminate\Database\Seeder;
use App\Models\Tax;

class TaxesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tax::create([
            'name' => 'IMP CONS',
            'code' => 'IMPCO',
            'rate' => 8,
            'type' => 1,
        ]);

        Tax::create([
            'name' => 'IVA 16%',
            'code' => 'IVA16',
            'rate' => 16,
            'type' => 1,
        ]);

        Tax::create([
            'name' => 'IVA 19%',
            'code' => 'IVA19',
            'rate' => 19,
            'type' => 1,
        ]);
        
        Tax::create([
            'name' => 'No Imp',
            'code' => 'Ni',
            'rate' => 0,
            'type' => 1,
        ]);
    }
}
