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
                    <h3 class="pull-left page-title"><i class="fa fa-edit"></i> {{__('page.edit_sale')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('sale.index')}}">{{__('page.sales')}}</a></li>
                        <li class="active">{{__('page.edit')}}</li>
                    </ol>
                </div>
            </div>
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body p-md-5" id="page">
                <form class="form-layout form-layout-1" action="{{route('sale.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$sale->id}}" id="sale_id">
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.sale_date')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="date" id="sale_date" value="{{date('Y-m-d H:i', strtotime($sale->timestamp))}}" placeholder="Sale Date" autocomplete="off" required>
                                @error('date')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.reference_number')}}:</label>
                                <input class="form-control" type="text" name="reference_number" value="{{$sale->reference_no}}" placeholder="{{__('page.reference_number')}}">
                                @error('reference_number')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>                        
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.user')}}:</label>
                                <select class="form-control select2-show-search" name="user" data-placeholder="{{__('page.user')}}">
                                    <option label="{{__('page.user')}}"></option>
                                    @foreach ($users as $item)
                                        <option value="{{$item->id}}" @if($sale->biller_id == $item->id) selected @endif>{{$item->name}}</option>
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
                    <div class="row mg-b-25">                        
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.store')}}:</label>
                                <select class="form-control select2" name="store" data-placeholder="Select Store">
                                    <option label="Select Store"></option>
                                    @foreach ($stores as $item)
                                        <option value="{{$item->id}}" @if($sale->store_id == $item->id) selected @endif>{{$item->name}}</option>
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
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.customer')}}:</label>
                                <select class="form-control select2-show-search" name="customer" data-placeholder="{{__('page.customer')}}">
                                    <option label="Customer"></option>
                                    @foreach ($customers as $item)
                                        <option value="{{$item->id}}" @if($sale->customer_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('customer')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.attachment')}}:</label>
                                <input type="file" name="attachment" id="file2" class="file-input-styled">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div>
                                <h5 class="mg-t-10" style="float:left">{{__('page.order_items')}}</h5>
                                <a href="#" class="btn btn-primary btn-icon mg-b-10 add-product" style="float:right" @click="add_item()"><div><i class="fa fa-plus"></i></div></a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered mt-2" id="product_table">
                                    <thead class="table-success">
                                        <tr>
                                            <th>{{__('page.product_name_code')}}</th>
                                            <th>{{__('page.product_price')}}</th>
                                            <th>{{__('page.quantity')}}</th>
                                            <th>{{__('page.product_tax')}}</th>
                                            <th>{{__('page.subtotal')}}</th>
                                            <th width="30"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $orders = $sale->orders;
                                        @endphp
                                        <tr v-for="(item,i) in order_items" :key="i">
                                            <td>
                                                <input type="hidden" name="product_id[]" class="product_id" :value="item.product_id" />
                                                <input type="text" name="product_name[]" ref="product" class="form-control form-control-sm product" v-model="item.product_name_code" required />
                                            </td>
                                            <td><input type="number" class="form-control form-control-sm" name="price[]" v-model="item.price" placeholder="{{__('page.product_price')}}" /></td>
                                            <td><input type="number" class="form-control input-sm quantity" name="quantity[]" v-model="item.quantity" placeholder="Quantity" /></td>
                                            <td class="tax">@{{item.tax_name}}</td>
                                            <td class="subtotal">
                                                @{{item.sub_total | currency}}
                                                <input type="hidden" name="subtotal[]" :value="item.sub_total" />
                                                <input type="hidden" name="order_id[]" :value="item.order_id" />
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
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.note')}}:</label>
                                <textarea class="form-control" name="note" rows="3" placeholder="{{__('page.note')}}">{{$sale->note}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check mg-r-2"></i>{{__('page.save')}}</button>
                        <a href="{{route('sale.index')}}" class="btn btn-warning"><i class="fa fa-times mg-r-2"></i>{{__('page.cancel')}}</a>
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
<script src="{{ asset('js/sale_edit.js') }}"></script>
@endsection
