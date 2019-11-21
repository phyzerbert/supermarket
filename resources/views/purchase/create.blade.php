@extends('layouts.master')
@section('style')
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    {{-- <script src="{{asset('master/plugins/vuejs/vue.js')}}"></script>
    <script src="{{asset('master/plugins/vuejs/axios.js')}}"></script> --}}
    <style>
        .table>tbody>tr>td {
            padding-top: .5rem;
            padding-bottom: .5rem;
        }
        #btn-change-rate {
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-plus-circle"></i> {{__('page.add_purchase')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('purchase.index')}}">{{__('page.purchase')}}</a></li>
                        <li class="active">{{__('page.add')}}</li>
                    </ol>
                </div>
            </div>

            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill p-md-5" id="page">
                <form class="form-layout form-layout-1" action="{{route('purchase.save')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.purchase_date')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="date" id="purchase_date" value="{{date('Y-m-d H:i')}}"placeholder="{{__('page.purchase_date')}}" autocomplete="off" required>
                                @error('date')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.reference_number')}}:</label>
                                <input class="form-control" type="text" name="reference_number" value="{{ old('reference_number') }}" required placeholder="{{__('page.reference_number')}}">
                                @error('reference_number')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.store')}}</label>
                                <select class="form-control" name="store">
                                    <option value="" hidden>{{__('page.store')}}</option>
                                    @foreach ($stores as $item)
                                        <option value="{{$item->id}}" @if($loop->index == 0) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('store')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.credit_days')}}:</label>
                                <input type="number" class="form-control" name="credit_days" min=0 value="{{old('credit_days')}}" required placeholder="{{__('page.credit_days')}}" />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.supplier')}}:</label>
                                <div class="input-group">                                  
                                    <select class="form-control select2-show-search" name="supplier" id="search_supplier" required>
                                        <option value="" hidden>{{__('page.select_supplier')}}</option>
                                        @foreach ($suppliers as $item)
                                            <option value="{{$item->id}}" @if(old('supplier') == $item->id) selected @endif>{{$item->company}}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-btn">
                                        <button class="btn bd bg-primary text-white ml-1" id="btn-add-supplier" style="border-radius:100px !important" type="button"><i class="fa fa-plus"></i></button>
                                    </span>  
                                </div>
                                @error('supplier')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.attachment')}}:</label>
                                <input type="file" name="attachment[]" id="file2" class="file-input-styled" multiple />
                            </div>
                        </div>
                        @php
                            $currencies = \App\Models\Currency::all();
                        @endphp
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label w-100">
                                    {{__('page.currency')}}: 
                                    <span class="badge badge-success float-right" id="btn-change-rate" data-toggle="modal" data-target="#rateModal">{{__('page.change_rate')}}</span>
                                </label>
                                <select name="currency_id" class="form-control" v-model="currency" @change="convert_currnecy">
                                    @foreach ($currencies as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div>
                                <h4 class="mg-t-10" style="float:left">{{__('page.order_items')}}</h4>
                                <a href="#" class="btn btn-sm btn-primary btn-icon mb-2 add-product" style="float:right" @click="add_item()"><div><i class="fa fa-plus"></i></div></a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="product_table">
                                    <thead class="table-success">
                                        <tr>
                                            <th>{{__('page.product_name_code')}}</th>
                                            <th>{{__('page.expiry_date')}}</th>
                                            <th>{{__('page.product_cost')}}</th>
                                            <th>{{__('page.quantity')}}</th>
                                            <th>{{__('page.product_tax')}}</th>
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
                                            <td><input type="date" class="form-control form-control-sm expiry_date" name="expiry_date[]" autocomplete="off" v-model="item.expiry_date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="{{__('page.expiry_date')}}" /></td>
                                            <td><input type="number" class="form-control form-control-sm cost" name="cost[]" v-model="item.cost" step="0.01" required placeholder="{{__('page.product_cost')}}" /></td>
                                            <td><input type="number" class="form-control form-control-sm quantity" name="quantity[]" v-model="item.quantity" required placeholder="{{__('page.quantity')}}" /></td>
                                            <td class="tax">@{{item.tax_name}}</td>
                                            <td class="subtotal">
                                                @{{item.sub_total | currency}}
                                                <input type="hidden" name="subtotal[]" :value="item.sub_total" />
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-warning btn-icon remove-product" @click="remove(i)"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">{{__('page.total')}}</th>
                                            <th class="total_quantity">@{{total.quantity}}</th>
                                            <th class="total_tax"></th>
                                            <th colspan="2" class="total">@{{total.cost | currency}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>                      
                        <div class="col-md-4 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.discount')}}:</label>
                                <input type="text" name="discount_string" class="form-control" v-model="discount_string" placeholder="{{__('page.discount')}}">
                                <input type="hidden" name="discount" :value="discount">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.shipping')}}:</label>
                                <input type="text" name="shipping_string" class="form-control" v-model="shipping_string" placeholder="{{__('page.shipping')}}">
                                <input type="hidden" name="shipping" :value="shipping">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.returns')}}:</label>
                                <input type="number" name="returns" class="form-control" min="0" v-model="returns" placeholder="{{__('page.returns')}}">
                                <input type="hidden" name="grand_total" :value="grand_total">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-right">{{__('page.purchase')}}: @{{total.cost | currency}} - {{__('page.discount')}}: @{{discount | currency}} - {{__('page.shipping')}}: @{{shipping | currency}} - {{__('page.returns')}}: @{{returns | currency}} = {{__('page.grand_total')}}: @{{grand_total | currency}}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.note')}}:</label>
                                <textarea class="form-control" name="note" rows="3" placeholder="{{__('page.note')}}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-check mr-2"></i>{{__('page.save')}}</button>
                        <a href="{{route('purchase.index')}}" class="btn btn-warning"><i class="fa fa-times mr-2"></i>{{__('page.cancel')}}</a>
                    </div>
                </form>
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
                        <button type="button" id="btn_create" class="btn btn-primary btn-submit"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mg-r-10"></i>&nbsp;{{__('page.close')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rateModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('page.change_rate')}}</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{route('currency.save')}}" id="rate_form" method="post">
                    @csrf
                    @php                        
                        $rate_bolivar = \App\Models\Currency::find(1)->rate;
                        $rate_dollar = \App\Models\Currency::find(2)->rate;
                        $rate_euro = \App\Models\Currency::find(3)->rate;
                    @endphp
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">Bolivar</label>
                            <input class="form-control" type="text" name="rate_bolivar" value="{{$rate_bolivar}}" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Dollar</label>
                            <input class="form-control" type="text" name="rate_dollar" value="{{$rate_dollar}}" />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Euro</label>
                            <input class="form-control" type="text" name="rate_euro" value="{{$rate_euro}}" />
                        </div>
                    </div>  
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-submit"><i class="fa fa-check mg-r-10"></i>&nbsp;{{__('page.save')}}</button>
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
<script src="{{asset('master/plugins/styling/uniform.min.js')}}"></script>
<script>
    $(document).ready(function () {

        $("#purchase_date").datetimepicker({
            dateFormat: 'yy-mm-dd',
        });
        $(".expiry_date").datepicker({
            dateFormat: 'yy-mm-dd',
        });

        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });        
        
        $('#search_supplier').wrap('<div class="position-relative" style="width: calc(100% - 45px)"></div>')
                    .select2({
                        width: 'resolve',
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
                    }
                    else if(data.message == 'The given data was invalid.') {
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

    });
</script>
<script src="{{ asset('js/purchase_create.js') }}"></script>
@endsection
