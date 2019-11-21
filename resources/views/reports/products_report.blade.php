@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-cubes"></i> {{__('page.product_report')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('report.overview_chart')}}">{{__('page.reports')}}</a></li>
                        <li class="active">{{__('page.product_report')}}</li>
                    </ol>
                </div>
            </div> 
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="">
                    @include('elements.pagesize')                    
                    <form action="" method="POST" class="form-inline float-left" id="searchForm">
                        @csrf
                        @if($role == 'admin')
                            <select class="form-control form-control-sm mr-sm-2 mb-2" name="company_id" id="search_company">
                                <option value="" hidden>{{__('page.select_company')}}</option>
                                @foreach ($companies as $item)
                                    <option value="{{$item->id}}" @if ($company_id == $item->id) selected @endif>{{$item->name}}</option>
                                @endforeach
                            </select>
                        @endif
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="product_code" id="search_code" value="{{$product_code}}" placeholder="{{__('page.product_code')}}">
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="product_name" id="search_name" value="{{$product_name}}" placeholder="{{__('page.product_name')}}">

                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
                        <button type="button" class="btn btn-sm btn-danger mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
                    </form>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th width="60">{{__('page.image')}}</th>
                                <th>{{__('page.product_code')}}</th>
                                <th>{{__('page.product_name')}}</th>
                                <th>{{__('page.purchased')}}</th>
                                <th>{{__('page.sold')}}</th>
                                <th>{{__('page.purchased_amount')}}</th>
                                <th>{{__('page.sold_amount')}}</th>
                                <th>{{__('page.profit_loss')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $footer_purchased_quantity = $footer_sold_quantity = $footer_purchased_amount = $footer_sold_amount = 0;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    $mod_purchased_quantity = \App\Models\Order::where('product_id', $item->id)->where('orderable_type', "App\Models\Purchase");
                                    $mod_sold_quantity = \App\Models\Order::where('product_id', $item->id)->where('orderable_type', "App\Models\Sale");                                    
                                    $mod_purchased_amount = \App\Models\Order::where('product_id', $item->id)->where('orderable_type', "App\Models\Purchase");
                                    $mod_sold_amount = \App\Models\Order::where('product_id', $item->id)->where('orderable_type', "App\Models\Sale");

                                    if($company_id != ''){
                                        $company = \App\Models\Company::find($company_id);
                                        $company_purchases = $company->purchases()->pluck('id');
                                        $company_sales = $company->sales()->pluck('id');

                                        $purchased_quantity = $mod_purchased_quantity->whereIn('orderable_id', $company_purchases);
                                        $sold_quantity = $mod_sold_quantity->whereIn('orderable_id', $company_sales);                                    
                                        $purchased_amount = $mod_purchased_amount->whereIn('orderable_id', $company_purchases);
                                        $sold_amount = $mod_sold_amount->whereIn('orderable_id', $company_sales);
                                    }


                                    $purchased_quantity = $mod_purchased_quantity->sum('quantity');
                                    $sold_quantity = $mod_sold_quantity->sum('quantity');                                    
                                    $purchased_amount = $mod_purchased_amount->sum('subtotal');
                                    $sold_amount = $mod_sold_amount->sum('subtotal');

                                    $footer_purchased_quantity += $purchased_quantity;
                                    $footer_sold_quantity += $sold_quantity;
                                    $footer_purchased_amount += $purchased_amount;
                                    $footer_sold_amount += $sold_amount;
                                @endphp                              
                                <tr>
                                    <td class="wd-40">{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td class="py-1" width="50">
                                        @php
                                            $image_path = asset('images/no-image.png');
                                            if(file_exists($item->image)){
                                                $image_path = asset($item->image);
                                            }
                                        @endphp
                                        <img class="bordered rounded-circle attachment" height="40" width="40" src="{{$image_path}}" alt="">
                                    </td>
                                    <td>{{$item->code}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{number_format($purchased_quantity)}}</td>
                                    <td>{{number_format($sold_quantity)}}</td>                                        
                                    <td>{{number_format($purchased_amount)}}</td>
                                    <td>{{number_format($sold_amount)}}</td>                                      
                                    <td>{{number_format($sold_amount - $purchased_amount)}}</td>                                      
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">{{__('page.total')}}</th>
                                <th>{{number_format($footer_purchased_quantity)}}</th>
                                <th>{{number_format($footer_sold_quantity)}}</th>
                                <th>{{number_format($footer_purchased_amount)}}</th>
                                <th>{{number_format($footer_sold_amount)}}</th>
                                <th>{{number_format($footer_sold_amount - $footer_purchased_amount)}}</th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends([
                                'company_id' => $company_id,
                                'product_code' => $product_code,
                                'product_name' => $product_name,
                            ])->links() !!}
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
        $("#pagesize").change(function(){
            $("#pagesize_form").submit();
        });
        $("#btn-reset").click(function(){
            $("#search_name").val('');
            $("#search_code").val('');
            $("#search_company").val('');
        });
    });
</script>
@endsection
