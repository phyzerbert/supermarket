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
                    <h3 class="pull-left page-title"><i class="fa fa-truck"></i> {{__('page.purchases_list')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('purchase.index')}}">{{__('page.purchase')}}</a></li>
                        <li class="active">{{__('page.list')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="clearfix">
                    @include('elements.pagesize')                    
                    @include('purchase.filter')
                    @if($role == 'user')
                        <a href="{{route('purchase.create')}}" class="btn btn-success btn-sm float-right ml-3 mb-2" id="btn-add"><i class="fa fa-plus mg-r-2"></i> {{__('page.add_new')}}</a>
                    @endif
                    <button class="btn btn-sm btn-info float-right ml-2 mb-2" id="btn-export"><i class="fa fa-file-excel-o mr-2"></i>{{__('page.export')}}</button>
                    <input type="text" class="form-control form-control-sm col-md-2 float-right" id="input_keyword" value="{{$keyword}}" placeholder="{{__('page.keyword')}}" />
                </div>
                <div class="table-responsive mt-2" style="padding-bottom: 5rem;">
                    <table class="table table-bordered table-hover">
                        <thead class="">
                            <tr>
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
                                <th>{{__('page.currency')}}</th>
                                <th>{{__('page.grand_total')}}</th>
                                <th>{{__('page.paid')}}</th>
                                <th>{{__('page.balance')}}</th>
                                <th>{{__('page.payment_status')}}</th>
                                <th width="120">{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $footer_grand_total = $footer_paid = array(0,0,0,0);
                            @endphp
                            @forelse ($data as $item)
                                @php
                                    $paid = $item->payments()->where('status', 1)->sum('amount');
                                    $preturn = $item->preturns()->where('status', 1)->sum('amount');
                                    $grand_total = $item->grand_total - $preturn;
                                    // if(($expiry_period != '') && ($grand_total == $paid)) continue;
                                    $footer_grand_total[$item->currency_id] += $grand_total;
                                    $footer_paid[$item->currency_id] += $paid;
                                @endphp
                                <tr class="@if($item->status == 0) text-danger @endif">
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                    <td class="reference_no">{{$item->reference_no}}</td>
                                    <td class="supplier" data-id="{{$item->supplier_id}}">@isset($item->supplier->company){{$item->supplier->company}}@endisset</td>
                                    <td class="currency" data-value="{{$item->currency_id}}">{{$item->currency->name ?? ''}}</td>
                                    <td class="grand_total"> {{number_format($grand_total, 2)}} </td>
                                    <td class="paid"> {{ number_format($paid, 2) }} </td>
                                    <td class="balance" data-value="{{$grand_total - $paid, 2}}">
                                        @if($grand_total - $paid < 0)
                                            <span class="text-danger">{{number_format($grand_total - $paid, 2)}}</span>
                                        @else
                                            <span>{{number_format($grand_total - $paid, 2)}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($paid == 0)
                                            <span class="badge badge-danger">{{__('page.pending')}}</span>
                                        @elseif($paid < $grand_total)
                                            <span class="badge badge-primary">{{__('page.partial')}}</span>
                                        @else
                                            <span class="badge badge-success">{{__('page.paid')}}</span>
                                        @endif
                                        @php
                                            $pending_payments = $item->payments()->where('status', 0)->count();
                                            $pending_preturns = $item->preturns()->where('status', 0)->count();
                                        @endphp
                                        @if($pending_payments)
                                            <img src="{{asset('images/pending.png')}}" width="25" height="25" alt="" title="{{__('page.payment_pending_approval')}}" />
                                        @endif
                                        @if($pending_preturns)
                                            <img src="{{asset('images/pending1.png')}}" width="25" height="25" alt="" title="{{__('page.return_pending_approval')}}" />
                                        @endif
                                    </td>
                                    <td class="py-2" align="center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('page.action')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{route('purchase.detail', $item->id)}}" class="dropdown-item">{{__('page.details')}}</a></li>
                                                <li><a href="{{route('payment.index', ['purchase', $item->id])}}" class="dropdown-item">{{__('page.payment_list')}}</a></li>
                                                <li><a href="{{route('preturn.index', $item->id)}}" class="dropdown-item">{{__('page.return_list')}}</a></li>
                                                @if ($item->status == 1)
                                                    <li><a href="#" data-id="{{$item->id}}" data-status={{$item->status}} class="dropdown-item btn-add-payment">{{__('page.add_payment')}}</a></li>
                                                    <li><a href="#" data-id="{{$item->id}}" data-status={{$item->status}} class="dropdown-item btn-add-preturn">{{__('page.add_return')}}</a></li>
                                                @endif
                                                @if(in_array($role, ['admin', 'user']))
                                                    <li><a href="{{route('purchase.report', $item->id)}}" class="dropdown-item">{{__('page.report')}}</a></li>
                                                    <li><a href="{{route('purchase.email', $item->id)}}" class="dropdown-item">{{__('page.email')}}</a></li>
                                                    <li><a href="{{route('purchase.edit', $item->id)}}" class="dropdown-item">{{__('page.edit')}}</a></li>
                                                    <li><a href="{{route('purchase.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="20" align="center">{{__('page.no_data')}}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">{{__('page.total')}}</th>
                                <th class="py-1">
                                    <p class="my-0">Bolivar : {{number_format($footer_grand_total[1], 2)}}</p>
                                    <p class="my-0">Dollar : {{number_format($footer_grand_total[2], 2)}}</p>
                                    <p class="my-0">Euro : {{number_format($footer_grand_total[3], 2)}}</p>
                                </th>
                                <th class="py-1">
                                    <p class="my-0">Bolivar : {{number_format($footer_paid[1], 2)}}</p>
                                    <p class="my-0">Dollar : {{number_format($footer_paid[2], 2)}}</p>
                                    <p class="my-0">Euro : {{number_format($footer_paid[3], 2)}}</p>
                                </th>
                                <th class="py-1">
                                    <p class="my-0">Bolivar : {{number_format($footer_grand_total[1] - $footer_paid[1], 2)}}</p>
                                    <p class="my-0">Dollar : {{number_format($footer_grand_total[2] - $footer_paid[2], 2)}}</p>
                                    <p class="my-0">Euro : {{number_format($footer_grand_total[3] - $footer_paid[3], 2)}}</p>
                                </th>
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
                                'expiry_period' => $expiry_period,
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
                            <input class="form-control date datepicker" type="text" name="date" autocomplete="off" value="{{date('Y-m-d H:i')}}" placeholder="{{__('page.date')}}">
                        </div>                        
                        <div class="form-group">
                            <label class="control-label">{{__('page.reference_no')}}</label>
                            <input class="form-control reference_no" type="text" name="reference_no" required placeholder="{{__('page.reference_no')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.account')}}</label>
                            <select name="account" class="form-control account" required>
                                <option value="" hidden>{{__('page.select_account')}}</option>
                                @foreach ($accounts as $item)
                                    <option value="{{$item->id}}" data-currency="{{$item->currency_id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
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

    <!-- The Modal -->
    <div class="modal fade" id="preturnModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.add_return')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('preturn.create')}}" id="preturn_form" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" class="purchase_id" name="purchase_id" />
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.date')}}</label>
                            <input class="form-control date datepicker" type="text" name="date" autocomplete="off" value="{{date('Y-m-d H:i')}}" placeholder="{{__('page.date')}}">
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
        $(".datepicker").datetimepicker({
            dateFormat: 'yy-mm-dd',
        });
        
        $(".btn-add-payment").click(function(){
            // $("#payment_form input.form-control").val('');
            let status = $(this).data('status');
            if(status != 1){
                return alert("{{__('page.can_not_add_payment')}}");
            }
            let id = $(this).data('id');
            let balance = $(this).parents('tr').find('.balance').data('value');
            let currency = $(this).parents('tr').find('.currency').data('value');
            $("#payment_form .account option").hide();
            $("#payment_form .account option[data-currency=" + currency +"]").show();
            $("#payment_form .paymentable_id").val(id);
            $("#payment_form .amount").val(balance);
            $("#paymentModal").modal();
        });

                
        $(".btn-add-preturn").click(function(){
            let status = $(this).data('status');
            if(status != 1){
                return alert("{{__('page.can_not_add_return')}}");
            }
            let id = $(this).data('id');

            $("#preturn_form .purchase_id").val(id);
            $("#preturnModal").modal();
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

        $("#input_keyword").keyup(function(e){
            if(e.keyCode != 13){
                $("#search_keyword").val($(this).val());
            }else{
                $("#searchForm").submit();
            }                
        });

        $('#search_supplier').wrap('<div class="position-relative" style="width: 200px;"></div>')
            .select2({
                width: 'resolve',
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
        });

        $("#btn-export").click(function(){
            $("#searchForm").attr('action', "{{route('purchase.export')}}");
            $("#searchForm").submit();
        });

        $("#btn-submit").click(function(){
            $("#searchForm").attr('action', "");
            $("#searchForm").submit();
        });
    });
</script>
@endsection
