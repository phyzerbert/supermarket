@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-star-half-o"></i> {{__('page.tax_rate_management')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="#">{{__('page.setting')}}</a></li>
                        <li class="active">{{__('page.tax_rate')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="">
                    @if ($role == 'admin')
                        <button type="button" class="btn btn-sm btn-success float-right mb-2" id="btn-add"><i class="icon ion-plus mg-r-2"></i>{{__('page.add_new')}}</button>
                    @endif
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>{{__('page.name')}}</th>
                                <th>{{__('page.code')}}</th>
                                <th>{{__('page.rate')}}</th>
                                <th>{{__('page.type')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $loop->index+1 }}</td>
                                    <td class="name">{{$item->name}}</td>
                                    <td class="code">{{$item->code}}</td>
                                    <td class="rate">{{$item->rate}}</td>
                                    <td class="type" data-id="{{$item->type}}">
                                        @if ($item->type == 1)
                                            <span class="badge badge-primary">{{__('page.percentage')}}</span>
                                        @elseif($item->type == 2)
                                            <span class="badge badge-info">{{__('page.fixed')}}</span>
                                        @endif
                                    </td>
                                    <td class="py-1">
                                        <a href="#" class="btn btn-sm btn-primary btn-icon mr-2 btn-edit" data-id="{{$item->id}}"><div><i class="fa fa-edit"></i></div></a>
                                        <a href="{{route('tax_rate.delete', $item->id)}}" class="btn btn-sm btn-danger btn-icon btn-confirm" data-id="{{$item->id}}"><div><i class="fa fa-trash-o"></i></div></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>                
    </div>

    <!-- The Modal -->
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.add_tax_rate')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('tax_rate.create')}}" id="create_form" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.name')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.name')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.code')}}</label>
                            <input class="form-control code" type="text" name="code" placeholder="{{__('page.code')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.rate')}}</label>
                            <input class="form-control rate" type="number" name="rate" min="0" placeholder="{{__('page.rate')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.type')}}</label>
                            <select class="form-control type" name="type">
                                <option value="" hidden>{{__('page.select_type')}}</option>
                                <option value="1">{{__('page.percentage')}}</option>
                                <option value="2">{{__('page.fixed')}}</option>
                            </select>
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
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.edit_tax_rate')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('tax_rate.edit')}}" id="edit_form" method="post">
                    @csrf
                    <input type="hidden" name="id" class="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.name')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.name')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.code')}}</label>
                            <input class="form-control code" type="text" name="code" placeholder="{{__('page.code')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.rate')}}</label>
                            <input class="form-control rate" type="number" name="rate" min="0" placeholder="{{__('page.rate')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.type')}}</label>
                            <select class="form-control type" name="type">
                                <option value="" hidden>{{__('page.select_type')}}</option>
                                <option value="1">{{__('page.percentage')}}</option>
                                <option value="2">{{__('page.fixed')}}</option>
                            </select>
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
<script>
    $(document).ready(function () {
        
        $("#btn-add").click(function(){
            $("#create_form input.form-control").val('');
            $("#addModal").modal();
        });

        $(".btn-edit").click(function(){
            let id = $(this).data("id");
            let name = $(this).parents('tr').find(".name").text().trim();
            let code = $(this).parents('tr').find(".code").text().trim();
            let rate = $(this).parents('tr').find(".rate").text().trim();
            let type = $(this).parents('tr').find(".type").data('id');
            $("#edit_form input.form-control").val('');
            $("#editModal .id").val(id);
            $("#editModal .name").val(name);
            $("#editModal .code").val(code);
            $("#editModal .rate").val(rate);
            $("#editModal .type").val(type);
            $("#editModal").modal();
        });

    });
</script>
@endsection
