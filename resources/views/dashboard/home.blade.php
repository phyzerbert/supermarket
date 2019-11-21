@extends('layouts.master')
@section('style')
    <link href="{{asset('master/plugins/daterangepicker/daterangepicker.min.css')}}" rel="stylesheet">    
@endsection
@section('content')
    @php
        $role = Auth::user()->role->slug;
    @endphp
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><i class="fa fa-dashboard"></i> {{__('page.dashboard')}}</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.dashboard')}}</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if ($role == 'admin')
                        @include('dashboard.top_filter')
                    @endif                    
                </div>                
            </div>
            <div class="row mt-3">
                <div class="col-md-6 col-xl-3">
                    <div class="mini-stat clearfix bx-shadow bg-white">
                        <span class="mini-stat-icon bg-info"><i class="fa fa-sign-in"></i></span>
                        <div class="mini-stat-info text-right text-dark">
                            <span class="counter text-dark">{{number_format($return['today_purchases']['total'])}}</span>
                            {{__('page.today_purchases')}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="mini-stat clearfix bx-shadow bg-white">
                        <span class="mini-stat-icon bg-primary"><i class="fa fa-truck"></i></span>
                        <div class="mini-stat-info text-right text-dark">
                            <span class="counter text-dark">{{number_format($return['week_purchases']['total'])}}</span>
                            {{__('page.week_purchases')}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="mini-stat clearfix bx-shadow bg-white">
                        <span class="mini-stat-icon bg-pink"><i class="fa fa-usd"></i></span>
                        <div class="mini-stat-info text-right text-dark">
                            <span class="counter text-dark">{{number_format($return['month_purchases']['total'])}}</span>
                            {{__('page.month_purchases')}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="mini-stat clearfix bx-shadow bg-white">
                        <span class="mini-stat-icon bg-success"><i class="fa fa-usd"></i></span>
                        <div class="mini-stat-info text-right text-dark">
                            <span class="counter text-dark">{{number_format($return['company_grand_total'] - $return['overall_purchases']['total_paid'])}}</span>
                            {{__('page.company_balance')}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6 col-xl-3">
                    <div class="mini-stat clearfix bx-shadow bg-white">
                        <span class="mini-stat-icon bg-info"><i class="fa fa-sign-out"></i></span>
                        <div class="mini-stat-info text-right text-dark">
                            <span class="counter text-dark">{{number_format($return['today_sales']['total'])}}</span>
                            {{__('page.today_sales')}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="mini-stat clearfix bx-shadow bg-white">
                        <span class="mini-stat-icon bg-primary"><i class="fa fa-shopping-cart"></i></span>
                        <div class="mini-stat-info text-right text-dark">
                            <span class="counter text-dark">{{number_format($return['week_sales']['total'])}}</span>
                            {{__('page.week_sales')}}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a href="{{route('report.expired_purchases_report').'?company_id='.$top_company.'&expiry_period='.$expiry_date}}">
                        <div class="mini-stat clearfix bx-shadow bg-white">
                            <span class="mini-stat-icon bg-warning"><i class="fa fa-exclamation-triangle"></i></span>
                            <div class="mini-stat-info text-right text-dark">
                                <span class="counter text-dark">{{$return['expired_in_5days_purchases']}}</span>
                                {{__('page.expiries_in_5days')}}
                            </div>
                        </div>
                    </a>
                </div>                     
                <div class="col-md-6 col-xl-3" id="expire_alert">
                    <div class="mini-stat clearfix bx-shadow bg-white">
                        <span class="mini-stat-icon bg-danger"><i class="fa fa- fa-exclamation-circle"></i></span>
                        <div class="mini-stat-info text-right text-dark">
                            <span class="counter text-dark">{{number_format($return['expired_purchases'])}}</span>
                            {{__('page.expired_purchases')}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="br-section-wrapper mt-3">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <h4 class="tx-primary float-left">{{__('page.overview')}}</h4>
                        <form action="" class="form-inline float-right" method="post">
                            @csrf
                            <input type="hidden" name="top_company" value="{{$top_company}}" />
                            <input type="text" class="form-control" name="period" id="period" style="width:220px !important" value="{{$period}}" autocomplete="off" placeholder="{{__('page.period')}}">
                            <button type="submit" class="btn btn-primary pd-y-7 ml-3"> <i class="fa fa-search"></i> {{__('page.search')}}</button>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="card card-body">
                        <div id="line_chart" style="height:400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/echarts/echarts-en.js')}}"></script>
<script src="{{asset('master/plugins/daterangepicker/jquery.daterangepicker.min.js')}}"></script>
<script>
    var role = "{{Auth::user()->role->slug}}";
    var legend_array = {!! json_encode([__('page.purchase'), __('page.sale'), __('page.payment')]) !!};
    var purchase = "{{__('page.purchase')}}";
    var sale = "{{__('page.sale')}}";
    var payment = "{{__('page.payment')}}";
        
    // console.log(legend_array);
    var Chart_overview = function() {

        var dashboard_chart = function() {
            if (typeof echarts == 'undefined') {
                console.warn('Warning - echarts.min.js is not loaded.');
                return;
            }

            // Define elements
            var area_basic_element = document.getElementById('line_chart');

            if (area_basic_element) {

                var area_basic = echarts.init(area_basic_element);

                area_basic.setOption({

                    color: ['#2ec7c9','#5ab1ef','#ff0000','#d87a80','#b6a2de'],

                    textStyle: {
                        fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                        fontSize: 13
                    },

                    animationDuration: 750,

                    grid: {
                        left: 0,
                        right: 40,
                        top: 35,
                        bottom: 0,
                        containLabel: true
                    },

                    
                    legend: {
                        data: [purchase, sale, payment],
                        itemHeight: 8,
                        itemGap: 20
                    },

                    tooltip: {
                        trigger: 'axis',
                        backgroundColor: 'rgba(0,0,0,0.75)',
                        padding: [10, 15],
                        textStyle: {
                            fontSize: 13,
                            fontFamily: 'Roboto, sans-serif'
                        }
                    },

                    xAxis: [{
                        type: 'category',
                        boundaryGap: false,
                        data: {!! json_encode($key_array) !!},
                        axisLabel: {
                            color: '#333'
                        },
                        axisLine: {
                            lineStyle: {
                                color: '#999'
                            }
                        },
                        splitLine: {
                            show: true,
                            lineStyle: {
                                color: '#eee',
                                type: 'dashed'
                            }
                        }
                    }],

                    yAxis: [{
                        type: 'value',
                        axisLabel: {
                            color: '#333'
                        },
                        axisLine: {
                            lineStyle: {
                                color: '#999'
                            }
                        },
                        splitLine: {
                            lineStyle: {
                                color: '#eee'
                            }
                        },
                        splitArea: {
                            show: true,
                            areaStyle: {
                                color: ['rgba(250,250,250,0.1)', 'rgba(0,0,0,0.01)']
                            }
                        }
                    }],

                    series: [
                        {
                            name: purchase,
                            type: 'line',
                            data: {!! json_encode($purchase_array) !!},
                            areaStyle: {
                                normal: {
                                    opacity: 0.25
                                }
                            },
                            smooth: true,
                            symbolSize: 7,
                            itemStyle: {
                                normal: {
                                    borderWidth: 2
                                }
                            }
                        },
                        {
                            name: sale,
                            type: 'line',
                            smooth: true,
                            symbolSize: 7,
                            itemStyle: {
                                normal: {
                                    borderWidth: 2
                                }
                            },
                            areaStyle: {
                                normal: {
                                    opacity: 0.25
                                }
                            },
                            data: {!! json_encode($sale_array) !!}
                        },
                        {
                            name: payment,
                            type: 'line',
                            smooth: true,
                            symbolSize: 7,
                            itemStyle: {
                                normal: {
                                    borderWidth: 2
                                }
                            },
                            areaStyle: {
                                normal: {
                                    opacity: 0.25
                                }
                            },
                            data: {!! json_encode($payment_array) !!}
                        }
                    ]
                });
            }

            // Resize function
            var triggerChartResize = function() {
                area_basic_element && area_basic.resize();
            };

            // On sidebar width change
            $(document).on('click', '.sidebar-control', function() {
                setTimeout(function () {
                    triggerChartResize();
                }, 0);
            });

            // On window resize
            var resizeCharts;
            window.onresize = function () {
                clearTimeout(resizeCharts);
                resizeCharts = setTimeout(function () {
                    triggerChartResize();
                }, 200);
            };
        };

        return {
            init: function() {
                dashboard_chart();
            }
        }
    }();

    document.addEventListener('DOMContentLoaded', function() {
        Chart_overview.init();
    });

</script>
<script>
    $(document).ready(function () {
        $("#period").dateRangePicker();
        $("#top_company_filter").change(function(){
            $("#top_filter_form").submit();
        });

        $("#expire_alert").click(function(){
            swal("{{$return['expired_purchases']}} purchases is expired.");
        });
    });
</script>
@endsection
