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
                        <li><a href="{{route('concurrent_payments')}}">{{__('page.concurrent_payments')}}</a></li>
                        <li class="active">{{__('page.supplier_purchases')}}</li>
                    </ol>
                </div>
            </div>
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="clearfix">
                    <h3 class="text-primary float-left my-0">{{$supplier->company}}</h3>
                    <a href="{{route('concurrent_payments.add_payments', $supplier->id)}}" class="btn btn-sm btn-success float-right">{{__('page.add_payments')}}</a>
                </div>
                <div class="mt-3">
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
                                    <th>{{__('page.action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_grand = $total_paid = 0;
                                    $i = 0
                                @endphp
                                @foreach ($data as $item)
                                    @php
                                        $paid = $item->payments()->where('status', 1)->sum('amount');
                                        $preturn = $item->preturns()->where('status', 1)->sum('amount');
                                        $grand_total = $item->grand_total - $preturn;
                                        $balance = $grand_total - $paid;

                                        $orders = $item->orders;
                                        $product_array = array();
                                        foreach ($orders as $order) {
                                            $product_name = isset($order->product->name) ? $order->product->name : "product";
                                            $product_quantity = $order->quantity;
                                            array_push($product_array, $product_name."(".$product_quantity.")");
                                        }
                                    @endphp
                                    @if($balance > 0)
                                        @php
                                            $i++;                                            
                                            $total_grand += $grand_total;
                                            $total_paid += $paid;
                                        @endphp
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                            <td class="reference_no">{{$item->reference_no}}</td>
                                            <td class="company">{{$item->company->name}}</td>
                                            <td class="store">{{$item->store->name}}</td>
                                            <td class="product">{{ implode(", ", $product_array) }}</td>
                                            <td class="grand_total"> {{number_format($grand_total)}} </td>
                                            <td class="paid"> {{ number_format($paid) }} </td>
                                            <td class="balance" data-value="{{$grand_total - $paid}}"> {{number_format($grand_total - $paid)}} </td>
                                            <td class="py-2" align="center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        {{__('page.action')}}
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @if ($item->status == 1)
                                                            <li><a href="#" data-id="{{$item->id}}" data-status={{$item->status}} class="dropdown-item btn-add-payment">{{__('page.add_payment')}}</a></li>
                                                        @endif
                                                        @if(in_array($role, ['admin', 'user']))
                                                            <li><a href="{{route('purchase.edit', $item->id)}}" class="dropdown-item">{{__('page.edit')}}</a></li>
                                                            <li><a href="{{route('purchase.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6">{{__('page.total')}}</th>
                                    <th>{{number_format($total_grand)}}</th>
                                    <th>{{number_format($total_paid)}}</th>
                                    <th>{{number_format($total_grand - $total_paid)}}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
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
            let status = $(this).data('status');
            if(status != 1){
                return alert("{{__('page.can_not_add_payment')}}");
            }
            let id = $(this).data('id');
            let balance = $(this).parents('tr').find('.balance').data('value');
            $("#payment_form .paymentable_id").val(id);
            $("#payment_form .amount").val(balance);
            $("#paymentModal").modal();
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
