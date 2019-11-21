@extends('layouts.master')

@section('content')
    <div class="br-mainpanel">
        <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="{{route('home')}}">{{__('page.reports')}}</a>
                <a class="breadcrumb-item active" href="#">{{__('page.customers_report')}}</a>
            </nav>
        </div>
        <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
            <h4 class="tx-gray-800 mg-b-5"><i class="fa fa-user-circle-o"></i> Customers Report</h4>
        </div>
        
        @php
            $role = Auth::user()->role->slug;
        @endphp
        <div class="br-pagebody">
            <div class="br-section-wrapper">
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
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="name" id="search_name" value="{{$name}}" placeholder="{{__('page.name')}}">
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="customer_company" id="search_customer_company" value="{{$customer_company}}" placeholder="{{__('page.customer_company')}}">
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="phone_number" id="search_phone" value="{{$phone_number}}" placeholder="{{__('page.phone_number')}}">
                        
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
                        <button type="button" class="btn btn-sm btn-danger mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
                    </form>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-colored table-primary table-hover">
                        <thead class="thead-colored thead-primary">
                            <tr class="bg-blue">
                                <th class="wd-40">#</th>
                                <th>{{__('page.customer_company')}}</th>
                                <th>{{__('page.name')}}</th>
                                <th>{{__('page.phone_number')}}</th>
                                <th>{{__('page.email_address')}}</th>
                                <th>{{__('page.total_sales')}}</th>
                                <th>{{__('page.total_amount')}}</th>
                                <th>{{__('page.paid')}}</th>
                                <th>{{__('page.balance')}}</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $footer_total_sales = $footer_total_amount = $footer_paid = 0;
                            @endphp
                            @foreach ($data as $item)
                                @php
                                    $sales_array = $item->sales()->pluck('id');
                                    $total_sales = $item->sales()->count();
                                    
                                    $mod_total_amount = \App\Models\Order::whereIn('orderable_id', $sales_array)->where('orderable_type', "App\Models\Sale");
                                    $mod_paid = \App\Models\Payment::whereIn('paymentable_id', $sales_array)->where('paymentable_type', "App\Models\Sale");

                                    if($company_id != ''){
                                        $company = \App\Models\Company::find($company_id);
                                        $company_sales = $company->sales()->pluck('id');

                                        $mod_total_amount = $mod_total_amount->whereIn('orderable_id', $company_sales);
                                        $mod_paid = $mod_paid->whereIn('paymentable_id', $company_sales);
                                    }

                                    $total_amount = $mod_total_amount->sum('subtotal');
                                    $paid = $mod_paid->sum('amount'); 

                                    $footer_total_sales += $total_sales;
                                    $footer_total_amount += $total_amount;
                                    $footer_paid += $paid;

                                @endphp                              
                                <tr>
                                    <td class="wd-40">{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td>{{$item->company}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->phone_number}}</td>
                                    <td>{{$item->email}}</td>
                                    <td>{{number_format($total_sales)}}</td>
                                    <td>{{number_format($total_amount)}}</td>                                        
                                    <td>{{number_format($paid)}}</td>
                                    <td>{{number_format($total_amount - $paid)}}</td>                                      
                                    {{-- <td></td>--}}
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">{{__('page.total')}}</td>
                                <td>{{number_format($footer_total_sales)}}</td>
                                <td>{{number_format($footer_total_amount)}}</td>
                                <td>{{number_format($footer_paid)}}</td>
                                <td>{{number_format($footer_total_amount - $footer_paid)}}</td>
                            </tr>
                        </tfoot>
                    </table>                
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends([])->links() !!}
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
            $("#search_name").val('');
            $("#search_company").val('');
            $("#search_customer_company").val('');
            $("#search_phone").val('');
        });
    });
</script>
@endsection
