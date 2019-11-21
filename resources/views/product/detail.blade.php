@extends('layouts.master')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-info-circle"></i> {{__('page.product_detail')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('product.index')}}">{{__('page.product')}}</a></li>
                        <li class="active">{{__('page.details')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="@if($product->image){{asset($product->image)}}@else {{asset('images/no-image.png')}} @endif" class="border" width="100%" alt="">
                        <br><br>
                        <h5>{{__('page.note')}}</h5>
                        <p class="tx-black">
                            {{$product->detail}}                 
                        </p>
                    </div>
                    <div class="col-md-8">
                        <h4>{{__('page.product_detail')}}</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr class="bd-0-force">
                                        <td style="width:40%;text-align:right;font-weight:600">{{__('page.product_name')}} :</td>
                                        <td class="tx-bold tx-black">{{$product->name}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;font-weight:600">{{__('page.product_code')}} :</td>
                                        <td class="tx-bold tx-black">{{$product->code}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;font-weight:600">{{__('page.category')}} :</td>
                                        <td class="tx-bold tx-black">@isset($product->category->name){{$product->category->name}}@endisset</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;font-weight:600">{{__('page.product_unit')}} :</td>
                                        <td class="tx-bold tx-black">{{$product->unit}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;font-weight:600">{{__('page.product_cost')}} :</td>
                                        <td class="tx-bold tx-black">{{$product->cost}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;font-weight:600">{{__('page.product_price')}} :</td>
                                        <td class="tx-bold tx-black">{{$product->price}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;font-weight:600">{{__('page.tax_rate')}} :</td>
                                        <td class="tx-bold tx-black">@isset($product->tax->name){{$product->tax->name}}@endisset</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;font-weight:600">{{__('page.tax_method')}} :</td>
                                        <td class="tx-bold tx-black">
                                            @if ($product->tax_method == 0)
                                                Inclusive
                                            @elseif($product->tax_method == 1)
                                                Exclusive
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;font-weight:600">{{__('page.alert_quantity')}} :</td>
                                        <td class="tx-bold tx-black">{{$product->alert_quantity}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;font-weight:600">{{__('page.supplier')}} :</td>
                                        <td class="tx-bold tx-black">@isset($product->supplier->name){{$product->supplier->name}}@endisset</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <a href="{{route('product.index')}}" class="btn btn-primary" style="float:right">
                            <div class="ht-40">
                                <span class="icon wd-40"><i class="fa fa-undo"></i></span>
                                <span class="pd-x-15 tx-center">{{__('page.back')}}</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>                
    </div>
@endsection