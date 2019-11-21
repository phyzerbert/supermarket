@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-dashboard"></i> {{__('page.overview_chart')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="#">{{__('page.reports')}}</a></li>
                        <li class="active">{{__('page.overview_chart')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="br-pagebody">
                <div class="br-section-wrapper">                
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.company_chart')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-info"><i class="fa fa-pie-chart"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.company_chart')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.store_chart')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-success"><i class="fa fa-bar-chart"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.store_chart')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.product_quantity_alert')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-warning"><i class="fa fa-exclamation-triangle"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.product_quantity_alert')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.product_expiry_alert')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-danger"><i class="fa fa-exclamation-circle"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.product_expiry_alert')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.products_report')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-secondary"><i class="fa fa-cubes"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.product_report')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.categories_report')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-primary"><i class="fa fa-sitemap"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.category_report')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.sales_report')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-pink"><i class="fa fa-sign-out"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.sales_report')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.purchases_report')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-purple"><i class="fa fa-sign-in"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.purchases_report')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.payments_report')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-inverse"><i class="fa fa-money"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.payments_report')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.customers_report')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-info"><i class="fa fa-user"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.customers_report')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.suppliers_report')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-primary"><i class="fa fa-truck"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.suppliers_report')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <a href="{{route('report.users_report')}}">
                                <div class="mini-stat clearfix bx-shadow bg-white">
                                    <span class="mini-stat-icon bg-success"><i class="fa fa-users"></i></span>
                                    <div class="mini-stat-info text-right text-dark pt-2">
                                        <span class="counter text-dark">{{__('page.users_report')}}</span>                                   
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card card-body card-fill">
                    @if($role == 'admin')
                        <div class="row mb-3">
                            <div class="col-md-12">
                                @include('elements.company_filter')
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-12">
                            <canvas id="bar_chart" style="height:400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>                
    </div>

@endsection

@section('script')
<script src="{{asset('master/plugins/Chart.js/Chart.js')}}"></script>
<script>
    var barData = {
        labels: ["{{$return['last_month']['month_name']}}", "{{$return['this_month']['month_name']}}"],
        datasets: [
            {
                label: "Purchase",
                backgroundColor:'#DADDE0',
                data: ["{!!$return['last_month']['purchase'] !!}", "{!!$return['this_month']['purchase'] !!}"]
            },
            {
                label: "Sale",
                backgroundColor: '#2ecc71',
                borderColor: "#fff",
                data: ["{!!$return['last_month']['sale'] !!}", "{!!$return['this_month']['sale'] !!}"]
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
        $("#company_filter").change(function(){
            $("#company_filter_form").submit();
        })
    });
</script>
@endsection
