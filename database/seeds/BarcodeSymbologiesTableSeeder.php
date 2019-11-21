<?php

use Illuminate\Database\Seeder;
use App\Models\BarcodeSymbology;

class BarcodeSymbologiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BarcodeSymbology::Create(['name' => 'Code25']);
        BarcodeSymbology::Create(['name' => 'Code39']);
        BarcodeSymbology::Create(['name' => 'Code128']);
        BarcodeSymbology::Create(['name' => 'EAN8']);
        BarcodeSymbology::Create(['name' => 'EAN13']);
        BarcodeSymbology::Create(['name' => 'UPC-A']);
        BarcodeSymbology::Create(['name' => 'UPC-E']);
    }
}
