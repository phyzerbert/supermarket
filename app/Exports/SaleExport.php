<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SaleExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $data = $this->data;
        $sales = array();
        $footer_grand_total = $footer_paid = 0;
        $i = 0;
        foreach ($data as $item) {

            $paid = $item->payments()->sum('amount');
            $grand_total = $item->grand_total;
            $footer_grand_total += $grand_total;
            $footer_paid += $paid;

            $sales[$i] = array();
            $sales[$i]['no'] = $i+1;
            $sales[$i]['date'] = date('Y-m-d H:i', strtotime($item->timestamp));
            $sales[$i]['reference_no'] = $item->reference_no;
            $sales[$i]['customer'] = isset($item->customer->company) ? $item->customer->company : '';
            // $sales[$i]['status'] = $item->status == 1 ? __('page.approved') : __('page.pending');
            $sales[$i]['grand_total'] = number_format($grand_total);
            $sales[$i]['paid'] = number_format($paid);
            $sales[$i]['balance'] = number_format($grand_total - $paid);

            if($paid == 0) {
                $sales[$i][__('page.payment_status')] = __('page.pending');
            }elseif($paid < $grand_total) {
                $sales[$i][__('page.payment_status')] = __('page.partial');
            }else {
                $sales[$i][__('page.payment_status')] = __('page.paid');
            }
            $i++;
        }
        $sales[$i] = ['', '', '', '', number_format($footer_grand_total), number_format($footer_paid), number_format($footer_grand_total - $footer_paid), ''];
        return $sales;
    }
    
    public function headings(): array
    {
        return [
            'No',
            __('page.date'),
            __('page.reference_no'),
            __('page.customer'),
            // __('page.status'),
            __('page.grand_total'),
            __('page.paid'),
            __('page.balance'),
            __('page.payment_status'),
        ];
    }
}
