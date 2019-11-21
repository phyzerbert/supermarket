<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SupplierPurchaseSheet implements FromArray, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $data = $this->data;
        $purchases = array();
        $footer_grand_total = $footer_paid = 0;
        $i = 0;
        foreach ($data as $item) {

            $paid = $item->payments()->sum('amount');
            $grand_total = $item->grand_total;
            $footer_grand_total += $grand_total;
            $footer_paid += $paid;

            $purchases[$i] = array();
            $purchases[$i]['no'] = $i+1;
            $purchases[$i]['date'] = date('Y-m-d H:i', strtotime($item->timestamp));
            $purchases[$i]['reference_no'] = $item->reference_no;
            $purchases[$i]['company'] = isset($item->company->name) ? $item->company->name : '';
            $purchases[$i]['status'] = $item->status == 1 ? __('page.approved') : __('page.pending');
            $purchases[$i]['grand_total'] = number_format($grand_total);
            $purchases[$i]['paid'] = number_format($paid);
            $purchases[$i]['balance'] = number_format($grand_total - $paid);

            if($paid == 0) {
                $purchases[$i][__('page.payment_status')] = __('page.pending');
            }elseif($paid < $grand_total) {
                $purchases[$i][__('page.payment_status')] = __('page.partial');
            }else {
                $purchases[$i][__('page.payment_status')] = __('page.paid');
            }
            $i++;
        }
        $purchases[$i] = ['', '', '', '', '', number_format($footer_grand_total), number_format($footer_paid), number_format($footer_grand_total - $footer_paid), ''];
        return $purchases;
    }

    public function headings(): array
    {
        return [
            'No',
            __('page.date'),
            __('page.reference_no'),
            __('page.company'),
            __('page.status'),
            __('page.grand_total'),
            __('page.paid'),
            __('page.balance'),
            __('page.payment_status'),
        ];
    }

    public function title(): string
    {
        return __('page.purchases');
    }
}
