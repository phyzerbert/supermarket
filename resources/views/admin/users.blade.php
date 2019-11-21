@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-users"></i> {{__('page.user_management')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.user_management')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="">
                    @include('elements.pagesize')
                    <form action="" method="POST" class="form-inline float-left" id="searchForm">
                        @csrf
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="name" id="search_name" value="{{$name}}" placeholder="{{__('page.name')}}">
                        <select class="form-control form-control-sm mr-sm-2 mb-2" name="company_id" id="search_company">
                            <option value="" hidden>{{__('page.select_company')}}</option>
                            @foreach ($companies as $item)
                                <option value="{{$item->id}}" @if ($company_id == $item->id) selected @endif>{{$item->name}}</option>
                            @endforeach        
                        </select>
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="phone_number" id="search_phone" value="{{$phone_number}}" placeholder="{{__('page.phone_number')}}">
                        
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
                        <button type="button" class="btn btn-sm btn-info mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
                    </form>
                    @if ($role == 'admin')
                        <button type="button" class="btn btn-success btn-sm float-right mg-b-5" id="btn-add"><i class="icon ion-person-add mg-r-2"></i> {{__('page.add_new')}}</button>
                    @endif
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-colored thead-primary">
                            <tr class="bg-blue">
                                <th class="wd-40">#</th>
                                <th>{{__('page.username')}}</th>
                                <th>{{__('page.first_name')}}</th>
                                <th>{{__('page.last_name')}}</th>
                                <th>{{__('page.company')}}</th>
                                <th>{{__('page.phone_number')}}</th>
                                <th>{{__('page.role')}}</th>
                                <th>{{__('page.ip_address')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="username">{{$item->name}}</td>
                                    <td class="first_name">{{$item->first_name}}</td>
                                    <td class="last_name">{{$item->last_name}}</td>
                                    <td class="company" data-id="{{$item->company_id}}">@isset($item->company->name){{$item->company->name}}@endisset</td>
                                    <td class="phone">{{$item->phone_number}}</td>
                                    <td class="role" data-id="{{$item->role_id}}">{{$item->role->name}}</td>
                                    <td class="ip_address">{{$item->ip_address}}</td>
                                    <td class="py-1">
                                        <a href="#" class="btn btn-sm btn-primary btn-icon mr-1 btn-edit" data-id="{{$item->id}}"><div><i class="fa fa-edit"></i></div></a>
                                        <a href="{{route('user.delete', $item->id)}}" class="btn btn-sm btn-danger btn-icon mr-1 btn-confirm"><div><i class="fa fa-trash-o"></i></div></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>                
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends(['name' => $name, 'company_id' => $company_id, 'phone_number' => $phone_number])->links() !!}
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
                    <h4 class="modal-title">{{__('page.add_new_user')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="" id="create_form" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.username')}}</label>
                            <input class="form-control" type="text" name="name" id="name" placeholder="Username">
                            <span id="name_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.phone_number')}}</label>
                            <input class="form-control" type="text" name="phone_number" id="phone" placeholder="{{__('page.phone_number')}}">
                            <span id="phone_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.role')}}</label>
                            <select name="role" id="role" class="form-control">
                                <option value="1">{{__('page.admin')}}</option>
                                <option value="2" selected>{{__('page.user')}}</option>
                                <option value="3">{{__('page.buyer')}}</option>
                                <option value="4">{{__('page.secretary')}}</option>
                            </select>
                            <span id="role_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>                        
                        <div class="form-group">
                            <label class="control-label">{{__('page.company')}}</label>
                            <select name="company_id" id="company_id" class="form-control">
                                <option value="">{{__('page.select_company')}}</option>
                                @foreach ($companies as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>                                    
                                @endforeach
                            </select>
                            <span id="company_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.ip_address')}}</label>
                            <input class="form-control" type="text" name="ip_address" id="ip_address" placeholder="{{__('page.ip_address')}}">
                            <span id="ip_address_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group password-field">
                            <label class="control-label">{{__('page.password')}}</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="{{__('page.password')}}">
                            <span id="password_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>    
                        <div class="form-group password-field">
                            <label class="control-label">{{__('page.password_confirm')}}</label>
                            <input type="password" name="password_confirmation" id="confirm_password" class="form-control" placeholder="{{__('page.password_confirm')}}">
                            <span id="confirm_password_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                    </div>    
                    <div class="modal-footer">
                        <button type="button" id="btn_create" class="btn btn-primary btn-submit"><i class="fa fa-check-circle-o"></i>&nbsp;{{__('page.save')}}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;{{__('page.close')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.edit_user')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="" id="edit_form" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" class="id" />                    
                        <div class="form-group">
                            <label class="control-label">{{__('page.username')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.username')}}">
                            <span id="edit_name_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>                    
                        <div class="form-group">
                            <label class="control-label">{{__('page.first_name')}}</label>
                            <input class="form-control first_name" type="text" name="first_name" placeholder="{{__('page.first_name')}}">
                            <span id="edit_first_name_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>                    
                        <div class="form-group">
                            <label class="control-label">{{__('page.last_name')}}</label>
                            <input class="form-control last_name" type="text" name="last_name" placeholder="{{__('page.last_name')}}">
                            <span id="edit_last_name_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.phone_number')}}</label>
                            <input class="form-control phone_number" type="text" name="phone_number" placeholder="{{__('page.phone_number')}}">
                            <span id="edit_phone_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>                        
                        <div class="form-group">
                            <label class="control-label">{{__('page.company')}}</label>
                            <select name="company_id" class="form-control company">
                                <option value="">{{__('page.select_company')}}</option>
                                @foreach ($companies as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>                                    
                                @endforeach
                            </select>
                            <span id="edit_company_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.ip_address')}}</label>
                            <input class="form-control ip_address" type="text" name="ip_address" id="edit_ip_address" placeholder="{{__('page.ip_address')}}">
                            <span id="edit_ip_address_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group password-field">
                            <label class="control-label">{{__('page.new_password')}}</label>
                            <input type="password" name="password" class="form-control" placeholder="{{__('page.new_password')}}">
                            <span id="edit_password_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>    
                        <div class="form-group password-field">
                            <label class="control-label">{{__('page.password_confirm')}}</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="{{__('page.password_confirm')}}">
                            <span id="edit_confirmpassword_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                    </div>    
                    <div class="modal-footer">
                        <button type="button" id="btn_update" class="btn btn-primary btn-submit"><i class="fa fa-check-circle-o"></i>&nbsp;{{__('page.save')}}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;{{__('page.close')}}</button>
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

        $("#btn_create").click(function(){  
            $("#ajax-loading").show();
            $.ajax({
                url: "{{route('user.create')}}",
                type: 'post',
                dataType: 'json',
                data: $('#create_form').serialize(),
                success : function(data) {
                    if(data == 'success') {
                        Swal.fire("{{__('page.created_successfully')}}").then(function() {
                            window.location.reload();
                        })
                    }
                    else if(data.message == 'The given data was invalid.') {
                        Swal.fire(data.message)
                    }
                    $("#ajax-loading").hide();
                },
                error: function(data) {
                    $("#ajax-loading").hide();
                    if(data.responseJSON.message == 'The given data was invalid.') {
                        let messages = data.responseJSON.errors;
                        if(messages.name) {
                            $('#name_error strong').text(data.responseJSON.errors.name[0]);
                            $('#name_error').show();
                            $('#create_form #name').focus();
                        }
                        
                        if(messages.role) {
                            $('#role_error strong').text(data.responseJSON.errors.role[0]);
                            $('#role_error').show();
                            $('#create_form #role').focus();
                        }
                        
                        if(messages.company_id) {
                            $('#company_error strong').text(data.responseJSON.errors.company_id[0]);
                            $('#company_error').show();
                            $('#create_form #company_id').focus();
                        }

                        if(messages.ip_address) {
                            $('#ip_address_error strong').text(data.responseJSON.errors.ip_address[0]);
                            $('#ip_address_error').show();
                            $('#create_form #ip_address').focus();
                        }

                        if(messages.password) {
                            $('#password_error strong').text(data.responseJSON.errors.password[0]);
                            $('#password_error').show();
                            $('#create_form #password').focus();
                        }

                        if(messages.phone_number) {
                            $('#phone_error strong').text(data.responseJSON.errors.phone_number[0]);
                            $('#phone_error').show();
                            $('#create_form #phone').focus();
                        }
                    }
                }
            });
        });

        $(".btn-edit").click(function(){
            let user_id = $(this).attr("data-id");
            let username = $(this).parents('tr').find(".username").text().trim();
            let first_name = $(this).parents('tr').find(".first_name").text().trim();
            let last_name = $(this).parents('tr').find(".last_name").text().trim();
            let ip_address = $(this).parents('tr').find(".ip_address").text().trim();
            let company = $(this).parents('tr').find(".company").data('id');
            let phone = $(this).parents('tr').find(".phone").text().trim();

            $("#edit_form input.form-control").val('');
            $("#edit_form .id").val(user_id);
            $("#edit_form .name").val(username);
            $("#edit_form .first_name").val(first_name);
            $("#edit_form .last_name").val(last_name);
            $("#edit_form .ip_address").val(ip_address);
            $("#edit_form .company").val(company);
            $("#edit_form .phone_number").val(phone);

            $("#editModal").modal();
        });

        $("#btn_update").click(function(){
            $("#ajax-loading").show();
            $.ajax({
                url: "{{route('user.edit')}}",
                type: 'post',
                dataType: 'json',
                data: $('#edit_form').serialize(),
                success : function(data) {
                    if(data == 'success') {
                        Swal.fire("{{__('page.updated_successfully')}}").then(function() {
                            window.location.reload();
                        })
                    }
                    else if(data.message == 'The given data was invalid.') {
                        Swal.fire(data.message)
                    }
                    $("#ajax-loading").hide();
                },
                error: function(data) {
                    $("#ajax-loading").hide();
                    if(data.responseJSON.message == 'The given data was invalid.') {
                        let messages = data.responseJSON.errors;
                        if(messages.name) {
                            $('#edit_name_error strong').text(data.responseJSON.errors.name[0]);
                            $('#edit_name_error').show();
                            $('#edit_form #edit_name').focus();
                        }

                        if(messages.ip_address) {
                            $('#edit_ip_address_error strong').text(data.responseJSON.errors.ip_address[0]);
                            $('#ip_address_error').show();
                            $('#edit_ip_address').focus();
                        }

                        if(messages.password) {
                            $('#edit_password_error strong').text(data.responseJSON.errors.password[0]);
                            $('#edit_password_error').show();
                            $('#edit_form #edit_password').focus();
                        }

                        if(messages.phone) {
                            $('#edit_phone_error strong').text(data.responseJSON.errors.phone[0]);
                            $('#edit_phone_error').show();
                            $('#edit_form #edit_phone').focus();
                        }
                    }
                }
            });
        });

        $("#btn-reset").click(function(){
            $("#search_name").val('');
            $("#search_company").val('');
            $("#search_phone").val('');
        });

    });
</script>
@endsection
