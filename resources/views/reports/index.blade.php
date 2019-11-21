@extends('layouts.master')

@section('content')
    <div class="br-mainpanel">
        <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="#">{{__('page.report')}}</a>
                <a class="breadcrumb-item active" href="#">{{__('page.overview_chart')}}</a>
            </nav>
        </div>
        <div class="pd-x-20 pd-sm-x-30 pd-t-20 pd-sm-t-30">
            <h4 class="tx-gray-800 mg-b-5"><i class="fa fa-dashboard"></i> {{__('page.overview_chart')}}</h4>
        </div>
        
        @php
            $role = Auth::user()->role->slug;
        @endphp
        <div class="br-pagebody">
            <div class="br-section-wrapper">                
                <div class="row">
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.company_chart')}}" class="d-block p-3 mt-3 bg-primary rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-pie-chart tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.company_chart')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.store_chart')}}" class="d-block p-3 mt-3 bg-secondary rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-bar-chart tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.store_chart')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.product_quantity_alert')}}" class="d-block p-3 mt-3 bg-warning rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-exclamation-triangle tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.product_quantity_alert')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.product_expiry_alert')}}" class="d-block p-3 mt-3 bg-teal rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-exclamation-circle tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.product_expiry_alert')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.products_report')}}" class="d-block p-3 mt-3 bg-indigo rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-cubes tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.product_report')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.categories_report')}}" class="d-block p-3 mt-3 bg-info rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-code-fork tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.category_report')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.sales_report')}}" class="d-block p-3 mt-3 bg-success rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-sign-out tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.sales_report')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.purchases_report')}}" class="d-block p-3 mt-3 bg-danger rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-sign-in tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.purchases_report')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    {{-- <div class="col-sm-6 col-xl-3">
                        <a href="#" class="d-block p-3 mt-3 bg-purple rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-clock-o tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.daily_sales')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="#" class="d-block p-3 mt-3 bg-orange rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-calendar tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.monthly_sales')}}</p>
                                </div>
                            </div>
                        </a>
                    </div> --}}
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.payments_report')}}" class="d-block p-3 mt-3 bg-pink rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-money tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.payments_report')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.customers_report')}}" class="d-block p-3 mt-3 bg-success rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-user tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.customers_report')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.suppliers_report')}}" class="d-block p-3 mt-3 bg-orange rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-truck tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.suppliers_report')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <a href="{{route('report.users_report')}}" class="d-block p-3 mt-3 bg-primary rounded overflow-hidden">
                            <div class="d-flex align-items-center ht-50">
                                <i class="fa fa-users tx-50 lh-0 tx-white op-7"></i>
                                <div class="mg-l-20">
                                    <p class="tx-18 tx-white tx-lato mg-b-2 lh-1">{{__('page.users_report')}}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="br-section-wrapper">
                <div class="row">
                    <div class="card card-body">
                        <canvas id="bar_chart" style="height:400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>                
    </div>

@endsection

@section('script')
<script src="{{asset('master/lib/chart.js/Chart.js')}}"></script>
<script>
    var barData = {
        labels: ["Company Overview"],
        datasets: [
            {
                label: "{{ __('page.purchase) }}",
                backgroundColor:'#DADDE0',
                data: {!! json_encode($data1['purchase']) !!}
            },
            {
                label: "{{ __('page.sale) }}",
                backgroundColor: '#2ecc71',
                borderColor: "#fff",
                data: {!! json_encode($data1['sale']) !!}
            }
        ]
    };
    var barOptions = {
        responsive: true,
        maintainAspectRatio: false,
        tooltips: {
            callbacks: {
                label: function(tooltipItems, data) {
                    let value = parseInt(data.datasets[tooltipItems.datasetIndex].data[tooltipItems.index]).toLocaleString();
                    return data.datasets[tooltipItems.datasetIndex].label + ": " + value;
                }
            }
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: false,
                    callback: function(value, index, values) {
                        return value.toLocaleString();
                    }
                }
            }]
        }
    };

    var ctx = document.getElementById("bar_chart").getContext("2d");
    new Chart(ctx, {type: 'bar', data: barData, options:barOptions}); 
</script>

<script>
    $(document).ready(function () {
        
    });
</script>
@endsection
