@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/daterangepicker/daterangepicker.min.css')}}" rel="stylesheet">
    <style>
        #purchase_image {
            max-width: 100%;
            height: 500px;
        }
    </style>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-credit-card"></i> {{__('page.pending_payments')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.pending_payments')}}</li>
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
                                <th width="40">#</th>
                                <th>{{__('page.date')}}</th>
                                <th>{{__('page.reference_no')}}</th>
                                <th>{{__('page.type')}}</th>
                                <th>{{__('page.supplier')}} / {{__('page.customer')}}</th>
                                <th>{{__('page.amount')}}</th>
                                <th>{{__('page.note')}}</th>
                                @if(in_array($role, ['admin', 'user']))
                                    <th>{{__('page.action')}}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody> 
                            @php
                                $total_amount = 0;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    $image_path = asset('images/no-image.png');
                                    if($item->attachment){                                        
                                        if(file_exists($item->attachment)){
                                            $image_path = asset($item->attachment);
                                        }
                                    }
                                    $total_amount += $item->amount;
                                @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="date">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                    <td class="reference_no" >{{$item->reference_no}}</td>
                                    <td class="type" data-id="{{$item->paymentable_id}}">
                                        @if($item->paymentable)
                                            @if($item->paymentable_type == 'App\Models\Purchase')
                                                {{__('page.purchase')}} ({{$item->paymentable->reference_no}})
                                            @elseif($item->paymentable_type == 'App\Models\Sale')
                                                {{__('page.sale')}} ({{$item->paymentable->reference_no}})
                                            @endif
                                        @endif
                                    </td>
                                    <td class="supplier_customer">
                                        @if($item->paymentable)
                                            @if($item->paymentable_type == 'App\Models\Purchase')
                                                @isset($item->paymentable->supplier){{$item->paymentable->supplier->company}}@endisset
                                            @elseif($item->paymentable_type == 'App\Models\Sale')
                                                @isset($item->paymentable->customer){{$item->paymentable->customer->company}}@endisset
                                            @endif
                                        @endif
                                    </td>
                                    <td class="amount" data-value="{{$item->amount}}"> {{number_format($item->amount)}} </td>
                                    <td class="note"> {{$item->note}} </td>
                                    @if(in_array($role, ['admin', 'user']))
                                        <td class="text-center py-2">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    {{__('page.action')}}
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a href="javascript:;" data-id="{{$item->id}}" class="dropdown-item btn-edit">{{__('page.edit')}}</a></li>
                                                    <li><a href="{{route('payment.approve', $item->id)}}" data-id="{{$item->id}}" data-path="{{$image_path}}" class="dropdown-item btn-approve">{{__('page.approve')}}</a></li>
                                                    <li><a href="{{route('payment.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">{{__('page.total')}}</th>
                                <th>{{number_format($total_amount)}}</th>
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
                                'reference_no' => $reference_no,
                                'period' => $period,
                            ])->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>                
    </div>
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.edit_payment')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('payment.edit')}}" id="edit_form" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" class="id">
                    <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label">{{__('page.date')}}</label>
                                <input class="form-control date" type="text" name="date" autocomplete="off" value="{{date('Y-m-d H:i')}}" placeholder="{{__('page.date')}}">
                            </div>                        
                            <div class="form-group">
                                <label class="control-label">{{__('page.reference_no')}}</label>
                                <input class="form-control reference_no" type="text" name="reference_no" placeholder="{{__('page.reference_number')}}">
                            </div>                                                
                            <div class="form-group">
                                <label class="control-label">{{__('page.amount')}}</label>
                                <input class="form-control amount" type="text" name="amount" placeholder="{{__('page.amount')}}">
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
                        <button type="submit" id="btn_update" class="btn btn-primary btn-submit"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mg-r-10"></i>&nbsp;{{__('page.close')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="approveModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="purchase_image" class="border rounded"></div>
                </div>                
                <div class="modal-footer">
                    <a href="#" class="btn btn-primary" id="btn_approve"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.approve')}}</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mg-r-10"></i>&nbsp;{{__('page.close')}}</button>
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

        $("#period").dateRangePicker({
            autoClose: false,
        });

        $("#pagesize").change(function(){
            $("#pagesize_form").submit();
        });

        $("#btn-reset").click(function(){
            $("#search_company").val('');
            $("#search_reference_no").val('');
            $("#period").val('');
        });

        $("#edit_form input.date").datetimepicker({
            dateFormat: 'yy-mm-dd',
        });

        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });

        $(".btn-edit").click(function(){
            let id = $(this).data("id");
            let date = $(this).parents('tr').find(".date").text().trim();
            let reference_no = $(this).parents('tr').find(".reference_no").text().trim();
            let amount = $(this).parents('tr').find(".amount").data('value');
            let note = $(this).parents('tr').find(".note").text().trim();
            console.log(date)
            $("#editModal input.form-control").val('');
            $("#editModal .id").val(id);
            $("#editModal .date").val(date);
            $("#editModal .reference_no").val(reference_no);
            $("#editModal .amount").val(amount);
            $("#editModal .note").val(note);
            $("#editModal").modal();
        });

        $(".btn-approve").click(function(e){
            e.preventDefault();
            let image_path = $(this).data('path');
            let url = $(this).attr('href');
            $("#btn_approve").attr("href", url);
            $("#purchase_image").html('')
            $("#purchase_image").verySimpleImageViewer({
                imageSource: image_path,
                frame: ['100%', '100%'],
                maxZoom: '900%',
                zoomFactor: '10%',
                mouse: true,
                keyboard: true,
                toolbar: true,
            });
            $("#approveModal").modal();    
        }); 

    });
</script>
@endsection
