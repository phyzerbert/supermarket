@extends('layouts.master')
@section('style')
    {{-- <link href="{{asset('master/plugins/datatables/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{asset('master/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet"> --}}
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-truck"></i> {{__('page.suppliers_report')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('report.overview_chart')}}">{{__('page.reports')}}</a></li>
                        <li class="active">{{__('page.suppliers_report')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            
            <div class="card card-body">
                <div class="">
                    @include('elements.pagesize')
                    <form action="" method="POST" class="form-inline float-left" id="searchForm">
                        @csrf
                        @if ($role == 'admin')    
                            <select class="form-control form-control-sm mr-sm-2 mb-2" name="company_id" id="search_company">
                                <option value="" hidden>{{__('page.select_company')}}</option>
                                @foreach ($companies as $item)
                                    <option value="{{$item->id}}" @if ($company_id == $item->id) selected @endif>{{$item->name}}</option>
                                @endforeach        
                            </select>
                        @endif
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="name" id="search_name" value="{{$name}}" placeholder="{{__('page.name')}}">
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="company" id="search_company" value="{{$company}}" placeholder="{{__('page.company')}}">
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="phone_number" id="search_phone" value="{{$phone_number}}" placeholder="{{__('page.phone_number')}}">
                        
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
                        <button type="button" class="btn btn-sm btn-info mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
                    </form>
                    <button type="button" class="btn btn-success btn-sm float-right mg-b-5" id="btn-add"><i class="icon ion-person-add mg-r-2"></i> Add New</button>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover" id="supplier_table">
                        <thead>
                            <tr>
                                <th class="wd-40">#</th>
                                <th>{{__('page.company')}}</th>
                                <th>{{__('page.name')}}</th>
                                <th>{{__('page.phone')}}</th>
                                <th>{{__('page.email_address')}}</th>
                                <th style="width:120px;">{{__('page.total_purchases')}}</th>
                                <th style="width:120px !important;">{{__('page.total_amount')}}</th>
                                <th>{{__('page.paid')}}</th>
                                <th>{{__('page.balance')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @php
                                $footer_total_purchases = $footer_total_amount = $footer_paid = 0;
                            @endphp                               
                            @foreach ($data as $item)
                                @php
                                    $purchases_array = $item->purchases()->pluck('id');
                                    $total_purchases = $item->purchases()->count();
                                    $mod_total_amount = $item->purchases();
                                    $mod_paid = \App\Models\Payment::whereIn('paymentable_id', $purchases_array)->where('status', 1)->where('paymentable_type', "App\Models\Purchase");
                                    $mod_preturn = \App\Models\Preturn::whereIn('purchase_id', $purchases_array)->where('status', 1);

                                    if($company_id != ''){
                                        $company = \App\Models\Company::find($company_id);
                                        $company_purchases = $company->purchases()->pluck('id');

                                        $mod_total_amount = $mod_total_amount->where('company_id', $company_id);
                                        $mod_paid = $mod_paid->whereIn('paymentable_id', $company_purchases);
                                        $mod_preturn = $mod_preturn->whereIn('purchase_id', $company_purchases);
                                    }

                                    $paid = $mod_paid->sum('amount');  
                                    $preturn = $mod_preturn->sum('amount');
                                    $total_amount = $mod_total_amount->sum('grand_total');  
                                    $total_amount = $total_amount - $preturn;

                                    $footer_total_purchases += $total_purchases;
                                    $footer_total_amount += $total_amount;
                                    $footer_paid += $paid;
                                @endphp                              
                                <tr>
                                    <td class="wd-40">{{ $loop->index + 1 }}</td>
                                    <td>{{$item->company}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->phone_number}}</td>
                                    <td>{{$item->email}}</td>
                                    <td style="width:120px !important;">{{number_format($total_purchases)}}</td>
                                    <td style="width:120px !important;">{{number_format($total_amount)}}</td>                                        
                                    <td>{{number_format($paid)}}</td>
                                    <td>{{number_format($total_amount - $paid)}}</td>                                      
                                    <td class="text-center py-2">                                        
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{__('page.action')}}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{route('report.suppliers_report.purchases', $item->id)}}" class="dropdown-item">{{__('page.view_reports')}}</a></li>
                                                <li><a href="{{route('supplier.report', $item->id)}}" class="dropdown-item">{{__('page.report')}}</a></li>
                                                <li><a href="{{route('supplier.export', $item->id)}}" class="dropdown-item">{{__('page.export')}}</a></li>
                                                <li><a href="{{route('supplier.email', $item->id)}}" class="dropdown-item">{{__('page.email')}}</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">{{__('page.total')}}</th>
                                <th>{{number_format($footer_total_purchases)}}</th>
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
                            {!! $data->appends(['name' => $name, 'company' => $company, 'phone_number' => $phone_number])->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>                
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('master/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script>
    $(document).ready(function () {
        // $('#supplier_table').DataTable({
        //     responsive: true,
        //     lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        //     language: {
        //         searchPlaceholder: 'Search...',
        //         sSearch: '',
        //         lengthMenu: '_MENU_ Items/Page',
        //     }
        // });
        // $(".dataTables_length select").addClass("form-control-sm");
        $("#btn-reset").click(function(){
            $("#search_name").val('');
            $("#search_company").val('');
            $("#search_supplier_company").val('');
            $("#search_phone").val('');
        });

    });
    $("#pagesize").change(function(){
        $("#pagesize_form").submit();
    });
</script>
@endsection
