<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CustomerExport implements WithMultipleSheets
{
    use Exportable;
    
    protected $sales;
    protected $payments;

    public function __construct($sales, $payments)
    {
        $this->sales = $sales;
        $this->payments = $payments;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new CustomerSaleSheet($this->sales);
        $sheets[] = new PaymentSheet($this->payments, 'sale');

        return $sheets;
    }
}
