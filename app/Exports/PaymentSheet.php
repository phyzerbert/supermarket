<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentSheet implements FromArray, WithHeadings, WithTitle
{
    private $payments;
    private $type;

    public function __construct($payments, $type)
    {
        $this->payments = $payments;
        $this->type = $type;
    }

    public function array(): array
    {
        $data = $this->payments;
        $payments = array();
        $total_amount = 0;                           
        $i = 0;
        foreach ($data as $item){                               
            $total_amount += $item->amount;
            $payments[$i]['no'] = $i+1;
            $payments[$i]['date'] = date('Y-m-d H:i', strtotime($item->timestamp));            
            $payments[$i]['reference_no'] = $item->reference_no;
            $payments[$i]['paymentable_ref'] = $item->paymentable->reference_no;
            $payments[$i]['amount'] = number_format($item->amount);
            $payments[$i]['note'] = $item->note;
            $i++;
        }
        $payments[$i] = ['', '', '', '', number_format($total_amount), ''];
        return $payments;
    }

    public function headings(): array
    {
        $paymentable = '';
        if($this->type == 'purchase'){
            $paymentable = __('page.purchase');
        }elseif($this->type == 'sale'){
            $paymentable = __('page.sale');
        }
        return [
            'No',
            __('page.date'),
            __('page.reference_no'),
            $paymentable,
            __('page.amount'),
            __('page.note'),
        ];
    }

    public function title(): string
    {
        return __('page.payments');
    }
}
