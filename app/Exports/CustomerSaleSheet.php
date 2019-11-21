<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomerSaleSheet implements FromArray, WithHeadings, WithTitle
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
            $sales[$i]['company'] = isset($item->company->name) ? $item->company->name : '';
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
            __('page.company'),
            // __('page.status'),
            __('page.grand_total'),
            __('page.paid'),
            __('page.balance'),
            __('page.payment_status'),
        ];
    }

    public function title(): string
    {
        return __('page.sales');
    }
}
