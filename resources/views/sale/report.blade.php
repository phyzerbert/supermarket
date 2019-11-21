<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{__('page.sale_report')}}</title>
    
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
        <h2 class="text-center font-weight-bold mt-5">{{__('page.sale_report')}}</h2>
        <table class="w-100 mt-5" id="table-customer">
            <tbody>
                <tr>
                    <td class="w-50" valign="top">
                        <h5 class="mb-0 text-uppercase">{{__('page.sale')}}</h5>
                        <p class="my-0 text-center" style="font-size:24px">{{$sale->reference_no}}</p>
                        
                    </td>
                    <td class="w-50 pt-3 text-right" rowspan="2" valign="top">
                        @if($sale->customer)
                            <table class="w-100">
                                <tr><td class="value">{{$sale->customer->name}}</td></tr>
                                <tr><td class="value">{{$sale->customer->company}}</td></tr>
                                <tr><td class="value">{{$sale->customer->email}}</td></tr>
                                <tr><td class="value">{{$sale->customer->phone_number}}</td></tr>
                                <tr><td class="value">{{$sale->customer->city}}</td></tr>
                                <tr><td class="value">{{$sale->customer->address}}</td></tr>
                            </table>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="pt-1">
                        <table class="w-100">
                            <tr>
                                <td class="field">{{__('page.date')}} : </td>    
                                <td class="value">{{date('d/m/Y', strtotime($sale->timestamp))}}</td>
                            </tr>
                            <tr>
                                <td class="field">{{__('page.company')}} : </td>
                                <td class="value">@if($sale->company){{ $sale->company->name }}@endif</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <h4 class="mt-e" style="font-weight: 600;">{{__('page.order_items')}}</h4>
        <table class="table">
            <thead class="table-info">
                <tr>
                    <th width="50">#</th>
                    <th>{{__('page.product')}}</th>
                    <th>{{__('page.price')}}</th>
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
                    $paid = $sale->payments()->sum('amount');
                @endphp
                @foreach ($sale->orders as $item)
                    @php
                        $tax = ($item->product->tax) ? $item->product->tax->rate : 0;
                        $quantity = $item->quantity;
                        $price = $item->price;
                        $tax_rate = $price * $tax / 100;
                        $subtotal = $item->subtotal;

                        $total_quantity += $quantity;
                        $total_tax_rate += $tax_rate;
                        $total_amount += $subtotal;
                    @endphp
                    <tr>
                        <td>{{$loop->index+1}}</td>
                        <td>
                            @if($item->product)
                                {{$item->product->name}}
                                <p class="text-muted mb-0">{{$item->product->code}}</p>
                            @endif
                        </td>
                        
                        <td>{{number_format($item->price)}}</td>
                        <td>{{$item->quantity}}</td>
                        <td>@isset($item->product->tax->name){{$item->product->tax->name}}@endisset</td>
                        <td>{{number_format($item->subtotal)}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="tx-bold tx-black">
                <tr>
                    <th colspan="3" class="tx-bold" style="text-align:right">{{__('page.total')}} (COP)</th>
                    <th>{{$total_quantity}}</th>
                    <th>{{$total_tax_rate}}</th>
                    <th>{{number_format($total_amount)}}</th>
                </tr>
                <tr>
                    <th colspan="5" style="text-align:right">{{__('page.total_amount')}} (COP)</th>
                    <th>{{number_format($sale->grand_total)}}</th>
                </tr>
                <tr>
                    <th colspan="5" style="text-align:right">{{__('page.paid')}} (COP)</th>
                    <th>{{number_format($paid)}}</th>
                </tr>
                <tr>
                    <th colspan="5" style="text-align:right">{{__('page.balance')}} (COP)</th>
                    <th>{{number_format($sale->grand_total - $paid)}}</th>
                </tr>
            </tfoot>
        </table>
        <div class="mt-5">
            <h4 class="text-right pr-3">
                {{__('page.grand_total')}} : <span class="text-primary">{{number_format($sale->grand_total)}}</span> 
            </h4>
        </div>
    </div>
</body>
</html>