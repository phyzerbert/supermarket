@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-user"></i> {{__('page.customers_report')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('report.overview_chart')}}">{{__('page.reports')}}</a></li>
                        <li class="active">{{__('page.customers_report')}}</li>
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
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="name" id="search_name" value="{{$name}}" placeholder="{{__('page.name')}}">
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="customer_company" id="search_customer_company" value="{{$customer_company}}" placeholder="{{__('page.customer_company')}}">
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="phone_number" id="search_phone" value="{{$phone_number}}" placeholder="{{__('page.phone_number')}}">
                        
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
                        <button type="button" class="btn btn-sm btn-info mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
                    </form>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>{{__('page.customer_company')}}</th>
                                <th>{{__('page.name')}}</th>
                                <th>{{__('page.phone_number')}}</th>
                                <th>{{__('page.email_address')}}</th>
                                <th>{{__('page.total_sales')}}</th>
                                <th>{{__('page.total_amount')}}</th>
                                <th>{{__('page.paid')}}</th>
                                <th>{{__('page.balance')}}</th>
                                <th style="width:120px">{{__('page.action')}}</th>
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
                                    <td class="text-center py-2">                                        
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('page.action')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{route('report.customers_report.sales', $item->id)}}" class="dropdown-item">{{__('page.view_reports')}}</a></li>
                                                <li><a href="{{route('customer.report', $item->id)}}" class="dropdown-item">{{__('page.report')}}</a></li>
                                                <li><a href="{{route('customer.export', $item->id)}}" class="dropdown-item">{{__('page.export')}}</a></li>
                                                <li><a href="{{route('customer.email', $item->id)}}" class="dropdown-item">{{__('page.email')}}</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">{{__('page.total')}}</th>
                                <th>{{number_format($footer_total_sales)}}</th>
                                <th>{{number_format($footer_total_amount)}}</th>
                                <th>{{number_format($footer_paid)}}</th>
                                <th>{{number_format($footer_total_amount - $footer_paid)}}</th>
                                <th></th>
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
                                'name' => $name,
                                'customer_company' => $customer_company,
                                'phone_number' => $phone_number,
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
        $("#btn-reset").click(function(){
            $("#search_name").val('');
            $("#search_company").val('');
            $("#search_customer_company").val('');
            $("#search_phone").val('');
        });
    });
</script>
@endsection
