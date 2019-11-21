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
                    <h3 class="pull-left page-title"><i class="fa fa-credit-card"></i> {{__('page.purchase_order')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.purchase_order')}}</li>
                    </ol>
                </div>
            </div>
            
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body">
                <div class="">
                    @include('elements.pagesize')                    
                    @include('pre_order.filter')
                    @if($role == 'user')
                        <a href="{{route('pre_order.create')}}" class="btn btn-success btn-sm float-right ml-3 mb-2" id="btn-add"><i class="fa fa-plus mg-r-2"></i> {{__('page.add_new')}}</a>
                    @endif
                    @include('elements.keyword')
                </div>
                <div class="table-responsive mt-2 pb-5">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-blue">
                                <th style="width:40px;">#</th>
                                <th>
                                    {{__('page.date')}}
                                    <span class="sort-date float-right">
                                        @if($sort_by_date == 'desc')
                                            <i class="fa fa-angle-up"></i>
                                        @elseif($sort_by_date == 'asc')
                                            <i class="fa fa-angle-down"></i>
                                        @endif
                                    </span>
                                </th>
                                <th>{{__('page.reference_no')}}</th>
                                <th>{{__('page.supplier')}}</th>
                                <th>{{__('page.grand_total')}}</th>
                                <th>{{__('page.received')}}</th>
                                <th>{{__('page.balance')}}</th>
                                <th>{{__('page.status')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $footer_grand_total = $footer_received = $footer_balance = 0;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    // $received = $item->payments()->sum('amount');
                                    $received = $item->purchases()->sum('grand_total');
                                    $grand_total = $item->grand_total;
                                    $balance = $grand_total - $received;
                                    $footer_grand_total += $grand_total;
                                    // $footer_paid += $received;
                                    $footer_balance += $balance;
                                @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                    <td class="reference_no">{{$item->reference_no}}</td>
                                    <td class="supplier" data-id="{{$item->supplier_id}}">{{$item->supplier->company}}</td>
                                    <td class="grand_total"> {{number_format($grand_total)}} </td>
                                    <td class="received"> {{ number_format($received) }} </td>
                                    <td class="balance" data-value="{{$balance}}"> {{number_format($balance)}} </td>
                                    <td class="status">
                                        @if ($received == 0)
                                            <span class="badge badge-danger">{{__('page.pending')}}</span>
                                        @elseif($received < $grand_total)
                                            <span class="badge badge-primary">{{__('page.partial')}}</span>
                                        @else
                                            <span class="badge badge-success">{{__('page.received')}}</span>
                                        @endif
                                    </td>
                                    <td class="py-2" align="center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('page.action')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{route('pre_order.detail', $item->id)}}" class="dropdown-item">{{__('page.details')}}</a></li>
                                                <li><a href="{{route('received_order.index')}}?order_id={{$item->id}}" class="dropdown-item">{{__('page.received_list')}}</a></li>
                                                <li><a href="{{route('pre_order.receive', $item->id)}}" class="dropdown-item">{{__('page.receive')}}</a></li>                                                    
                                                <li><a href="{{route('pre_order.edit', $item->id)}}" class="dropdown-item">{{__('page.edit')}}</a></li>
                                                <li><a href="{{route('pre_order.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">{{__('page.total')}}</th>
                                <th>{{number_format($footer_grand_total)}}</th>
                                <th>{{number_format($footer_received)}}</th>
                                <th>{{number_format($footer_balance)}}</th>
                                <th colspan="2"></th>
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
                                'supplier_id' => $supplier_id,
                                'reference_no' => $reference_no,
                                'period' => $period,
                                // 'expiry_period' => $expiry_period,
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
<script src="{{asset('master/plugins/styling/uniform.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $("#payment_form input.date").datetimepicker({
            dateFormat: 'yy-mm-dd',
        });


        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });

        $("#period").dateRangePicker({
            autoClose: false,
        });

        $("#pagesize").change(function(){
            $("#pagesize_form").submit();
        });

        $("#keyword_filter").change(function(){
            $("#keyword_filter_form").submit();
        });

        $('#search_supplier').wrap('<div class="position-relative" style="width: 200px;"></div>')
                    .select2({
                        width: 'resolve',
                    });

        $("#btn-reset").click(function(){
            $("#search_company").val('');
            $("#search_supplier").val('').change();
            $("#search_reference_no").val('');
            $("#period").val('');
        });
        var toggle = 'desc';
        if($("#search_sort_date").val() == 'desc'){
            toggle = true;
        } else {
            toggle = false;
        }
        $(".sort-date").click(function(){
            let status = $("#search_sort_date").val();
            if (status == 'asc') {
                $("#search_sort_date").val('desc');
            } else {
                $("#search_sort_date").val('asc');
            }
            $("#searchForm").submit();
        })
    });
</script>
@endsection
