@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-info-circle"></i> {{__('page.purchase_detail')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li>{{__('page.purchase')}}</li>
                        <li class="active">{{__('page.details')}}</li>
                    </ol>
                </div>
            </div>
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-fill card-body bg-success detail-card">
                        <div class="row">
                            <div class="col-xl-3 text-center">
                                <div class="detail-card-icon bg-warning"><i class="fa fa-plug"></i></div>
                            </div>
                            <div class="col-xl-9">
                                <h3 class="text-white mb-2">{{__('page.supplier')}}</h3>
                                <p class="text-light" style="font-size:16px;"><strong>{{__('page.name')}}</strong> : @isset($order->supplier->name){{$order->supplier->name}}@endisset</p>
                                <p class="text-light" style="font-size:16px;"><strong>{{__('page.email')}}</strong> : @isset($order->supplier->email){{$order->supplier->email}}@endisset</p>
                                <p class="text-light" style="font-size:16px;"><strong>{{__('page.phone')}}</strong> : @isset($order->supplier->phone_number){{$order->supplier->phone_number}}@endisset</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-fill card-body bg-info detail-card">
                        <div class="row">
                            <div class="col-xl-3 text-center">
                                <div class="detail-card-icon bg-success"><i class="fa fa-file-text-o"></i></div>
                            </div>
                            <div class="col-xl-9">
                                <h3 class="text-white mb-2">{{__('page.reference')}}</h3>
                                <p class="text-light" style="font-size:16px;"><strong>{{__('page.number')}}</strong> : {{$order->reference_no}}</p>
                                <p class="text-light" style="font-size:16px;"><strong>{{__('page.date')}}</strong> : {{$order->timestamp}}</p>
                                <p class="text-light" style="font-size:16px;">
                                    <strong>{{__('page.attachment')}}</strong> : 
                                    @if ($order->attachment != "")
                                        <a href="#" class="attachment" data-value="{{$order->attachment}}">&nbsp;&nbsp;&nbsp;<i class="fa fa-paperclip"></i></a>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">                    
                    <div class="card card-fill card-body bg-success detail-card">
                        <div class="row">
                            <div class="col-xl-3 text-center">
                                <div class="detail-card-icon bg-primary text-light"><i class="fa fa-calendar"></i></div>
                            </div>
                            <div class="col-xl-9">
                                <h3 class="text-white mb-2">{{__('page.created_at')}}</h3>
                                <p class="text-light" style="font-size:16px;"><strong>{{__('page.created_by')}}</strong> : @isset($order->user->name){{$order->user->name}}@endisset</p>
                                <p class="text-light" style="font-size:16px;"><strong>{{__('page.created_at')}}</strong> : {{$order->created_at}}</p>
                                <p class="text-light" style="font-size:16px;"><strong></strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 table-responsive">
                    <h4>{{__('page.order_items')}}</h4>
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr>
                                <th class="wd-40">#</th>
                                <th>{{__('page.product_code')}}</th>
                                <th>{{__('page.product_name')}}</th>
                                <th>{{__('page.product_cost')}}</th>
                                <th>{{__('page.discount')}}</th>
                                <th>{{__('page.quantity')}}</th>
                                <th>{{__('page.subtotal')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_discount = 0;
                                $total_amount = 0;
                            @endphp
                            @foreach ($order->items as $item)
                                @php
                                    $discount = $item->discount;
                                    $subtotal = $item->subtotal;

                                    $total_discount += $discount;
                                    $total_amount += $subtotal;
                                @endphp
                                <tr>
                                    <td>{{$loop->index+1}}</td>
                                    <td>@isset($item->product->code){{$item->product->code}}@endisset</td>
                                    <td>@isset($item->product->name){{$item->product->name}}@endisset</td>
                                    <td>{{number_format($item->cost - $item->discount)}}</td>
                                    <td>
                                        @if(strpos( $item->discount_string , '%' ) !== false)
                                            {{$item->discount_string}} ({{number_format($item->discount)}})
                                        @else
                                            {{number_format($item->discount)}}
                                        @endif
                                    </td>
                                    <td>{{$item->quantity}}</td>
                                    <td>{{number_format($item->subtotal)}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="tx-bold tx-black">
                            <tr>
                                <th colspan="4" class="text-right">{{__('page.total')}} </th>
                                <th>{{number_format($total_discount)}}</th>
                                <th></th>
                                <th>{{number_format($total_amount)}}</th>
                            </tr>
                            <tr>
                                <th colspan="6" style="text-align:right">{{__('page.total_amount')}} </th>
                                <th>{{number_format($order->grand_total)}}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>{{__('page.note')}}</h5>
                    <p class="mx-2">{{$order->note}}</p>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{route('pre_order.index')}}" class="btn btn-secondary"><i class="fa fa-credit-card"></i>  {{__('page.purchase_order')}}</a>
                    <a href="{{route('received_order.index')}}?order_id={{$order->id}}" class="btn btn-info"><i class="icon ion-cash"></i>  {{__('page.received_list')}}</a>
                </div>
            </div>
        </div>                
    </div>

    <div class="modal fade" id="attachModal">
        <div class="modal-dialog" style="margin-top:17vh">
            <div class="modal-content">
                <div id="image_preview"></div>
                {{-- <img src="" id="attachment" width="100%" height="600" alt=""> --}}
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/select2/dist/js/select2.min.js')}}"></script>
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
