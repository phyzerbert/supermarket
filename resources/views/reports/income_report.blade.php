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
                    <h3 class="pull-left page-title"><i class="fa fa-sign-in"></i> {{__('page.income_report')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('report.overview_chart')}}">{{__('page.reports')}}</a></li>
                        <li class="active">{{__('page.income_report')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            
            <div class="card card-body card-fill">
                <div class="">
                    @include('elements.pagesize')
                    <form action="" method="POST" class="form-inline top-search-form float-left" id="searchForm">
                        @csrf                        
                        @if($role == 'admin')
                            <select class="form-control form-control-sm mr-sm-2 mb-2" name="company_id" id="search_company">
                                <option value="" hidden>{{__('page.select_company')}}</option>
                                @foreach ($companies as $item)
                                    <option value="{{$item->id}}" @if ($company_id == $item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        @endif
                        <select class="form-control form-control-sm mr-sm-2 mb-2 select2-show-search" name="customer_id" id="search_customer" data-placeholder="{{__('page.select_customer')}}">
                            <option label="">{{__('page.select_customer')}}</option>
                            @foreach ($customers as $item)
                                <option value="{{$item->id}}" @if ($customer_id == $item->id) selected @endif>{{$item->name}}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control form-control-sm mx-sm-2 mb-2" name="reference_no" id="search_reference_no" value="{{$reference_no}}" placeholder="{{__('page.reference_no')}}">
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="period" id="period" autocomplete="off" value="{{$period}}" placeholder="{{__('page.date')}}">
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
                        <button type="button" class="btn btn-sm btn-danger mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
                    </form>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>{{__('page.date')}}</th>
                                <th>{{__('page.reference_no')}}</th>
                                <th>{{__('page.sale_reference')}}</th>
                                <th>{{__('page.customer')}}</th>
                                <th>{{__('page.amount')}}</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @php
                                $total_amount = 0;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    $total_amount += $item->amount;
                                @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                    <td class="reference_no" >{{$item->reference_no}}</td>
                                    {{-- <td class="sale" data-id="{{$item->paymentable_id}}">
                                        @if ($item->paymentable_type == 'App\Models\Sale')
                                            {{$item->paymentable->reference_no}}
                                        @endif                                        
                                    </td> --}}
                                    <td class="purchase" data-id="{{$item->paymentable_id}}">
                                        {{$item->paymentable->reference_no}}
                                    </td>
                                    <td class="supplier">
                                        @isset($item->paymentable->customer->name){{$item->paymentable->customer->name}}@endisset
                                    </td>
                                    <td class="amount"> {{number_format($item->amount)}} </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">{{__('page.total')}}</th>
                                <th>{{number_format($total_amount)}}</th>
                            </tr>
                        </tfoot>
                    </table>                
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends([])->links() !!}
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
        $("#payment_form input.date").datetimepicker({
            dateFormat: 'yy-mm-dd',
        });         

        $("#period").dateRangePicker({
            autoClose: false,
        });
        
        $('#search_customer').wrap('<div class="position-relative" style="width: 200px;"></div>')
            .select2({
                width: 'resolve',
            });

        $("#pagesize").change(function(){
            $("#pagesize_form").submit();
        });

        $("#btn-reset").click(function(){
            $("#search_company").val('');
            $("#search_reference_no").val('');
            $("#period").val('');
        });

    });
</script>
@endsection
