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
        .card-image {
            position: relative;
            width: 55px;
            height: 55px;
            margin-right: 7px;
            cursor: pointer;
            float: left;
        }
        .purchase-image {
            width: 100%;
            height: 100%;
        }
        .btn-delete-image {
            position: absolute;
            color: #444;
            top: 0px;
            right: 5px;
        }
        .custom-uploader {
            width: 100%;
            height: 100%;
            color: #363b4d;
            background: #d5d5d5;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid #c9c9c9;
            border-radius: 5px;
        }
    </style>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-info-circle"></i> {{__('page.edit_purchase')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('purchase.index')}}">{{__('page.purchase')}}</a></li>
                        <li class="active">{{__('page.edit')}}</li>
                    </ol>
                </div>
            </div>
            @php
                $role = Auth::user()->role->slug;
            @endphp
            
            <div class="card card-body p-lg-5" id="page">
                <form class="form-layout form-layout-1" action="{{route('purchase.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$purchase->id}}">  
                    <input type="hidden" name="" data-id="{{$purchase->id}}" data-type="purchase" id="data">  
                    <div class="row mt-3">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.purchase_date')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="date" id="purchase_date" value="{{date('Y-m-d H:i', strtotime($purchase->timestamp))}}" placeholder="Purchase Date" autocomplete="off" required>
                                @error('date')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.reference_no')}}:</label>
                                <input class="form-control" type="text" name="reference_number" value="{{$purchase->reference_no}}" placeholder="{{__('page.reference_no')}}">
                                @error('reference_number')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-group">
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
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
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
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.credit_days')}}:</label>
                                <input type="number" class="form-control" name="credit_days" min="0" value="{{$purchase->credit_days}}">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <h4 class="mt-2">{{__('page.attachment')}}</h4>
                            <div class="clearfix">
                                @foreach ($purchase->images as $image)
                                    @if (file_exists($image->path))
                                        <div class="card-image">
                                            <img src="{{asset($image->path)}}" href="{{asset($image->path)}}" class="purchase-image border rounded" alt="">
                                            <span class="btn-delete-image btn-confirm" href="{{route('purchase.image.delete', $image->id)}}"><i class="fa fa-times-circle-o"></i></span>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="card-image">
                                    <label class="custom-uploader pt-2 pb-1" for="input-custom-uploader">                                                       
                                        <span style="font-size:28px;">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                        <input class="tg-fileinput d-none" type="file" id="input-custom-uploader" name="attachment[]" accept="image/*" multiple />
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div>
                                <h4 class="mt-2" style="float:left">{{__('page.order_items')}}</h4>
                                <a href="#" class="btn btn-primary btn-sm btn-icon add-product" style="float:right" @click="add_item()"><i class="fa fa-plus"></i></a>
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
                                            <th width="30"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $orders = $purchase->orders;
                                        @endphp
                                        <tr v-for="(item,i) in order_items" :key="i">
                                            <td>
                                                <input type="hidden" name="product_id[]" class="product_id" :value="item.product_id" />
                                                <input type="text" name="product_name[]" ref="product" class="form-control form-control-sm product" v-model="item.product_name_code" required />
                                            </td>
                                            <td><input type="date" class="form-control form-control-sm expiry_date" name="expiry_date[]" autocomplete="off" v-model="item.expiry_date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="{{__('page.expiry_date')}}" /></td>
                                            <td><input type="number" class="form-control form-control-sm cost" name="cost[]" v-model="item.cost" placeholder="{{__('page.product_cost')}}" /></td>
                                            <td><input type="number" class="form-control input-sm quantity" name="quantity[]" v-model="item.quantity" placeholder="{{__('page.quantity')}}" /></td>
                                            <td class="tax">@{{item.tax_name}}</td>
                                            <td class="subtotal">
                                                @{{formatPrice(item.sub_total)}}
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
                                            <th colspan="3">{{__('page.total')}}</th>
                                            <th class="total_quantity">@{{total.quantity}}</th>
                                            <th class="total_tax"></th>
                                            <th colspan="2" class="total">@{{formatPrice(total.cost)}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                    
                    <div class="row mb-4">                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.discount')}}</label>
                                <input type="text" name="discount_string" class="form-control" min="0" v-model="discount_string" placeholder="{{__('page.discount')}}">
                                <input type="hidden" name="discount" :value="discount">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.shipping')}}</label>
                                <input type="text" name="shipping_string" class="form-control" min="0" v-model="shipping_string" placeholder="{{__('page.shipping')}}">
                                <input type="hidden" name="shipping" :value="shipping">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.returns')}}</label>
                                <input type="number" name="returns" class="form-control" min="0" v-model="returns" placeholder="{{__('page.returns')}}">
                                <input type="hidden" name="grand_total" :value="grand_total">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-right">{{__('page.purchase')}}: @{{total.cost | currency}} - {{__('page.discount')}}: @{{discount | currency}} - {{__('page.shipping')}}: @{{shipping | currency}} - {{__('page.returns')}}: @{{returns}} = {{__('page.grand_total')}}: @{{grand_total | currency}}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="form-control-label">{{__('page.note')}}:</label>
                                <textarea class="form-control" name="note" rows="3" placeholder="{{__('page.note')}}">{{$purchase->note}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer text-right">
                        <button type="submit" class="btn btn-primary mr-3"><i class="fa fa-check mr-2"></i>{{__('page.save')}}</button>
                        <a href="{{route('purchase.index')}}" class="btn btn-warning"><i class="fa fa-times mr-2"></i>{{__('page.cancel')}}</a>
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
<script src="{{asset('master/plugins/ezview/EZView.js')}}"></script>
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

        if($(".purchase-image").length) {
            $(".purchase-image").EZView();
        }
    });
</script>
<script src="{{ asset('js/purchase_edit.js') }}"></script>
@endsection
