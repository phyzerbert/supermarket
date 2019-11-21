@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/lib/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-info-circle"></i> {{__('page.sale_detail')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('sale.index')}}">{{__('page.sales')}}</a></li>
                        <li class="active">{{__('page.detail')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body">
                <div class="row row-deck">
                    <div class="col-lg-4">
                        <div class="card card-fill card-body bg-success detail-card">
                            <div class="row">
                                <div class="col-xl-3 text-center">
                                    <div class="detail-card-icon bg-warning"><i class="fa fa-plug"></i></div>
                                </div>
                                <div class="col-xl-9">
                                    <h3 class="text-white mb-2">{{__('page.customer')}}</h3>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.name')}}</strong> : @isset($sale->customer->name){{$sale->customer->name}}@endisset</p>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.email')}}</strong> : @isset($sale->customer->email){{$sale->customer->email}}@endisset</p>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.phone')}}</strong> : @isset($sale->customer->phone_number){{$sale->customer->phone_number}}@endisset</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-fill card-body bg-info detail-card">
                            <div class="row">
                                <div class="col-xl-3 text-center">
                                    <div class="detail-card-icon bg-white"><i class="fa fa-truck"></i></div>
                                </div>
                                <div class="col-xl-9">
                                    <h3 class="text-white mb-2">{{__('page.store')}}</h3>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.name')}}</strong> : @isset($sale->store->name){{$sale->store->name}}@endisset</p>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.company')}}</strong> : @isset($sale->store->company->name){{$sale->store->company->name}}@endisset</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">                    
                        <div class="card card-fill card-body bg-secondary detail-card">
                            <div class="row">
                                <div class="col-xl-3 text-center">
                                    <div class="detail-card-icon bg-primary text-light"><i class="fa fa-file-text-o"></i></div>
                                </div>
                                <div class="col-xl-9">
                                    <h3 class="text-white mb-2">{{__('page.reference')}}</h3>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.number')}}</strong> : {{$sale->reference_no}}</p>
                                    <p class="text-light" style="font-size:16px;"><strong>{{__('page.date')}}</strong> : {{$sale->timestamp}}</p>
                                    <p class="text-light" style="font-size:16px;">
                                        <strong>{{__('page.attachment')}} : </strong>
                                        @if ($sale->attachment != "")
                                            <a href="#" class="attachment" data-value="{{$sale->attachment}}">&nbsp;&nbsp;&nbsp;<i class="fa fa-paperclip"></i></a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mg-t-20">
                    <div class="col-md-12 table-responsive">
                        <h5>{{__('page.order_items')}}</h5>
                        <table class="table table-bordered">
                            <thead class="table-info">
                                <tr>
                                    <th class="wd-40">#</th>
                                    <th>{{__('page.product_name_code')}}</th>
                                    <th>{{__('page.product_cost')}}</th>
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
                                    $tax = $item->product->tax->rate;
                                    $quantity = $item->quantity;
                                    $cost = $item->product->cost;
                                    $tax_rate = $cost * $tax / 100;
                                    $subtotal = $quantity*($cost + $tax_rate);

                                    $total_quantity += $quantity;
                                    $total_tax_rate += $tax_rate;
                                    $total_amount += $subtotal;
                                @endphp
                                    <tr>
                                        <td>{{$loop->index+1}}</td>
                                        <td>{{$item->product->name}} ({{$item->product->code}})</td>
                                        <td>{{$item->product->cost}}</td>
                                        <td>{{$item->quantity}}</td>
                                        <td>{{$item->product->tax->name}}</td>
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
                                    <th>{{number_format($total_amount)}}</td>
                                </tr>
                                <tr>
                                    <th colspan="5" style="text-align:right">{{__('page.paid')}} (COP)</th>
                                    <th>{{number_format($paid)}}</td>
                                </tr>
                                <tr>
                                    <th colspan="5" style="text-align:right">{{__('page.balance')}} (COP)</th>
                                    <th>{{number_format($total_amount - $paid)}}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-12 col-md-3">
                        <div class="card card-body card-fill bg-success ">
                            <h6 class="card-title text-white mb-2">{{__('page.created_by')}} @isset($sale->user->name){{$sale->user->name}}@endisset</h6>
                            <h6 class="card-title text-white">{{__('page.created_at')}} {{$sale->created_at}}</h6>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9 text-right">
                        <a href="{{route('sale.index')}}" class="btn btn-secondary"><i class="fa fa-credit-card"></i> {{__('page.sales_list')}}</a>
                        <a href="{{route('payment.index', ['sale', $sale->id])}}" class="btn btn-info"><i class="icon ion-cash"></i> {{__('page.payment_list')}}</a>
                    </div>
                </div>
            </div>
        </div>                
    </div>

@endsection

@section('script')
<script src="{{asset('master/lib/select2/js/select2.min.js')}}"></script>
<script>
    $(document).ready(function () {        
        $(".attachment").click(function(e){
            e.preventDefault();
            let path = '{{asset("/")}}' + $(this).data('value');
            console.log(path)
            // $("#attachment").attr('src', path);
            $("#image_preview").html('')
            $("#image_preview").verySimpleImageViewer({
                imageSource: path,
                frame: ['100%', '100%'],
                maxZoom: '900%',
                zoomFactor: '10%',
                mouse: true,
                keyboard: true,
                toolbar: true,
            });
            $("#attachModal").modal();
        });
    });
</script>
@endsection
