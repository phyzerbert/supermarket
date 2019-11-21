@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-exclamation-triangle"></i> {{__('page.product_quantity_alert')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('report.product_quantity_alert')}}">{{__('page.reports')}}</a></li>
                        <li class="active">{{__('page.product_quantity_alert')}}</li>
                    </ol>
                </div>
            </div>        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="card card-body card-fill">
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                {{-- <th width="40">#</th> --}}
                                <th width="60">{{__('page.image')}}</th>
                                <th>{{__('page.product_code')}}</th>
                                <th>{{__('page.product_name')}}</th>
                                <th>{{__('page.quantity')}}</th>
                                <th>{{__('page.alert_quantity')}}</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($data as $item)
                                @php
                                    $quantity = \App\Models\StoreProduct::where('product_id', $item->id)->sum('quantity');
                                @endphp
                                @if ($item->alert_quantity >= $quantity)                                
                                    <tr>
                                        {{-- <td class="wd-40">{{ $loop->index+1 }}</td> --}}
                                        <td class="image py-1"><img src="@if($item->image){{asset($item->image)}}@else{{asset('images/no-image.png')}}@endif" class="rounded-circle" width="40" height="40" alt=""></td>
                                        <td>{{$item->code}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$quantity}}</td>
                                        <td>{{$item->alert_quantity}}</td>                                        
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>                
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        
    });
</script>
@endsection
