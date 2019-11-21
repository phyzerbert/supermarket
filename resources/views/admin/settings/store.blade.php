@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-th-large"></i> {{__('page.store_management')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.store')}}</li>
                    </ol>
                </div>
            </div>         
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="">
                    @if ($role == 'admin')
                        <button type="button" class="btn btn-success btn-sm float-right mg-b-5" id="btn-add"><i class="icon ion-plus mg-r-2"></i> Add New</button>
                    @endif
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>{{__('page.name')}}</th>
                                <th>{{__('page.company')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="name">{{$item->name}}</td>
                                    <td class="company" data-id="{{$item->company_id}}">@if($item->company){{$item->company->name}}@endif</td>
                                    <td class="py-1">
                                        <a href="#" class="btn btn-sm btn-primary btn-icon mr-1 btn-edit" data-id="{{$item->id}}"><div><i class="fa fa-edit"></i></div></a>
                                        <a href="{{route('store.delete', $item->id)}}" class="btn btn-sm btn-danger btn-icon btn-confirm" data-id="{{$item->id}}"><div><i class="fa fa-trash-o"></i></div></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>                
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>Total <strong style="color: red">{{ $data->total() }}</strong> Items</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends([])->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>                
    </div>

    <!-- The Modal -->
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.add_store')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('store.create')}}" id="create_form" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.name')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.name')}}" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.company')}}</label>
                            <select class="form-control company" name="company" required>
                                <option value="" hidden>{{__('page.select_company')}}</option>
                                @foreach ($companies as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
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
                    <h4 class="modal-title">{{__('page.edit_stoer')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('store.edit')}}" id="edit_form" method="post">
                    @csrf
                    <input type="hidden" name="id" class="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.name')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.name')}}" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label">{{__('page.company')}}</label>
                            <select class="form-control company" name="company" required>
                                <option value="" hidden>{{__('page.select_company')}}</option>
                                @foreach ($companies as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
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
            $("#create_form .invalid-feedback strong").text('');
            $("#addModal").modal();
        });

        $(".btn-edit").click(function(){
            let id = $(this).data("id");
            let name = $(this).parents('tr').find(".name").text().trim();
            let company = $(this).parents('tr').find(".company").data('id');
            $("#edit_form input.form-control").val('');
            $("#editModal .id").val(id);
            $("#editModal .name").val(name);
            $("#editModal .company").val(company);
            $("#editModal").modal();
        });

    });
</script>
@endsection
