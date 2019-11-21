@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/daterangepicker/daterangepicker.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-truck"></i> {{__('page.supplier_purchases')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('report.overview_chart')}}">{{__('page.reports')}}</a></li>
                        <li><a href="{{route('report.suppliers_report')}}">{{__('page.suppliers_report')}}</a></li>
                        <li class="active">{{__('page.supplier_purchases')}}</li>
                    </ol>
                </div>
            </div>
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill"> 
                <div class="">
                    <ul class="nav nav-tabs tabs" role="tablist" style="width: 100%;border:none">
                        <li class="nav-item tab" style="width: 120px;">
                            <a class="nav-link active" id="home-tab-2" data-toggle="tab" href="{{route('report.suppliers_report.purchases', $supplier->id)}}" role="tab" aria-controls="home-2" aria-selected="false">
                                <span class="d-block d-sm-none"><i class="fa fa-sign-in"></i></span>
                                <span class="d-none d-sm-block">{{__('page.purchases')}}</span>
                            </a>
                        </li>
                        <li class="nav-item tab" style="width: 100px;">
                            <a class="nav-link" id="profile-tab-2" data-toggle="tab" href="{{route('report.suppliers_report.payments', $supplier->id)}}" role="tab" aria-controls="profile-2" aria-selected="true">
                                <span class="d-block d-sm-none"><i class="fa fa-credit-card"></i></span>
                                <span class="d-none d-sm-block">{{__('page.payments')}}</span>
                            </a>
                        </li>
                        <div class="indicator" style="left: 0px;width:120px;"></div>
                    </ul>
                </div>
                <div class="br-section-wrapper">
                    <div class="mt-2">
                        @include('elements.pagesize')
                        <form action="" method="POST" class="form-inline top-search-form float-left" id="searchForm">
                            @csrf
                            @if ($role == 'admin')    
                                <select class="form-control form-control-sm mr-sm-2 mb-2" name="company_id" id="search_company">
                                    <option value="" hidden>{{__('page.select_company')}}</option>
                                    @foreach ($companies as $item)
                                        <option value="{{$item->id}}" @if ($company_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach        
                                </select>
                            @endif
                            <select class="form-control form-control-sm mr-sm-2 mb-2" name="store_id" id="search_store">
                                <option value="" hidden>{{__('page.select_store')}}</option>
                                @foreach ($stores as $item)
                                    <option value="{{$item->id}}" @if ($store_id == $item->id) selected @endif>{{$item->name}}</option>
                                @endforeach        
                            </select>
                            <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="reference_no" id="search_reference_no" value="{{$reference_no}}" placeholder="{{__('page.reference_no')}}">
                            <input type="text" class="form-control form-control-sm mx-sm-2 mb-2" name="period" id="period" autocomplete="off" value="{{$period}}" placeholder="{{__('page.purchase_date')}}">
                            <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
                            <button type="button" class="btn btn-sm btn-info mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
                        </form>
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
                                        $total_grand += $grand_total;
                                        $total_paid += $paid;

                                        $orders = $item->orders;
                                        $product_array = array();
                                        foreach ($orders as $order) {
                                            $product_name = isset($order->product->name) ? $order->product->name : "product";
                                            $product_quantity = $order->quantity;
                                            array_push($product_array, $product_name."(".$product_quantity.")");
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                        <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                        <td class="reference_no">{{$item->reference_no}}</td>
                                        <td class="company">{{$item->company->name}}</td>
                                        <td class="store">{{$item->store->name}}</td>
                                        <td class="product">{{ implode(", ", $product_array) }}</td>
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
                                    <td colspan="6">{{__('page.total')}}</td>
                                    <td>{{number_format($total_grand)}}</td>
                                    <td>{{number_format($total_paid)}}</td>
                                    <td>{{number_format($total_grand - $total_paid)}}</td>
                                    <td></td>
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
                                    'reference_no' => $reference_no,
                                    'period' => $period,
                                ])->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>                
    </div>

@endsection

@section('script')
<script src="{{asset('master/plugins/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script>
<script src="{{asset('master/plugins/daterangepicker/jquery.daterangepicker.min.js')}}"></script>
<script>
    $(document).ready(function () {

        $("#period").dateRangePicker({
            autoClose: false,
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

        $("ul.nav a.nav-link").click(function(){
            location.href = $(this).attr('href');
        });
    });
</script>
@endsection
