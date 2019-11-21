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
                    <h3 class="pull-left page-title"><i class="fa fa-download"></i> {{__('page.received_orders')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('received_order.index')}}">{{__('page.received_orders')}}</a></li>
                        <li class="active">{{__('page.list')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="">
                    @include('elements.pagesize')
                    @include('received_order.filter')
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
                                <th>{{__('page.purchase_status')}}</th>
                                <th>{{__('page.grand_total')}}</th>
                                <th>{{__('page.paid')}}</th>
                                <th>{{__('page.balance')}}</th>
                                <th>{{__('page.payment_status')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $footer_grand_total = $footer_paid = 0;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    $paid = $item->payments()->sum('amount');
                                    $grand_total = $item->grand_total;
                                    $footer_grand_total += $grand_total;
                                    $footer_paid += $paid;
                                @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                    <td class="reference_no">{{$item->reference_no}}</td>
                                    <td class="supplier" data-id="{{$item->supplier_id}}">{{$item->supplier->company}}</td>
                                    <td class="status">
                                        @if ($item->status == 1)
                                            <span class="badge badge-success">{{__('page.received')}}</span>
                                        @elseif($item->status == 0)
                                            <span class="badge badge-primary">{{__('page.pending')}}</span>
                                        @endif
                                    </td>
                                    <td class="grand_total"> {{number_format($grand_total)}} </td>
                                    <td class="paid"> {{ number_format($paid) }} </td>
                                    <td class="balance" data-value="{{$grand_total - $paid}}"> {{number_format($grand_total - $paid)}} </td>
                                    <td>
                                        @if ($paid == 0)
                                            <span class="badge badge-danger">{{__('page.pending')}}</span>
                                        @elseif($paid < $grand_total)
                                            <span class="badge badge-primary">{{__('page.partial')}}</span>
                                        @else
                                            <span class="badge badge-success">{{__('page.paid')}}</span>
                                        @endif
                                    </td>
                                    <td class="py-2" align="center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('page.action')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{route('purchase.detail', $item->id)}}" class="dropdown-item">{{__('page.details')}}</a></li>
                                                <li><a href="{{route('purchase.edit', $item->id)}}" class="dropdown-item">{{__('page.edit')}}</a></li>
                                                <li><a href="{{route('purchase.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">{{__('page.total')}}</th>
                                <th>{{number_format($footer_grand_total)}}</th>
                                <th>{{number_format($footer_paid)}}</th>
                                <th>{{number_format($footer_grand_total - $footer_paid)}}</th>
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

    <!-- The Modal -->
    <div class="modal fade" id="paymentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.add_payment')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('payment.create')}}" id="payment_form" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" class="type" name="type" value="purchase" />
                    <input type="hidden" class="paymentable_id" name="paymentable_id" />
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.date')}}</label>
                            <input class="form-control date" type="text" name="date" autocomplete="off" value="{{date('Y-m-d H:i')}}" placeholder="{{__('page.date')}}">
                        </div>                        
                        <div class="form-group">
                            <label class="control-label">{{__('page.reference_no')}}</label>
                            <input class="form-control reference_no" type="text" name="reference_no" required placeholder="{{__('page.reference_no')}}">
                        </div>                                                
                        <div class="form-group">
                            <label class="control-label">{{__('page.amount')}}</label>
                            <input class="form-control amount" type="text" name="amount" required placeholder="{{__('page.amount')}}">
                        </div>                                               
                        <div class="form-group">
                            <label class="control-label">{{__('page.attachment')}}</label>
                            <input type="file" name="attachment" id="file2" class="file-input-styled">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.note')}}</label>
                            <textarea class="form-control note" type="text" name="note" placeholder="{{__('page.note')}}"></textarea>
                        </div> 
                    </div>    
                    <div class="modal-footer">
                        <button type="submit" id="btn_create" class="btn btn-primary btn-submit"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mg-r-10"></i>&nbsp;{{__('page.close')}}</button>
                    </div>
                </form>
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
        
        $(".btn-add-payment").click(function(){
            // $("#payment_form input.form-control").val('');
            let id = $(this).data('id');
            let balance = $(this).parents('tr').find('.balance').data('value');
            $("#payment_form .paymentable_id").val(id);
            $("#payment_form .amount").val(balance);
            $("#paymentModal").modal();
        });

        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });
        
        $('#search_supplier').wrap('<div class="position-relative" style="width: 200px;"></div>')
            .select2({
                width: 'resolve',
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

        $("#btn-reset").click(function(){
            $("#search_company").val('');
            $("#search_store").val('');
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
