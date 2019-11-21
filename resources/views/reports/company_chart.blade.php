@extends('layouts.master')
@section('style')
    <link href="{{asset('master/plugins/daterangepicker/daterangepicker.min.css')}}" rel="stylesheet">
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-pie-chart"></i> {{__('page.company_chart')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('report.overview_chart')}}">{{__('page.reports')}}</a></li>
                        <li class="active">{{__('page.company_chart')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <form action="" class="form-inline mx-auto" method="post">
                            @csrf
                            <input type="text" class="form-control form-control-sm" name="period" id="period" style="width:250px !important" value="{{$period}}" autocomplete="off" placeholder="{{__('page.period')}}">
                            <button type="submit" class="btn btn-sm btn-primary pd-y-7 ml-2"> <i class="fa fa-search"></i> {{__('page.search')}}</button>
                            <button type="button" class="btn btn-sm btn-warning pd-y-7 ml-2" id="btn-reset"> <i class="fa fa-eraser"></i> {{__('page.reset')}}</button>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <canvas id="bar_chart" style="height:600px;"></canvas>
                    </div>
                </div>
            </div>
        </div>                
    </div>

@endsection

@section('script')
<script src="{{asset('master/plugins/Chart.js/Chart.js')}}"></script>
<script src="{{asset('master/plugins/daterangepicker/jquery.daterangepicker.min.js')}}"></script>
<script>
    var barData = {
        labels: {!! json_encode($company_names) !!},
        datasets: [
            {
                label: "{{__('page.purchase')}}",
                backgroundColor:'#DADDE0',
                data: {!! json_encode($company_purchases_array) !!}
            },
            {
                label: "{{__('page.sale')}}",
                backgroundColor: '#2ecc71',
                borderColor: "#fff",
                data: {!! json_encode($company_sales_array) !!}
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
        $("#period").dateRangePicker()
        $("#btn-reset").click(function(){
            $("#period").val('');
        });
    });
</script>
@endsection
