<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Report</title>
    
    <link href="{{asset('master/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('master/css/style.css')}}" rel="stylesheet" type="text/css" />
    <style>
            body {
                color: #584747;
                border: solid 1px black;
                padding: 10px;
                background-color: #FFF;
                /* background: url('{{asset("images/bg_pdf.jpg")}}') no-repeat; */
                /* background-size: 100% 100%; */
            }
            .table td, .table th {
                padding: .4rem;
            }
            .main {
            }
            .title {
                margin-top: 30px;
                text-align:center;
                font-size:30px;
                font-weight: 700;
                text-decoration:underline;
            }
            .value {
                font-size: 14px;
                font-weight: 500;
                text-decoration: underline;
            }
            .field {
                font-size: 12px;
            }
            td.value {
                /* line-height: 1; */
            }
            .table-bordered, .table-bordered td, .table-bordered th {
                border: 1px solid #2d2d2d;
            }
            .table thead th {
                border-bottom: 2px solid #2d2d2d;
            }
            #table-customer {
                font-size: 14px;
                font-weight: 600;
            }
            #table-item {
                font-size: 14px;
                color: #584747;
            }
            .footer {
                position: absolute;
                bottom: 10px;;
            }
            .footer tr td {
                font-size: 11px;
                color: #584747;
                text-align: center;
                line-height: 1;
            }
    
        </style>
</head>
<body>
    <div class="main">
        <h2 class="text-center font-weight-bold mt-5">{{__('page.purchase_report')}}</h2>
        <table class="w-100 mt-5" id="table-customer">
            <tbody>
                <tr>
                    <td class="w-50" valign="top">
                        <h5 class="mb-0 text-uppercase">{{__('page.purchase')}}</h5>
                        <p class="my-0 text-center" style="font-size:24px">{{$purchase->reference_no}}</p>
                        
                    </td>
                    <td class="w-50 pt-3 text-right" rowspan="2" valign="top">
                        @if($purchase->supplier)
                            <table class="w-100">
                                <tr><td class="value">{{$purchase->supplier->name}}</td></tr>
                                <tr><td class="value">{{$purchase->supplier->company}}</td></tr>
                                <tr><td class="value">{{$purchase->supplier->email}}</td></tr>
                                <tr><td class="value">{{$purchase->supplier->phone_number}}</td></tr>
                                <tr><td class="value">{{$purchase->supplier->city}}</td></tr>
                                <tr><td class="value">{{$purchase->supplier->address}}</td></tr>
                            </table>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="pt-1">
                        <table class="w-100">
                            <tr>
                                <td class="field">{{__('page.date')}} : </td>    
                                <td class="value">{{date('d/m/Y', strtotime($purchase->timestamp))}}</td>
                            </tr>
                            <tr>
                                <td class="field">{{__('page.company')}} : </td>
                                <td class="value">@if($purchase->company){{ $purchase->company->name }}@endif</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <h4 class="mt-e" style="font-weight: 600;">{{__('page.order_items')}}</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>{{__('page.product')}}</th>
                    <th>{{__('page.cost')}}</th>
                    <th>{{__('page.quantity')}}</th>
                    <th>{{__('page.product_tax')}}</th>
                    <th>{{__('page.subtotal')}}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_quantity = 0;
                    $total_tax_rate = 0;
                    $total_amount = 0;
                    $paid = $purchase->payments()->where('status', 1)->sum('amount');
                @endphp
                @foreach ($purchase->orders as $item)
                    @php
                        $tax = ($item->product->tax) ? $item->product->tax->rate : 0;
                        $quantity = $item->quantity;
                        $cost = $item->cost;
                        $tax_rate = $cost * $tax / 100;
                        $subtotal = $item->subtotal;

                        $total_quantity += $quantity;
                        $total_tax_rate += $tax_rate;
                        $total_amount += $subtotal;
                    @endphp
                    <tr>
                        <td>
                            @if($item->product)
                                {{$item->product->name}}
                                <p class="text-muted mb-0">{{$item->product->code}}</p>
                            @endif
                        </td>
                        
                        <td>{{number_format($item->cost)}}</td>
                        <td>{{$item->quantity}}</td>
                        <td>@isset($item->product->tax->name){{$item->product->tax->name}}@endisset</td>
                        <td>{{number_format($item->subtotal)}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align:right">{{__('page.total')}} (COP)</th>
                    <th>{{$total_quantity}}</th>
                    <th>{{$total_tax_rate}}</th>
                    <th>{{number_format($total_amount)}}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align:right">{{__('page.discount')}} (COP)</th>
                    <th>
                        @if(strpos( $purchase->discount_string , '%' ) !== false)
                            {{$purchase->discount_string}} ({{number_format($purchase->discount)}})
                        @else
                            {{number_format($purchase->discount)}}
                        @endif
                    </th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align:right">{{__('page.shipping')}} (COP)</th>
                    <th>
                        @if(strpos( $purchase->shipping_string , '%' ) !== false)
                            {{$purchase->shipping_string}} ({{number_format($purchase->shipping)}})
                        @else
                            {{number_format($purchase->shipping)}}
                        @endif
                    </th>
                </tr>
                
                <tr>
                    <th colspan="4" style="text-align:right">{{__('page.returns')}}</th>
                    <th>
                        @php
                            $preturns = $purchase->preturns()->where('status', 1)->sum('amount');
                            $grand_total = $purchase->grand_total - $preturns;
                        @endphp
                        {{number_format($preturns)}}
                    </th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align:right; vertical-align: middle;">{{__('page.grand_total')}}</th>
                    <th class="text-primary" style="font-size:25px">{{number_format($grand_total)}}</th>
                </tr>
            </tfoot>
        </table>
        <h4 class="mt-e" style="font-weight: 600;">{{__('page.payment_list')}}</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>{{__('page.date')}}</th>
                    <th>{{__('page.reference_no')}}</th>
                    <th>{{__('page.amount')}}</th>
                    <th>{{__('page.status')}}</th>
                    <th>{{__('page.note')}}</th>
                </tr>
            </thead>
            <tbody>                                
                @foreach ($purchase->payments as $item)
                    <tr>
                        <td>{{date('d/m/Y', strtotime($item->timestamp))}}</td>
                        <td>{{$item->reference_no}}</td>
                        <td>{{number_format($item->amount)}}</td>
                        <td>
                            @if($item->status)
                                <span class="text-success">{{__('page.approved')}}</span>
                            @else
                                <span class="text-danger">{{__('page.pending')}}</span>
                            @endif
                        </td>
                        <td>
                            <span class="tx-info note">{{$item->note}}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align:right">{{__('page.paid')}}</th>
                    <th>{{number_format($paid)}}</th>
                </tr>
                <tr>
                    <th colspan="4" style="text-align:right">{{__('page.balance')}}</th>
                    <th>{{number_format($grand_total - $paid)}}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>