@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-credit-card"></i> {{__('page.users_report')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li><a href="{{route('report.overview_chart')}}">{{__('page.reports')}}</a></li>
                        <li class="active">{{__('page.users_report')}}</li>
                    </ol>
                </div>
            </div>        
        @php
            $role = Auth::user()->role->slug;
        @endphp
        <div class="card card-body card-fill">
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
                        <input type="text" class="form-control form-control-sm mr-sm-2 mb-2" name="phone_number" id="search_phone" value="{{$phone_number}}" placeholder="{{__('page.phone_number')}}">
                        
                        <button type="submit" class="btn btn-sm btn-primary mb-2"><i class="fa fa-search"></i>&nbsp;&nbsp;{{__('page.search')}}</button>
                        <button type="button" class="btn btn-sm btn-info mb-2 ml-1" id="btn-reset"><i class="fa fa-eraser"></i>&nbsp;&nbsp;{{__('page.reset')}}</button>
                    </form>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="wd-40">#</th>
                                <th>{{__('page.username')}}</th>
                                <th>{{__('page.first_name')}}</th>
                                <th>{{__('page.last_name')}}</th>
                                <th>{{__('page.phone_number')}}</th>
                                <th>{{__('page.company')}}</th>
                                <th>{{__('page.role')}}</th>
                                <th>{{__('page.status')}}</th>
                                <th>{{__('page.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($data as $item)                             
                                <tr>
                                    <td class="wd-40">{{ (($data->currentPage() - 1 ) * $data->perPage() ) + $loop->iteration }}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->first_name}}</td>
                                    <td>{{$item->last_name}}</td>
                                    <td>{{$item->phone_number}}</td>
                                    <td>@isset($item->company){{$item->company->name}}@endisset</td>
                                    <td>@isset($item->role->name){{$item->role->name}}@endisset</td>
                                    <td>
                                        @if ($item->status == 1)
                                            <span class="badge badge-success"><i class="fa fa-check"></i> {{__('page.active')}}</span>
                                        @elseif($item->status == 0)
                                            <span class="badge badge-danger"><i class="fa fa- exclamation-triangle"></i>{{__('page.inactive')}}</span>
                                        @endif
                                    </td>                                      
                                    <td>
                                        <a href="{{route('report.users_report.purchases', $item->id)}}" class="badge badge-primary">{{__('page.view_reports')}}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>                
                    <div class="clearfix mt-2">
                        <div class="float-left" style="margin: 0;">
                            <p>{{__('page.total')}} <strong style="color: red">{{ $data->total() }}</strong> {{__('page.items')}}</p>
                        </div>
                        <div class="float-right" style="margin: 0;">
                            {!! $data->appends([
                                'company_id' => $company_id, 
                                'phone_number' => $phone_number,
                                'name' => $name,
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
            $("#search_company").val('');
            $("#search_phone").val('');
            $("#search_name").val('');
        });
    });
</script>
@endsection
