@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/lib/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="br-mainpanel">
        <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="{{route('home')}}">{{__('page.home')}}</a>
                <a class="breadcrumb-item" href="#">{{__('page.purchase')}}</a>
                <a class="breadcrumb-item active" href="#">{{__('page.details')}}</a>
            </nav>
        </div>
        <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
            <h4 class="tx-gray-800 mg-b-5"><i class="fa fa-info-circle"></i> {{__('page.purchase_detail')}}</h4>
        </div>
        
        @php
            $role = Auth::user()->role->slug;
        @endphp
        <div class="br-pagebody">
            <div class="br-section-wrapper">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <div class="card card-body tx-white-8 bg-success mg-y-10 bd-0 ht-150 purchase-card">
                            <div class="row">
                                <div class="col-3">
                                    <span class="card-icon tx-70"><i class="fa fa-plug"></i></span>
                                </div>
                                <div class="col-9">
                                    <h4 class="card-title tx-white tx-medium mg-b-10">{{__('page.supplier')}}</h4>
                                    <p class="tx-16 mg-b-3">{{__('page.name')}}: @isset($purchase->supplier->name){{$purchase->supplier->name}}@endisset</p>
                                    <p class="tx-16 mg-b-3">{{__('page.email')}}: @isset($purchase->supplier->email){{$purchase->supplier->email}}@endisset</p>
                                    <p class="tx-16 mg-b-3">{{__('page.phone')}}: @isset($purchase->supplier->phone_number){{$purchase->supplier->phone_number}}@endisset</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card card-body bg-teal tx-white mg-y-10 bd-0 ht-150 purchase-card">
                            <div class="row">
                                <div class="col-3">
                                    <span class="card-icon tx-70"><i class="fa fa-truck"></i></span>
                                </div>
                                <div class="col-9">
                                    <h4 class="card-title tx-white tx-medium mg-b-10">{{__('page.store')}}</h4>
                                    <p class="tx-16 mg-b-3">{{__('page.name')}}: @isset($purchase->store->name){{$purchase->store->name}}@endisset</p>
                                    <p class="tx-16 mg-b-3">{{__('page.company')}}: @isset($purchase->store->company->name){{$purchase->store->company->name}}@endisset</p>
                                    <p class="tx-16 mg-b-3"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card card-body bg-info tx-white-8 mg-y-10 bd-0 ht-150 purchase-card">
                            <div class="row">                                
                                <div class="col-3">
                                    <span class="card-icon tx-70"><i class="fa fa-file-text-o"></i></span>
                                </div>
                                <div class="col-9">
                                    <h4 class="card-title tx-white tx-medium mg-b-10">{{__('page.reference')}}</h4>
                                    <p class="tx-16 mg-b-3">{{__('page.number')}}: {{$purchase->reference_no}}</p>
                                    <p class="tx-16 mg-b-3">{{__('page.date')}}: {{$purchase->timestamp}}</p>
                                    <p class="tx-16 mg-b-3">
                                        {{__('page.attachment')}}: 
                                        @if ($purchase->attachment != "")
                                            <a href="#" class="attachment" data-value="{{$purchase->attachment}}">&nbsp;&nbsp;&nbsp;<i class="fa fa-paperclip"></i></a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-t-20">
                    <div class="col-md-12">
                        <h5>Supplier Note</h5>
                        <p class="mx-2">@isset($purchase->supplier->note){{$purchase->supplier->note}}@endisset</p>
                    </div>
                </div>
                <div class="row mg-t-20">
                    <div class="col-md-12 table-responsive">
                        <h5>Orders Item</h5>
                        <table class="table table-bordered table-colored table-info">
                            <thead>
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
                                    $paid = $purchase->payments()->sum('amount');
                                @endphp
                                @foreach ($purchase->orders as $item)
                                @php
                                    $tax = $item->product->tax->rate;
                                    $quantity = $item->quantity;
                                    $cost = $item->cost;
                                    $tax_rate = $cost * $tax / 100;
                                    $subtotal = $item->subtotal;

                                    $total_quantity += $quantity;
                                    $total_tax_rate += $tax_rate;
                                    $total_amount += $subtotal;
                                @endphp
                                    <tr>
                                        <td>{{$loop->index+1}}</td>
                                        <td>@isset($item->product->name){{$item->product->name}} ({{$item->product->code}})@endisset</td>
                                        <td>{{$item->cost}}</td>
                                        <td>{{$item->quantity}}</td>
                                        <td>@isset($item->product->tax->name){{$item->product->tax->name}}@endisset</td>
                                        <td>{{$item->subtotal}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="tx-bold" style="text-align:right">{{__('page.total')}} (COP)</td>
                                    <td>{{$total_quantity}}</td>
                                    <td>{{$total_tax_rate}}</td>
                                    <td>{{number_format($total_amount)}}</td>
                                </tr>
                            </tbody>
                            <tfoot class="tx-bold tx-black">
                                <tr>
                                    <td colspan="5" style="text-align:right">{{__('page.discount')}} (COP)</td>
                                    <td>
                                        @if(strpos( $purchase->discount_string , '%' ) !== false)
                                            {{$purchase->discount_string}} ({{number_format($purchase->discount)}})
                                        @else
                                            {{number_format($purchase->discount)}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align:right">{{__('page.shipping')}} (COP)</td>
                                    <td>
                                        @if(strpos( $purchase->shipping_string , '%' ) !== false)
                                            {{$purchase->shipping_string}} ({{number_format($purchase->shipping)}})
                                        @else
                                            {{number_format($purchase->shipping)}}
                                        @endif
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td colspan="5" style="text-align:right">{{__('page.returns')}}</td>
                                    <td>
                                        {{number_format($purchase->returns)}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align:right">{{__('page.total_amount')}} (COP)</td>
                                    <td>{{number_format($purchase->grand_total)}}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align:right">{{__('page.paid')}} (COP)</td>
                                    <td>{{number_format($paid)}}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align:right">{{__('page.balance')}} (COP)</td>
                                    <td>{{number_format($purchase->grand_total - $paid)}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row mg-t-20">
                    <div class="col-md-12">
                        <h5>{{__('page.note')}}</h5>
                        <p class="mx-2">{{$purchase->note}}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-6 col-md-4 card card-body tx-white-8 bg-success mg-l-15 bd-0 d-block" style="float:right !important;">                            
                        <h6 class="card-title tx-white tx-medium mg-b-5">{{__('page.created_by')}} @isset($purchase->user->name){{$purchase->user->name}}@endisset</h6>
                        <h6 class="card-title tx-white tx-medium mg-y-5">{{__('page.created_at')}} {{$purchase->created_at}}</h6>
                    </div>
                    <div class="col-6 col-md-7 text-right">
                        <a href="{{route('purchase.index')}}" class="btn btn-secondary"><i class="fa fa-credit-card"></i>  {{__('page.purchases_list')}}</a>
                        <a href="{{route('payment.index', ['purchase', $purchase->id])}}" class="btn btn-info"><i class="icon ion-cash"></i>     {{__('page.payment_list')}}</a>
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
