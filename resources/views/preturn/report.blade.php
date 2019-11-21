<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{__('page.return_report')}}</title>
    
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
        <h2 class="text-center font-weight-bold mt-5">{{__('page.return_report')}}</h2>
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
        <h4 class="mt-e" style="font-weight: 600;">{{__('page.return_list')}}</h4>
        <table class="table mt-2">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>{{__('page.date')}}</th>
                    <th>{{__('page.reference_no')}}</th>
                    <th>{{__('page.amount')}}</th> 
                    <th>{{__('page.note')}}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $data = $purchase->preturns;
                @endphp                                
                @foreach ($data as $item)
                    <tr class="@if($item->status == 0) text-danger @endif">
                        <td>{{ $loop->index + 1 }}</td>
                        <td class="date">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                        <td class="reference_no">{{$item->reference_no}}</td>
                        <td class="amount" data-value="{{$item->amount}}">{{number_format($item->amount)}}</td>
                        <td class="" data-path="{{$item->attachment}}">
                            <span class="tx-info note">{{$item->note}}</span>&nbsp;
                            @if($item->attachment != "")
                                <a href="{{asset($item->attachment)}}" class="attachment"><i class="fa fa-paperclip"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>