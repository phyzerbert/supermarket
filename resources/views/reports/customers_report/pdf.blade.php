<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{__('page.customer_report')}}</title>
    <link href="{{asset('master/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('master/css/style.css')}}" rel="stylesheet" type="text/css" />
    <style>
        body {
            border: solid 1px black;
            padding: 10px;
            background-color: #FFF;
        }
        .title {
            margin-top: 30px;
            text-align:center;
            font-size:30px;
            font-weight: 800;
            text-decoration:underline;
        }
        .value {
            text-decoration: underline;
            font-weight: 600;
        }
        .table-bordered, .table-bordered td, .table-bordered th {
            border: 1px solid #2d2d2d;
        }
        .table thead th {
            border-bottom: 2px solid #2d2d2d;
        }
        #table-customer {
            font-size: 14px;
            font-weight: 500;
            color: black;
        }
        #table-customer tbody td {
            height: 25px;
        }
    </style>
</head>
<body>
    <h1 class="title">{{__('page.customer_report')}}</h1>

    @php
        $sales_array = $customer->sales()->pluck('id');
        $total_sales = $customer->sales()->count();
        $total_amount = $customer->sales()->sum('grand_total');
        $paid = \App\Models\Payment::whereIn('paymentable_id', $sales_array)->where('paymentable_type', 'App\Models\Sale')->sum('amount');
    @endphp

    <table class="w-100 mt-5" id="table-customer">
        <tbody>
            <tr>
                <td>
                    <table class="w-100">
                        <tbody>
                            <tr>
                                <td>{{__('page.name')}} : </td>
                                <td class="value">{{$customer->name}}</td>
                            </tr>
                            <tr>
                                <td>{{__('page.company')}} : </td>
                                <td class="value">{{$customer->company}}</td>
                            </tr>
                            <tr>
                                <td>{{__('page.email')}} : </td>
                                <td class="value">{{$customer->email}}</td>
                            </tr>
                            <tr>
                                <td>{{__('page.phone_number')}} : </td>
                                <td class="value">{{$customer->phone_number}}</td>
                            </tr>
                            <tr>
                                <td>{{__('page.city')}} : </td>
                                <td class="value">{{$customer->city}}</td>
                            </tr>
                            <tr>
                                <td>{{__('page.address')}} : </td>
                                <td class="value">{{$customer->address}}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td valign="bottom">
                    <table class="w-100">
                        <tbody>
                            <tr>
                                <td>{{__('page.total_amount')}} : </td>
                                <td class="value">{{number_format($total_amount)}}</td>
                            </tr>
                            <tr>
                                <td>{{__('page.paid')}} : </td>
                                <td class="value">{{number_format($paid)}}</td>
                            </tr>
                            <tr>
                                <td>{{__('page.balance')}} : </td>
                                <td class="value">{{number_format($total_amount - $paid)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <h3 class="mt-5" style="font-size: 18px; font-weight: 500;">{{__('page.sales')}}</h3>
    <table class="table">
        <thead class="table-primary">
            <tr class="bg-blue">
                <th style="width:25px;">#</th>
                <th>
                    {{__('page.date')}}
                </th>
                <th>{{__('page.reference_no')}}</th>
                <th>{{__('page.grand_total')}}</th>
                <th>{{__('page.paid')}}</th>
                <th>{{__('page.balance')}}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $footer_grand_total = $footer_paid = 0;
                $data = $customer->sales;
            @endphp
            @foreach ($data as $item)
                @php
                    $paid = $item->payments()->sum('amount');
                    $grand_total = $item->grand_total;
                    $footer_grand_total += $grand_total;
                    $footer_paid += $paid;
                @endphp
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                    <td class="reference_no">{{$item->reference_no}}</td>
                    <td class="grand_total"> {{number_format($grand_total)}} </td>
                    <td class="paid"> {{ number_format($paid) }} </td>
                    <td class="balance" data-value="{{$grand_total - $paid}}"> {{number_format($grand_total - $paid)}} </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">{{__('page.total')}}</th>
                <th>{{number_format($footer_grand_total)}}</th>
                <th>{{number_format($footer_paid)}}</th>
                <th>{{number_format($footer_grand_total - $footer_paid)}}</th>  
            </tr>
        </tfoot>
    </table>
</body>
</html>