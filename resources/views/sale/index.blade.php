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
                    <h3 class="pull-left page-title"><i class="fa fa-credit-card"></i> {{__('page.sales_list')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('sale.index')}}">{{__('page.sales')}}</a></li>
                        <li class="active">{{__('page.list')}}</li>
                    </ol>
                </div>
            </div>         
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class=" clearfix">
                    @include('elements.pagesize') 
                    @include('sale.filter')
                    @if($role == 'user')
                        <a href="{{route('sale.create')}}" class="btn btn-success btn-sm float-right mb-2" id="btn-add"><i class="fa fa-plus mg-r-2"></i> {{__('page.add_new')}}</a>
                    @endif
                    <button class="btn btn-sm btn-info float-right mr-2" id="btn-export"><i class="fa fa-file-excel-o mr-2"></i>{{__('page.export')}}</button>
                    {{-- <input type="text" class="form-control form-control-sm col-md-2 float-right" id="input_keyword" value="{{$keyword}}" placeholder="{{__('page.keyword')}}" /> --}}
                </div>
                <div class="table-responsive mt-2 pb-5">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>{{__('page.date')}}</th>
                                <th>{{__('page.reference_no')}}</th>
                                <th>{{__('page.user')}}</th>
                                <th>{{__('page.customer')}}</th>
                                {{-- <th>{{__('page.sale_status')}}</th> --}}
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
                            @forelse ($data as $item)
                                @php
                                    $grand_total = $item->orders()->sum('subtotal');
                                    $paid = $item->payments()->sum('amount');
                                    $footer_grand_total += $grand_total;
                                    $footer_paid += $paid;
                                @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                    <td class="reference_no">{{$item->reference_no}}</td>
                                    <td class="user">{{$item->biller->name}}</td>
                                    <td class="customer" data-id="{{$item->customer_id}}">{{$item->customer->name}}</td>
                                    {{-- <td class="status">
                                        @if ($item->status == 1)
                                            <span class="badge badge-success">{{__('page.received')}}</span>
                                        @elseif($item->status == 0)
                                            <span class="badge badge-primary">{{__('page.pending')}}</span>
                                        @endif
                                    </td> --}}
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
                                                <li><a href="{{route('sale.detail', $item->id)}}" class="dropdown-item">{{__('page.details')}}</a></li>
                                                <li><a href="{{route('payment.index', ['sale', $item->id])}}" class="dropdown-item">{{__('page.payment_list')}}</a></li>
                                                <li><a href="#" data-id="{{$item->id}}" class="dropdown-item btn-add-payment">{{__('page.add_payment')}}</a></li>
                                                <li><a href="{{route('sale.report', $item->id)}}" class="dropdown-item">{{__('page.report')}}</a></li>
                                                <li><a href="{{route('sale.email', $item->id)}}" class="dropdown-item">{{__('page.email')}}</a></li>
                                                <li><a href="{{route('sale.edit', $item->id)}}" class="dropdown-item">{{__('page.edit')}}</a></li>
                                                <li><a href="{{route('sale.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" align="center">{{__('page.no_data')}}</td>
                                </tr>
                            @endforelse
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
                                'customer_id' => $customer_id,
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
                    <input type="hidden" class="type" name="type" value="sale" />
                    <input type="hidden" class="paymentable_id" name="paymentable_id" />
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.date')}}</label>
                            <input class="form-control date" type="text" name="date" autocomplete="off" value="{{date('Y-m-d H:i')}}" placeholder="Date">
                        </div>                        
                        <div class="form-group">
                            <label class="control-label">{{__('page.reference_no')}}</label>
                            <input class="form-control reference_no" type="text" name="reference_no" required placeholder="{{__('page.reference_number')}}">
                        </div>                                                
                        <div class="form-group">
                            <label class="control-label">{{__('page.amount')}}</label>
                            <input class="form-control amount" type="number" name="amount" placeholder="{{__('page.amount')}}">
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

        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });
        
        $(".btn-add-payment").click(function(){
            // $("#payment_form input.form-control").val('');
            // let status = $(this).data('status');
            // if(status != 1){
            //     return alert("{{__('page.can_not_add_payment')}}");
            // }
            let id = $(this).data('id');
            let balance = $(this).parents('tr').find('.balance').data('value');
            console.log(balance)
            $("#payment_form .paymentable_id").val(id);
            $("#payment_form .amount").val(balance);
            $("#paymentModal").modal();
        });

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

        $("#btn-export").click(function(){
            $("#searchForm").attr('action', "{{route('sale.export')}}");
            $("#searchForm").submit();
        });

        $("#btn-search").click(function(){
            $("#searchForm").attr('action', "");
            $("#searchForm").submit();
        });
    });
</script>
@endsection
