@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-user-circle-o"></i> {{__('page.customer_management')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.customer_management')}}</li>
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
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="company" id="search_company" value="{{$company}}" placeholder="{{__('page.company')}}">
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="phone_number" id="search_phone" value="{{$phone_number}}" placeholder="{{__('page.phone_number')}}">
                        
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
                        <button type="button" class="btn btn-sm btn-info mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
                    </form>
                    <button type="button" class="btn btn-success btn-sm float-right mg-b-5" id="btn-add"><i class="icon ion-person-add mg-r-2"></i> {{__('page.add_new')}}</button>                    
                </div>
                <div class="table-responsive mt-2 pb-5">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-colored thead-primary">
                            <tr class="bg-blue">
                                <th style="width:30px;">#</th>
                                <th>{{__('page.name')}}</th>
                                <th>{{__('page.company')}}</th>
                                <th>{{__('page.email')}}</th>
                                <th>{{__('page.phone_number')}}</th>
                                <th>{{__('page.city')}}</th>
                                <th>{{__('page.address')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="name">{{$item->name}}</td>
                                    <td class="company" data-id="{{$item->company}}">@isset($item->company){{$item->company}}@endisset</td>
                                    <td class="email">{{$item->email}}</td>
                                    <td class="phone_number">{{$item->phone_number}}</td>
                                    <td class="city">{{$item->city}}</td>
                                    <td class="address">{{$item->address}}</td>
                                    <td class="py-2 text-center">                                        
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('page.action')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{route('customer.report', $item->id)}}" class="dropdown-item">{{__('page.report')}}</a></li>
                                                <li><a href="{{route('customer.email', $item->id)}}" class="dropdown-item">{{__('page.email')}}</a></li>
                                                <li><a href="{{route('customer.export', $item->id)}}" class="dropdown-item">{{__('page.export')}}</a></li>
                                                <li><a href="javascript:;" class="dropdown-item btn-edit" data-id="{{$item->id}}">{{__('page.edit')}}</a></li>
                                                <li><a href="{{route('customer.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                            </ul>
                                        </div>
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
                            {!! $data->appends(['name' => $name, 'company' => $company, 'phone_number' => $phone_number])->links() !!}
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
                    <h4 class="modal-title">{{__('page.add_customer')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="" id="create_form" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.name')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.customer_name')}}">
                            <span id="name_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.company')}}</label>
                            <input class="form-control company" type="text" name="company" placeholder="{{__('page.company_name')}}">
                            <span id="company_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.email')}}</label>
                            <input class="form-control email" type="email" name="email" placeholder="{{__('page.email_address')}}">
                            <span id="email_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.phone_number')}}</label>
                            <input class="form-control phone_number" type="text" name="phone_number" placeholder="{{__('page.phone_number')}}">
                            <span id="phone_number_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.address')}}</label>
                            <input class="form-control address" type="text" name="address" placeholder="{{__('page.address')}}">
                            <span id="address_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.city')}}</label>
                            <input class="form-control city" type="text" name="city" placeholder="{{__('page.city')}}">
                            <span id="city_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                    </div>    
                    <div class="modal-footer">
                        <button type="button" id="btn_create" class="btn btn-primary btn-submit"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
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
                    <h4 class="modal-title">{{__('page.edit_customer')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="" id="edit_form" method="post">
                    @csrf
                    <input type="hidden" name="id" class="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.name')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.customer_name')}}">
                            <span id="edit_name_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.company')}}</label>
                            <input class="form-control company" type="text" name="company" placeholder="{{__('page.company_name')}}">
                            <span id="edit_company_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.email')}}</label>
                            <input class="form-control email" type="email" name="email" placeholder="{{__('page.email_address')}}">
                            <span id="edit_email_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.phone_number')}}</label>
                            <input class="form-control phone_number" type="text" name="phone_number" placeholder="{{__('page.phone_number')}}">
                            <span id="edit_phone_number_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.address')}}</label>
                            <input class="form-control address" type="text" name="address" placeholder="{{__('page.address')}}">
                            <span id="edit_address_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('page.city')}}</label>
                            <input class="form-control city" type="text" name="city" placeholder="{{__('page.city')}}">
                            <span id="edit_city_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                    </div>  
                    <div class="modal-footer">
                        <button type="button" id="btn_update" class="btn btn-primary btn-submit"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
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

        $("#btn_create").click(function(){          
            $.ajax({
                url: "{{route('customer.create')}}",
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
                },
                error: function(data) {
                    console.log(data.responseJSON);
                    if(data.responseJSON.message == 'The given data was invalid.') {
                        let messages = data.responseJSON.errors;
                        if(messages.name) {
                            $('#name_error strong').text(data.responseJSON.errors.name[0]);
                            $('#name_error').show();
                            $('#create_form .name').focus();
                        }
                        
                        if(messages.company) {
                            $('#company_error strong').text(data.responseJSON.errors.company[0]);
                            $('#company_error').show();
                            $('#create_form .company').focus();
                        }

                        if(messages.email) {
                            $('#email_error strong').text(data.responseJSON.errors.email[0]);
                            $('#email_error').show();
                            $('#create_form .email').focus();
                        }

                        if(messages.phone_number) {
                            $('#phone_number_error strong').text(data.responseJSON.errors.phone_number[0]);
                            $('#phone_number_error').show();
                            $('#create_form .phone_number').focus();
                        }

                        if(messages.address) {
                            $('#address_error strong').text(data.responseJSON.errors.address[0]);
                            $('#address_error').show();
                            $('#create_form .address').focus();
                        }

                        if(messages.city) {
                            $('#city_error strong').text(data.responseJSON.errors.city[0]);
                            $('#city_error').show();
                            $('#create_form .city').focus();
                        }
                    }
                }
            });
        });

        $(".btn-edit").click(function(){
            let id = $(this).data("id");
            let name = $(this).parents('tr').find(".name").text().trim();
            let company = $(this).parents('tr').find(".company").text().trim();
            let email = $(this).parents('tr').find(".email").text().trim();
            let phone_number = $(this).parents('tr').find(".phone_number").text().trim();
            let city = $(this).parents('tr').find(".city").text().trim();
            let address = $(this).parents('tr').find(".address").text().trim();

            $("#edit_form input.form-control").val('');
            $("#editModal .id").val(id);
            $("#editModal .name").val(name);
            $("#editModal .company").val(company);
            $("#editModal .email").val(email);
            $("#editModal .phone_number").val(phone_number);
            $("#editModal .city").val(city);
            $("#editModal .address").val(address);

            $("#editModal").modal();
        });

        $("#btn_update").click(function(){
            $.ajax({
                url: "{{route('customer.edit')}}",
                type: 'post',
                dataType: 'json',
                data: $('#edit_form').serialize(),
                success : function(data) {
                    console.log(data);
                    if(data == 'success') {
                        Swal.fire("{{__('page.updated_successfully')}}").then(function() {
                            window.location.reload();
                        })
                    }
                    else if(data.message == 'The given data was invalid.') {
                        Swal.fire(data.message)
                    }
                },
                error: function(data) {
                    console.log(data.responseJSON);
                    if(data.responseJSON.message == 'The given data was invalid.') {
                        let messages = data.responseJSON.errors;
                        if(messages.name) {
                            $('#edit_name_error strong').text(data.responseJSON.errors.name[0]);
                            $('#edit_name_error').show();
                            $('#edit_form .name').focus();
                        }
                        
                        if(messages.company) {
                            $('#edit_company_error strong').text(data.responseJSON.errors.company[0]);
                            $('#edit_company_error').show();
                            $('#edit_form .company').focus();
                        }

                        if(messages.email) {
                            $('#edit_email_error strong').text(data.responseJSON.errors.email[0]);
                            $('#edit_email_error').show();
                            $('#edit_form .email').focus();
                        }

                        if(messages.phone_number) {
                            $('#edit_phone_number_error strong').text(data.responseJSON.errors.phone_number[0]);
                            $('#edit_phone_number_error').show();
                            $('#edit_form .phone_number').focus();
                        }

                        if(messages.address) {
                            $('#edit_address_error strong').text(data.responseJSON.errors.address[0]);
                            $('#edit_address_error').show();
                            $('#edit_form .address').focus();
                        }

                        if(messages.city) {
                            $('#edit_city_error strong').text(data.responseJSON.errors.city[0]);
                            $('#edit_city_error').show();
                            $('#edit_form .city').focus();
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

        $("#pagesize").change(function(){
            $("#pagesize_form").submit();
        });

    });
</script>
@endsection
