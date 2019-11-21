@extends('layouts.master')
@section('style')
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    <script src="{{asset('master/plugins/vuejs/vue.js')}}"></script>
    <script src="{{asset('master/plugins/vuejs/axios.js')}}"></script>    
    <style>
        .table>tbody>tr>td {
            padding-top: .5rem;
            padding-bottom: .5rem;
        }
    </style>
@endsection
@section('content')
<div class="content" id="page" style="opacity: 0;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="pull-left page-title"><i class="fa fa-money"></i> {{__('page.purchase_order')}}</h3>
                <ol class="breadcrumb pull-right">
                    <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                    <li><a href="#">{{__('page.purchase_order')}}</a></li>
                    <li class="active">{{__('page.list')}}</li>
                </ol>
            </div>
        </div>        
        @php
            $user = Auth::user();
            $role = $user->role->slug;
        @endphp
        <div class="card card-body p-lg-5">
            <form class="form-layout form-layout-1" action="{{route('pre_order.save')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mt-3">
                    <div class="col-lg-6 mb-2">
                        <div class="form-group">
                            <label class="form-control-label">{{__('page.date')}}<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="date" id="pre_order_date" value="{{date('Y-m-d H:i')}}"placeholder="{{__('page.date')}}" autocomplete="off" required>
                            @error('date')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 mb-2">
                        <div class="form-group">
                            <label class="form-control-label">{{__('page.reference_number')}}</label>
                            <input class="form-control" type="text" name="reference_number" value="{{ old('reference_number') }}" required placeholder="{{__('page.reference_number')}}">
                            @error('reference_number')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    @if(!$user->company)
                        <div class="col-md-6 col-lg-4">
                            <label for="company_id" class="form-control-label">{{__('page.company')}}</label>
                            <select name="company_id" class="form-control" id="company_id" required>
                                <option value="" hidden>{{__('page.select_company')}}</option>
                                @foreach ($companies as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach                                
                            </select>
                        </div>
                    @endif
                    <div class="col-md-6 @if(!$user->company) col-lg-4 @endif">
                        <div class="form-group mb-2">
                            <label class="form-control-label">{{__('page.supplier')}}</label>
                            <div class="input-group">                                  
                                <select class="form-control select2-show-search" name="supplier" id="search_supplier" required data-placeholder="{{__('page.select_supplier')}}">
                                    <option hidden>{{__('page.select_supplier')}}</option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{$item->id}}" @if(old('supplier') == $item->id) selected @endif>{{$item->company}}</option>
                                    @endforeach
                                </select>
                                <span class="input-group-btn">
                                    <button class="bd bg-primary text-white ml-1" id="btn-add-supplier" style="border-radius:100px !important;font-size:14px;padding:6px 11px;" type="button"><i class="fa fa-plus"></i></button>
                                </span>  
                            </div>
                            @error('supplier')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6 @if(!$user->company) col-lg-4 @endif">
                        <div class="form-group mb-2">
                            <label class="form-control-label">{{__('page.attachment')}}:</label>
                            <input type="file" name="attachment" id="file2" class="file-input-styled" accept="image/*">
                        </div>
                    </div>
                </div> 
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div>
                            <h4 class="mt-3" style="float:left">{{__('page.order_items')}}</h4>
                            <button type="button" class="btn btn-sm btn-primary btn-icon mb-2 wave-effect add-product" title="{{__('page.right_ctrl_key')}}" style="float:right" @click="add_item()"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-sm btn-success wave-effect mb-3 mr-3" id="btn_create_product" title="Shift Key" style="float:right"><i class="fa fa-plus"></i> {{__('page.new_product')}}</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="product_table">
                                <thead class="table-success">
                                    <tr>
                                        <th>{{__('page.product_name_code')}}</th>
                                        <th>{{__('page.product_cost')}}</th>
                                        <th>{{__('page.discount')}}</th>
                                        <th>{{__('page.quantity')}}</th>
                                        <th>{{__('page.subtotal')}}</th>
                                        <th style="width:30px"></th>
                                    </tr>
                                </thead>
                                <tbody class="top-search-form">
                                    <tr v-for="(item,i) in order_items" :key="i">
                                        <td>
                                            <input type="hidden" name="product_id[]" class="product_id" :value="item.product_id" />
                                            <input type="text" name="product_name[]" ref="product" class="form-control form-control-sm product" v-model="item.product_name_code" required />
                                        </td>
                                        {{-- <td><input type="date" class="form-control form-control-sm expiry_date" name="expiry_date[]" autocomplete="off" v-model="item.expiry_date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="{{__('page.expiry_date')}}" /></td> --}}
                                        <td><input type="number" class="form-control form-control-sm cost" name="cost[]" v-model="item.cost" required placeholder="{{__('page.product_cost')}}" /></td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm discount_string" name="discount_string[]" v-model="item.discount_string" required placeholder="{{__('page.discount')}}" />
                                            <input type="hidden" class="discount" name="discount[]" v-model="item.discount" />
                                        </td>
                                        <td><input type="number" class="form-control form-control-sm quantity" name="quantity[]" v-model="item.quantity" required placeholder="{{__('page.quantity')}}" /></td>
                                        {{-- <td class="tax">@{{item.tax_name}}</td> --}}
                                        <td class="subtotal">
                                            @{{formatPrice(item.sub_total)}}
                                            <input type="hidden" name="subtotal[]" :value="item.sub_total" />
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning btn-icon wave-effect remove-product" @click="remove(i)"><i class="fa fa-times"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">{{__('page.total')}}</th>
                                        <th class="total_discount">@{{formatPrice(total.discount)}}</th>
                                        <th></th>
                                        <th colspan="2" class="total">
                                            @{{formatPrice(total.cost)}}
                                            <input type="hidden" name="grand_total" :value="grand_total">
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-2">
                            <label class="form-control-label">{{__('page.note')}}</label>
                            <textarea class="form-control" name="note" rows="3" placeholder="{{__('page.note')}}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-layout-footer text-right mt-3">
                    <button type="submit" class="btn btn-primary mr-3"><i class="fa fa-check mr-2"></i>{{__('page.save')}}</button>
                    <a href="{{route('pre_order.index')}}" class="btn btn-warning"><i class="fa fa-times mr-2"></i>{{__('page.cancel')}}</a>
                </div>
            </form>
        </div>
        <div class="modal fade" id="addProductModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{__('page.new_product')}}</h4>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <form action="" id="create_product_form" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_name')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" placeholder="{{__('page.product_name')}}" required>
                                <span id="product_name_error" class="invalid-feedback">
                                    <strong></strong>
                                </span>                            
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_code')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="code" placeholder="{{__('page.product_code')}}" required>
                                <span id="product_code_error" class="invalid-feedback">
                                    <strong></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_unit')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="unit" placeholder="{{__('page.product_unit')}}" required>
                                <span id="product_unit_error" class="invalid-feedback">
                                    <strong></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_cost')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="cost" placeholder="{{__('page.product_cost')}}" required>
                                <span id="product_cost_error" class="invalid-feedback">
                                    <strong></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.product_image')}}:</label>                                
                                <label class="custom-file wd-100p">
                                    <input type="file" name="image" id="product_image" class="file-input-styled" accept="image/*">
                                </label>
                            </div>
                        </div>    
                        <div class="modal-footer">
                            <button type="button" id="btn_product_create" class="btn btn-primary btn-submit" @click="new_product()"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mg-r-10"></i>&nbsp;{{__('page.close')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>        
    </div>

    <div class="modal fade" id="addSupplierModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.add_supplier')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="" id="create_form" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">{{__('page.name')}}</label>
                            <input class="form-control name" type="text" name="name" placeholder="{{__('page.name')}}">
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
                        <div class="form-group">
                            <label class="control-label">{{__('page.note')}}</label>
                            <textarea class="form-control note" name="note" rows="3" placeholder="{{__('page.note')}}"></textarea>
                            <span id="note_error" class="invalid-feedback">
                                <strong></strong>
                            </span>
                        </div>
                    </div>    
                    <div class="modal-footer">
                        <button type="button" id="btn_create" class="btn btn-primary btn-submit"><i class="fa fa-check mr-3"></i>{{__('page.save')}}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-3"></i>{{__('page.close')}}</button>
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
<script src="{{asset('master/plugins/styling/uniform.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#search_supplier').wrap('<div class="position-relative" style="width: calc(100% - 45px)"></div>')
                    .select2({
                        width: 'resolve',
                    });
        $("#purchase_date").datetimepicker({
            dateFormat: 'yy-mm-dd',
        });
        $(".expiry_date").datepicker({
            dateFormat: 'yy-mm-dd',
        });

        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });

        $("#btn-add-supplier").click(function(){
            $("#create_form input.form-control").val('');
            $("#create_form .invalid-feedback strong").text('');
            $("#addSupplierModal").modal();
        });

        $("#btn_create").click(function(){
            $("#ajax-loading").show();
            $.ajax({
                url: "{{route('supplier.purchase_create')}}",
                type: 'post',
                dataType: 'json',
                data: $('#create_form').serialize(),
                success : function(data) {
                    $("#ajax-loading").hide();
                    if(data.id != null) {
                        $("#addSupplierModal").modal('hide');
                        $("#search_supplier").append(`
                            <option value="${data.id}">${data.company}</option>
                        `).val(data.id);
                    } else if (data.message == 'The given data was invalid.') {
                        alert(data.message);
                    }
                },
                error: function(data) {
                    $("#ajax-loading").hide();
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

        $("#btn_create_product").click(function(){
            $("#addProductModal").modal();
        });
    });
</script>
<script src="{{ asset('js/pre_order_items.js') }}"></script>
@endsection
