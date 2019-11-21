@extends('layouts.master')
@section('style')
    <link href="{{asset('master/plugins/select2/dist/css/select2.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/select2/dist/css/select2-bootstrap.css')}}" rel="stylesheet">
    <script src="{{asset('master/plugins/vuejs/vue.js')}}"></script>
    <script src="{{asset('master/plugins/vuejs/axios.js')}}"></script>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-credit-card"></i> {{__('page.purchase_order')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('pre_order.index')}}">{{__('page.purchase_order')}}</a></li>
                        <li class="active">{{__('page.receive')}}</li>
                    </ol>
                </div> 
            </div>     
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card" id="page">
                <div class="card-body table-responsive">
                    <div class="pd-t-20 py-3">
                        <h3 class="tx-gray-800 mb-3 float-left"><i class="fa fa-info-circle"></i> {{__('page.receive')}}</h3>
                        <input type="text" class="form-control form-control-sm float-right" style="width:300px;" name="" id="" v-model="keyword" placeholder="Product Name" @keyup="searchProduct">
                    </div>                        
                    <form action="{{route('pre_order.save_receive')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$order->id}}" id="order_id" />
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('page.product_code')}}</th>
                                    <th>{{__('page.product_name')}}</th>
                                    <th>{{__('page.product_cost')}}</th>
                                    <th>{{__('page.discount')}}</th>
                                    <th>{{__('page.ordered_quantity')}}</th>
                                    <th>{{__('page.received_quantity')}}</th>
                                    <th>{{__('page.balance')}}</th>
                                    <th>{{__('page.receive')}}</th>
                                    <th>{{__('page.subtotal')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_discount = 0;
                                    $total_amount = 0;
                                @endphp
                                    <tr v-for="(item,i) in filtered_items" :key="i">
                                        <td class="text-center" width="40">
                                            <div class="checkbox checkbox-primary pl-4" style="margin-top:-5px;">
                                                <input :id="item.item_id" type="checkbox" :name="'item[' + item.item_id +']'" :value="item.item_id" v-model="checked_items">
                                                <label :for="item.item_id"></label>
                                            </div>
                                        </td>
                                        <td>@{{item.product_code}}</td>
                                        <td>@{{item.product_name}}</td>
                                        <td>@{{formatPrice(item.cost - item.discount)}}</td>
                                        <td>@{{item.discount_string}}</td>
                                        <td>@{{item.ordered_quantity}}</td>
                                        <td>@{{item.received_quantity}}</td>
                                        <td>@{{item.balance}}</td>
                                        <td class="py-2"><input type="number" class="form-control form-control-sm" :name="'receive_quantity[' + item.item_id + ']'" v-model="item.receive_quantity" min="0" :max="item.balance"></td>
                                        <td>
                                            @{{formatPrice(item.sub_total)}}
                                            <input type="hidden" :name="'subtotal[' + item.item_id + ']'" :value="item.sub_total" />
                                        </td>
                                    </tr>
                            </tbody>
                            <tfoot class="tx-bold tx-black">
                                <tr>
                                    <th colspan="4" class="tx-bold">{{__('page.total')}} </th>
                                    <th>@{{formatPrice(total.discount)}}</th>
                                    <th colspan="4"></th>
                                    <th>
                                        @{{formatPrice(total.cost)}}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="row mt-4">
                            <div class="col-lg-4 col-md-6">
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
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group mb-2">
                                    <label class="form-control-label">{{__('page.store')}}:</label>
                                    <select class="form-control select2" name="store" data-placeholder="{{__('page.select_store')}}">
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
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="purchase_image">{{__('page.attachment')}}</label>
                                    <input type="file" name="attachment" class="form-control file-input-styled" id="purchase_image" />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">                                                  
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
                        <div class="row mt-3">                            
                            <div class="col-12 text-right">
                                <a href="{{route('pre_order.index')}}" class="btn btn-success"><i class="menu-item-icon icon ion-clipboard tx-16"></i>  {{__('page.purchase_order')}}</a>
                                <button type="submit" class="btn btn-primary ml-3"><i class="menu-item-icon icon ion-archive tx-16"></i>  {{__('page.receive')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>                
    </div>

@endsection

@section('script')
<script src="{{asset('master/plugins/select2/dist/js/select2.min.js')}}"></script>
<script src="{{asset('master/plugins/styling/uniform.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });
    });
</script>
<script src="{{ asset('js/pre_order_receive.js') }}"></script>
@endsection
