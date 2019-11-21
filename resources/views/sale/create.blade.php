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
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-plus-circle"></i> {{__('page.add_sale')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('sale.index')}}">{{__('page.sales')}}</a></li>
                        <li class="active">{{__('page.add')}}</li>
                    </ol>
                </div>
            </div>

            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill p-md-5" id="page">
                <form class="form-layout form-layout-1" action="{{route('sale.save')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.sale_date')}} <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="date" id="sale_date" value="{{date('Y-m-d H:i')}}" placeholder="{{__('page.sale_date')}}" autocomplete="off" required>
                                @error('date')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.reference_number')}}</label>
                                <input class="form-control" type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="{{__('page.reference_number')}}" required>
                                @error('reference_number')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.user')}}</label>
                                <select class="form-control select2-show-search" name="user" data-placeholder="{{__('page.user')}}" required>
                                    <option label="{{__('page.user')}}"></option>
                                    @foreach ($users as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('user')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">                        
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.store')}}</label>
                                <select class="form-control select2" name="store" data-placeholder="{{__('page.select_store')}}" required>
                                    <option label="{{__('page.select_store')}}"></option>
                                    @foreach ($stores as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('store')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.customer')}}</label>
                                <select class="form-control select2-show-search" name="customer" data-placeholder="{{__('page.select_customer')}}" required>
                                    <option label="{{__('page.select_customer')}}"></option>
                                    @foreach ($customers as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('supplier')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.attachment')}}:</label>
                                <input type="file" name="attachment" id="file2" class="file-input-styled">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
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
                                            <th>{{__('page.product_price')}}</th>
                                            <th>{{__('page.quantity')}}</th>
                                            <th>{{__('page.product_tax')}}</th>
                                            <th>{{__('page.subtotal')}}</th>
                                            <th style="width:30px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item,i) in order_items" :key="i">
                                            <td>
                                                <input type="hidden" name="product_id[]" class="product_id" :value="item.product_id" />
                                                <input type="text" name="product_name[]" ref="product" class="form-control form-control-sm product" v-model="item.product_name_code" required />
                                            </td>
                                            <td><input type="number" class="form-control form-control-sm" name="price[]" v-model="item.price" placeholder="{{__('page.product_price')}}" /></td>
                                            <td><input type="number" class="form-control form-control-sm  quantity" name="quantity[]" v-model="item.quantity" placeholder="{{__('page.quantity')}}" /></td>
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
                                            <th colspan="2">{{__('page.total')}}</th>
                                            <th class="total_quantity">@{{total.quantity}}</th>
                                            <th class="total_tax"></th>
                                            <th colspan="2" class="total">
                                                @{{total.price | currency}}
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
                                <label class="form-control-label">{{__('page.note')}}:</label>
                                <textarea class="form-control" name="note" rows="5" placeholder="{{__('page.note')}}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer text-right">
                        <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-check mr-2"></i>{{__('page.save')}}</button>
                        <a href="{{route('sale.index')}}" class="btn btn-warning"><i class="fa fa-times mr-2"></i>{{__('page.cancel')}}</a>
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
        $("#sale_date").datetimepicker({
            dateFormat: 'yy-mm-dd',
        });
        $(".expire_date").datepicker({
            dateFormat: 'yy-mm-dd',
        });
        
        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });        
    });
</script>
<script src="{{ asset('js/sale_create.js') }}"></script>
@endsection
