@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/daterangepicker/daterangepicker.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-sign-in"></i> {{__('page.purchases_report')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('report.overview_chart')}}">{{__('page.reports')}}</a></li>
                        <li class="active">{{__('page.purchases_report')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="">
                    @include('elements.pagesize')
                    @include('purchase.filter')
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>{{__('page.date')}}</th>
                                <th>{{__('page.reference_no')}}</th>
                                <th>{{__('page.company')}}</th>
                                <th>{{__('page.store')}}</th>
                                <th>{{__('page.supplier')}}</th>
                                <th>{{__('page.product_qty')}}</th>
                                <th>{{__('page.grand_total')}}</th>
                                <th>{{__('page.paid')}}</th>
                                <th>{{__('page.balance')}}</th>
                                <th>{{__('page.purchase_status')}}</th>
                                {{-- <th>Payment Status</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_grand = $total_paid = 0;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    $paid = $item->payments()->where('status', 1)->sum('amount');
                                    $preturn = $item->preturns()->where('status', 1)->sum('amount');
                                    $grand_total = $item->grand_total - $preturn;
                                    $orders = $item->orders;
                                    $product_array = array();
                                    foreach ($orders as $order) {
                                        $product_name = isset($order->product->name) ? $order->product->name : "product";
                                        $product_quantity = $order->quantity;
                                        array_push($product_array, $product_name."(".$product_quantity.")");
                                    }

                                    $total_grand += $grand_total;
                                    $total_paid += $paid;
                                @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                    <td class="reference_no">{{$item->reference_no}}</td>
                                    <td class="company">{{$item->company->name}}</td>
                                    <td class="store">{{$item->store->name}}</td>
                                    <td class="supplier" data-id="{{$item->supplier_id}}">{{$item->supplier->company}}</td>
                                    <td class="product">{{implode(", ", $product_array)}}</td>
                                    <td class="grand_total"> {{number_format($grand_total)}} </td>
                                    <td class="paid"> {{ number_format($paid) }} </td>
                                    <td> {{number_format($grand_total - $paid)}} </td>
                                    <td class="status">
                                        @if ($item->status == 1)
                                            <span class="badge badge-success"><i class="fa fa-check"></i> {{__('page.received')}}</span>
                                        @elseif($item->status == 0)
                                            <span class="badge badge-danger"><i class="fa fa- exclamation-triangle"></i>{{__('page.pending')}}</span>
                                        @endif
                                    </td>
                                    {{-- <td></td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7">{{__('page.total')}}</th>
                                <th>{{number_format($total_grand)}}</th>
                                <th>{{number_format($total_paid)}}</th>
                                <th>{{number_format($total_grand - $total_paid)}}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>                
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends([
                                'company_id' => $company_id, 
                                'store_id' => $store_id,
                                'supplier_id' => $supplier_id,
                                'reference_no' => $reference_no,
                                'period' => $period,
                            ])->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>                
    </div>

@endsection

@section('script')
<script src="{{asset('master/plugins/select2/dist/js/select2.min.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script>
<script src="{{asset('master/plugins/daterangepicker/jquery.daterangepicker.min.js')}}"></script>
<script>
    $(document).ready(function () {

        $("#period").dateRangePicker({
            autoClose: false,
        });

        $('#search_supplier').wrap('<div class="position-relative" style="width: 200px;"></div>')
            .select2({
                width: 'resolve',
            });

        $("#pagesize").change(function(){
            $("#pagesize_form").submit();
        });

        $("#btn-reset").click(function(){
            $("#search_company").val('');
            $("#search_store").val('');
            $("#search_supplier").val('');
            $("#search_reference_no").val('');
            $("#period").val('');
        });

    });
</script>
@endsection
