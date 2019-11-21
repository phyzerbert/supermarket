@extends('layouts.master')
@section('style')
    <link href="{{asset('master/lib/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('master/lib/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/lib/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    <script src="{{asset('master/lib/vuejs/vue.js')}}"></script>
    <script src="{{asset('master/lib/vuejs/axios.js')}}"></script>
@endsection
@section('content')
    <div class="br-mainpanel" id="page">
        <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="{{route('home')}}">{{__('page.home')}}</a>
                <a class="breadcrumb-item" href="{{route('received_order.index')}}">{{__('page.received_order')}}</a>
                <a class="breadcrumb-item active" href="#">{{__('page.edit')}}</a>
            </nav>
        </div>
        <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
            <h4 class="tx-gray-800 mg-b-5"><i class="fa fa-edit"></i> {{__('page.edit_purchase')}}</h4>   
            <input type="hidden" name="" data-id="{{$purchase->id}}" data-type="purchase" id="data">         
        </div>

        @php
            $role = Auth::user()->role->slug;
        @endphp
        <div class="br-pagebody">
            <div class="br-section-wrapper">
                <form class="form-layout form-layout-1" action="{{route('received_order.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$purchase->id}}">
                    <div class="row mg-b-25">
                        <div class="col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.purchase_date')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="date" id="purchase_date" value="{{date('Y-m-d H:i', strtotime($purchase->timestamp))}}" placeholder="Purchase Date" autocomplete="off" required>
                                @error('date')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.reference_no')}}:</label>
                                <input class="form-control" type="text" name="reference_number" value="{{$purchase->reference_no}}" placeholder="{{__('page.reference_no')}}">
                                @error('reference_number')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.store')}}:</label>
                                <select class="form-control select2" name="store" data-placeholder="{{__('page.select_store')}}">
                                    <option label="{{__('page.select_store')}}"></option>
                                    @foreach ($stores as $item)
                                        <option value="{{$item->id}}" @if($purchase->store_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('store')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mg-b-25">
                        <div class="col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.supplier')}}:</label>
                                <select class="form-control select2-show-search" name="supplier" data-placeholder="{{__('page.supplier')}}">
                                    <option label="{{__('page.supplier')}}"></option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{$item->id}}" @if($purchase->supplier_id == $item->id) selected @endif>{{$item->company}}</option>
                                    @endforeach
                                </select>
                                @error('supplier')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.attachment')}}:</label>
                                <input type="file" name="attachment" id="file2" class="file-input-styled">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.status')}}:</label>
                                <select class="form-control select2" name="status" data-placeholder="Status">
                                    <option label="{{__('page.status')}}"></option>
                                    <option value="0" @if($purchase->status == 0) selected @endif>{{__('page.pending')}}</option>
                                    <option value="1" @if($purchase->status == 1) selected @endif>{{__('page.received')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.credit_days')}}:</label>
                                <input type="number" class="form-control" name="credit_days" min="0" value="{{$purchase->credit_days}}">
                            </div>
                        </div>
                    </div>

                    <div class="row mg-b-25">
                        <div class="col-md-12">
                            <div>
                                <h5 class="mg-t-10" style="float:left">{{__('page.order_items')}}</h5>
                                <a href="#" class="btn btn-primary btn-icon rounded-circle mg-b-10 add-product" style="float:right" @click="add_item()"><div><i class="fa fa-plus"></i></div></a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-colored table-success" id="product_table">
                                    <thead>
                                        <tr>
                                            <th>{{__('page.product_name_code')}}</th>
                                            <th>{{__('page.expiry_date')}}</th>
                                            <th>{{__('page.product_cost')}}</th>
                                            <th>{{__('page.quantity')}}</th>
                                            <th>{{__('page.product_tax')}}</th>
                                            <th>{{__('page.subtotal')}}</th>
                                            <th class="wd-30"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $orders = $purchase->orders;
                                        @endphp
                                        <tr v-for="(item,i) in order_items" :key="i">
                                            <td>
                                                <select class="form-control input-sm select2 product" name="product_id[]" v-model="item.product_id" @change="get_product(i)">
                                                    <option value="" hidden>{{__('page.select_product')}}</option>
                                                    <option :value="product.id" v-for="(product, i) in products" :key="i">@{{product.name}}(@{{product.code}})</option>
                                                </select>
                                            </td>
                                            <td><input type="date" class="form-control form-control-sm expiry_date" name="expiry_date[]" autocomplete="off" v-model="item.expiry_date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="{{__('page.expiry_date')}}" /></td>
                                            <td><input type="number" class="form-control form-control-sm cost" name="cost[]" v-model="item.cost" placeholder="{{__('page.product_cost')}}" /></td>
                                            <td><input type="number" class="form-control input-sm quantity" name="quantity[]" v-model="item.quantity" placeholder="{{__('page.quantity')}}" /></td>
                                            <td class="tax">@{{item.tax_name}}</td>
                                            <td class="subtotal">
                                                @{{item.sub_total}}
                                                <input type="hidden" name="subtotal[]" :value="item.sub_total" />
                                                <input type="hidden" name="order_id[]" :value="item.order_id" />
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-warning btn-icon rounded-circle mg-t-3 remove-product" @click="remove(i)"><div style="width:25px;height:25px;"><i class="fa fa-times"></i></div></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">{{__('page.total')}}</td>
                                            <td class="total_quantity">@{{total.quantity}}</td>
                                            <td class="total_tax"></td>
                                            <td colspan="2" class="total">@{{total.cost}}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                    
                    <div class="row mg-b-25">                        
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.discount')}}:</label>
                                <input type="text" name="discount_string" class="form-control" v-model="discount_string" placeholder="{{__('page.discount')}}">
                                <input type="hidden" name="discount" :value="discount">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.shipping')}}:</label>
                                <input type="text" name="shipping_string" class="form-control" v-model="shipping_string" placeholder="{{__('page.shipping')}}">
                                <input type="hidden" name="shipping" :value="shipping">
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.returns')}}:</label>
                                <input type="number" name="returns" class="form-control" min="0" v-model="returns" placeholder="{{__('page.returns')}}">
                                <input type="hidden" name="grand_total" :value="grand_total">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-right">Purchase: @{{total.cost}} - Discount: @{{discount}} - Shipping: @{{shipping}} - Returns: @{{returns}} = Grand Total: @{{grand_total}}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.note')}}:</label>
                                <textarea class="form-control" name="note" rows="5" placeholder="{{__('page.note')}}">{{$purchase->note}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check mg-r-2"></i>{{__('page.save')}}</button>
                        <a href="{{route('purchase.index')}}" class="btn btn-warning"><i class="fa fa-times mg-r-2"></i>{{__('page.cancel')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('master/lib/select2/js/select2.min.js')}}"></script>
<script src="{{asset('master/lib/jquery-ui/jquery-ui.js')}}"></script>
<script src="{{asset('master/lib/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script>
<script src="{{asset('master/lib/styling/uniform.min.js')}}"></script>
<script>
    $(document).ready(function () {

        $("#purchase_date").datetimepicker({
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
<script src="{{ asset('js/purchase_edit_order_items.js') }}"></script>
@endsection
