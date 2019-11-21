@extends('layouts.master')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-cubes"></i> {{__('page.product_management')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="#">{{__('page.product')}}</a></li>
                        <li class="active">{{__('page.list')}}</li>
                    </ol>
                </div>
            </div>
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body">
                <div class="">
                    @include('elements.pagesize')
                    @include('product.filter')
                    <a href="{{route('product.create')}}" class="btn btn-success btn-sm float-right tx-white mg-b-5" id="btn-add"><i class="fa fa-plus mg-r-2"></i> Add New</a>                    
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead class="">
                            <tr class="bg-blue">
                                <th style="width:30px;">#</th>
                                <th style="width:50px"></th>
                                <th>{{__('page.product_code')}}</th>
                                <th>{{__('page.product_name')}}</th>
                                <th>{{__('page.category')}}</th>
                                <th>{{__('page.currency')}}</th>
                                <th>{{__('page.product_cost')}}</th>
                                <th>{{__('page.product_price')}}</th>
                                <th>{{__('page.quantity')}}</th>
                                <th>{{__('page.product_unit')}}</th>
                                <th>{{__('page.alert_quantity')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @forelse ($data as $item)
                                @php
                                    $quantity = $item->store_products()->sum('quantity');
                                @endphp
                                <tr>
                                    <td>{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="py-1" width="50">
                                        @php
                                            $image_path = asset('images/no-image.png');
                                            if(file_exists($item->image)){
                                                $image_path = asset($item->image);
                                            }
                                        @endphp
                                        <img class="bordered rounded-circle attachment" height="40" width="40" src="{{$image_path}}" alt="">
                                    </td>
                                    <td class="code">{{$item->code}}</td>
                                    <td class="name">{{$item->name}}</td>
                                    <td class="category">{{$item->category->name ?? ''}}</td>
                                    <td class="currency">{{$item->currency->name ?? ''}}</td>
                                    <td class="cost">{{number_format($item->cost, 2)}}</td>
                                    <td class="">{{number_format($item->price, 2)}}</td>
                                    <td class="quantity">{{$quantity}}</td>
                                    <td class="unit">{{$item->unit}}</td>
                                    <td class="alert_quantity">{{$item->alert_quantity}}</td>
                                    <td class="py-2" align="center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('page.action')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{route('product.detail', $item->id)}}" class="dropdown-item">{{__('page.details')}}</a></li>
                                                <li><a href="{{route('product.edit', $item->id)}}" class="dropdown-item">{{__('page.edit')}}</a></li>
                                                <li><a href="{{route('product.delete', $item->id)}}" class="dropdown-item btn-confirm">{{__('page.delete')}}</a></li>
                                            </ul>
                                        </div>                                       
                                    </td>
                                </tr>                            
                            @empty
                                <tr>
                                    <td colspan="15" align="center">{{__('page.no_data')}}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>                
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends(['name' => $name, 'code' => $code, 'category_id' => $category_id])->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>                
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        $("#btn-reset").click(function(){
            $("#search_code").val('');
            $("#search_name").val('');
            $("#search_category").val('');
        });
        $(".attachment").click(function(e){
            e.preventDefault();
            let path = $(this).attr('src');
            $("#image_preview").html('')
            $("#image_preview").verySimpleImageViewer({
                imageSource: path,
                frame: ['100%', '100%'],
                maxZoom: '900%',
                zoomFactor: '10%',
                mouse: true,
                keyboard: true,
                toolbar: true,
            });
            $("#attachModal").modal();
        });
    });
</script>
@endsection
