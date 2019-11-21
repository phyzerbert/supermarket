@extends('layouts.master')
@section('style')    
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-money"></i> {{__('page.return_list')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li>{{__('page.returns')}}</li>
                        <li class="active">{{__('page.list')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card">
                <div class="card-body table-responsive mt-2">
                    <div class="clearfix">
                        <a href="{{route('preturn.report', $id)}}" class="btn btn-primary btn-sm float-right mg-b-5"><i class="fa fa-file-pdf-o mr-2"></i>{{__('page.report')}}</a>
                    </div>
                    <table class="table table-bordered table-hover mt-2">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>{{__('page.date')}}</th>
                                <th>{{__('page.reference_no')}}</th>
                                <th>{{__('page.amount')}}</th> 
                                <th>{{__('page.note')}}</th>
                                @if(in_array($role, ['admin', 'user']))
                                    <th>{{__('page.action')}}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($data as $item)
                                <tr class="@if($item->status == 0) text-danger @endif">
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td class="date">{{date('Y-m-d H:i', strtotime($item->timestamp))}}</td>
                                    <td class="reference_no">{{$item->reference_no}}</td>
                                    <td class="amount" data-value="{{$item->amount}}">{{number_format($item->amount)}}</td>
                                    <td class="" data-path="{{$item->attachment}}">
                                        <span class="tx-info note">{{$item->note}}</span>&nbsp;
                                        @if($item->attachment != "")
                                            <a href="{{asset($item->attachment)}}" class="attachment"><i class="fa fa-paperclip"></i></a>
                                        @endif
                                    </td>
                                    @if(in_array($role, ['admin', 'user']))
                                        <td class="py-1">
                                            <a href="{{route('preturn.approve', $item->id)}}" class="btn btn-info btn-icon wave-effect mr-2 btn-confirm" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{__('page.approve')}}"><i class="fa fa-check-circle-o"></i></a>
                                            <a href="#" class="btn btn-primary btn-icon wave-effect mr-2 btn-edit" data-id="{{$item->id}}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{__('page.edit')}}"><i class="fa fa-edit"></i></a>
                                            <a href="{{route('preturn.delete', $item->id)}}" class="btn btn-danger btn-icon wave-effect btn-confirm" data-id="{{$item->id}}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{__('page.delete')}}"><i class="fa fa-trash-o"></i></a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                        
                    <div class="row">
                        <div class="col-md-12 mt-3 text-right">
                            @if($role == 'user' || $role == 'secretary')
                                <a href="{{route('purchase.create')}}" class="btn btn-primary mr-3">{{__('page.add_purchase')}}</a>
                            @endif
                            <a href="{{route('purchase.index')}}" class="btn btn-success mg-r-30">{{__('page.purchases_list')}}</a>
                        </div>
                    </div>
                </div>                
            </div>                
        </div>
    </div>

    <!-- The Modal -->
    
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.edit_return')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('preturn.edit')}}" id="edit_form" method="post" enctype="multipart/form-data">
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
@endsection

@section('script')
<script src="{{asset('master/plugins/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script>
<script src="{{asset('master/plugins/styling/uniform.min.js')}}"></script>
<script>
    $(document).ready(function () {
        
        // $("#btn-add").click(function(){
        //     $("#create_form input.form-control").val('');
        //     $("#create_form .invalid-feedback strong").text('');
        //     $("#addModal").modal();
        // });

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
            $("#editModal input.form-control").val('');
            $("#editModal .id").val(id);
            $("#editModal .date").val(date);
            $("#editModal .reference_no").val(reference_no);
            $("#editModal .amount").val(amount);
            $("#editModal .note").val(note);
            $("#editModal").modal();
        });

        $(".attachment").click(function(e){
            e.preventDefault();
            let path = $(this).attr('href');
            $("#image_preview").html('')
            $("#image_preview").verySimpleImageViewer({
                imageSource: path,
                frame: ['100%', '100%'],
                maxZoom: '900%',
                zoomFactor: '10%',
                mouse: true,
                keyboard: true,
                toolbar: true,
            });
            $("#attachModal").modal();
        });

    });
</script>
@endsection
