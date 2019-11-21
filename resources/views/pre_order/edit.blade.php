@extends('layouts.master')
@section('style')
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/jquery-ui/timepicker/jquery-ui-timepicker-addon.min.css')}}" rel="stylesheet">
    <script src="{{asset('master/plugins/vuejs/vue.js')}}"></script>
    <script src="{{asset('master/plugins/vuejs/axios.js')}}"></script>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-money"></i> {{__('page.purchase_order')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="#">{{__('page.purchase_order')}}</a></li>
                        <li class="active">{{__('page.edit')}}</li>
                    </ol>
                </div>
            </div>
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body p-lg-5" id="page" style="opacity:0">
                <form class="form-layout form-layout-1" action="{{route('pre_order.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="order_id" value="{{$order->id}}">
                    <div class="row mg-b-25">
                        <div class="col-md-6">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.date')}}: </label>
                                <input class="form-control" type="text" name="date" id="purchase_date" value="{{date('Y-m-d H:i', strtotime($order->timestamp))}}" placeholder="{{__('page.date')}}" autocomplete="off" required>
                                @error('date')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.reference_no')}}:</label>
                                <input class="form-control" type="text" name="reference_number" value="{{$order->reference_no}}" placeholder="{{__('page.reference_no')}}">
                                @error('reference_number')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mg-b-25">
                        <div class="col-lg-6">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.supplier')}}:</label>
                                <select class="form-control select2-show-search" name="supplier" id="search_supplier" data-placeholder="{{__('page.supplier')}}">
                                    <option value="">{{__('page.supplier')}}</option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{$item->id}}" @if($order->supplier_id == $item->id) selected @endif>{{$item->company}}</option>
                                    @endforeach
                                </select>
                                @error('supplier')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.attachment')}}:</label>
                                <input type="file" name="attachment" id="file2" class="file-input-styled">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <h4 class="mt-2" style="float:left">{{__('page.order_items')}}</h4>
                                <button type="button" class="btn btn-sm btn-primary btn-icon add-product" title="{{__('page.right_ctrl_key')}}" style="float:right" @click="add_item()"><div><i class="fa fa-plus"></i></div></button>
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
                                            <th width="30"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $orders = $order->orders;
                                        @endphp
                                        <tr v-for="(item,i) in order_items" :key="i">
                                            <td>
                                                <input type="hidden" name="product_id[]" class="product_id" :value="item.product_id" />
                                                <input type="text" name="product_name[]" class="form-control form-control-sm product" v-model="item.product_name_code" required />
                                            </td>
                                            <td><input type="number" class="form-control form-control-sm cost" name="cost[]" v-model="item.cost" placeholder="{{__('page.product_cost')}}" /></td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm discount_string" name="discount_string[]" v-model="item.discount_string" required placeholder="{{__('page.discount')}}" />
                                                <input type="hidden" class="discount" name="discount[]" v-model="item.discount" />
                                            </td>
                                            <td><input type="number" class="form-control input-sm quantity" name="quantity[]" v-model="item.quantity" placeholder="{{__('page.quantity')}}" /></td>
                                            <td class="subtotal">
                                                @{{formatPrice(item.sub_total)}}
                                                <input type="hidden" name="subtotal[]" :value="item.sub_total" />
                                                <input type="hidden" name="item_id[]" :value="item.item_id" />
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning btn-icon remove-product" @click="remove(i)"><i class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">{{__('page.total')}}</th>
                                            <th class="total_discount">@{{formatPrice(total.discount)}}</th>
                                            <th class=""></th>
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
                        <div class="col-lg-12">
                            <div class="form-group mg-b-10-force">
                                <label class="form-control-label">{{__('page.note')}}:</label>
                                <textarea class="form-control" name="note" rows="3" placeholder="{{__('page.note')}}">{{$order->note}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-layout-footer text-right">
                        <button type="submit" class="btn btn-primary mr-3"><i class="fa fa-check mr-2"></i>{{__('page.save')}}</button>
                        <a href="{{route('pre_order.index')}}" class="btn btn-warning"><i class="fa fa-times mr-2"></i>{{__('page.cancel')}}</a>
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
        $(".expire_date").datepicker({
            dateFormat: 'yy-mm-dd',
        });

        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });

        $('#search_supplier').wrap('<div class="position-relative"></div>')
                    .select2({
                        width: 'resolve',
                    });

    });
</script>
<script src="{{ asset('js/pre_order_edit_items.js') }}"></script>
@endsection
