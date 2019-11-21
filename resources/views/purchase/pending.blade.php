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
        .image-control {
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-filter"></i> {{__('page.pending_purchases')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.pending_purchases')}}</li>
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
                    @include('elements.keyword')
                </div>
                <div class="table-responsive mt-2">
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
                                <th>{{__('page.user')}}</th>
                                <th>{{__('page.supplier')}}</th>
                                <th>{{__('page.grand_total')}}</th>
                                <th width="150">{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $footer_grand_total = $footer_paid = 0;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    $image_array = $item->images;
                                    $paid = $item->payments()->sum('amount');
                                    $grand_total = $item->grand_total;
                                    if(($expiry_period != '') && ($grand_total == $paid)) continue;
                                    $footer_grand_total += $grand_total;
                                    // $footer_paid += $paid;
                                @endphp
                                <tr>
                                    <td class="d-none image-array">
                                        @foreach ($image_array as $image)
                                            @if (file_exists($image->path))
                                                <input type="hidden" name="" class="image-path" value="{{asset($image->path)}}" />
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="timestamp">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                    <td class="reference_no">{{$item->reference_no}}</td>
                                    <td class="user"> @if($item->user) {{$item->user->name}} @endif </td>
                                    <td class="supplier" data-id="{{$item->supplier_id}}"> @isset($item->supplier->company) {{$item->supplier->company}} @endisset</td>
                                    <td class="grand_total"> {{number_format($grand_total)}} </td>
                                    <td class="py-2" align="center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('page.action')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{route('purchase.detail', $item->id)}}" class="dropdown-item">{{__('page.details')}}</a></li>
                                                @if(in_array($role, ['admin', 'user']))
                                                    <li><a href="{{route('purchase.edit', $item->id)}}" class="dropdown-item">{{__('page.edit')}}</a></li>
                                                    <li><a href="{{route('purchase.approve', $item->id)}}" data-id="{{$item->id}}" class="dropdown-item btn-approve">{{__('page.approve')}}</a></li>
                                                    <li><a href="{{route('purchase.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                                @endif
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
    <div class="modal fade" id="approveModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="purchase_image" class="border rounded"></div>
                    <div class="text-center mt-2">
                        <span class="badge badge-success p-2 mr-3 image-control" id="prev_img"> << </span>
                        <span class="badge badge-success p-2 image-control" id="next_img"> >> </span>
                    </div>
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
        $("#payment_form input.date").datetimepicker({
            dateFormat: 'yy-mm-dd',
        });
        
        $(".btn-add-payment").click(function(){
            // $("#payment_form input.form-control").val('');
            let status = $(this).parents('tr').find('.status').data('value');
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

        $("#keyword_filter").change(function(){
            $("#keyword_filter_form").submit();
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
        })

        $(".btn-approve").click(function(e){
            e.preventDefault();
            let image_path = $(this).data('path');
            let url = $(this).attr('href');
            var image_array = [];
            $(this).parents('tr').find(".image-array").find(".image-path").each(function(){
                image_array.push($(this).val());
            });
            if(image_array.length == 0) image_array.push("{{asset('images/no-image.png')}}");
            $("#btn_approve").attr("href", url);
            $("#purchase_image").html('')
            let current = 0;
            $("#purchase_image").verySimpleImageViewer({
                imageSource: image_array[current],
                frame: ['100%', '100%'],
                maxZoom: '900%',
                zoomFactor: '10%',
                mouse: true,
                keyboard: true,
                toolbar: true,
            });
            $("#approveModal").modal();

            $("#prev_img").click(function(){
                if(current > 0) current--;
                $(".jqvsiv_main_image_content img").attr('src', image_array[current]);
            });
            $("#next_img").click(function(){
                if(current < image_array.length) current++;
                $(".jqvsiv_main_image_content img").attr('src', image_array[current]);
            });
        }); 
    });
</script>
@endsection
