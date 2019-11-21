<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SupplierExport implements WithMultipleSheets
{
    use Exportable;
    
    protected $purchases;
    protected $payments;

    public function __construct($purchases, $payments)
    {
        $this->purchases = $purchases;
        $this->payments = $payments;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new SupplierPurchaseSheet($this->purchases);
        $sheets[] = new PaymentSheet($this->payments, 'purchase');

        return $sheets;
    }
}
