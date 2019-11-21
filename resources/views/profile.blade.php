@extends('layouts.master')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-user"></i> {{__('page.my_profile')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.profile')}}</li>
                    </ol>
                </div>
            </div>    
        
            @php
                $role = Auth::user()->role->slug;
            @endphp
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-body">
                        <div class="text-center profile-image">
                            <img src="@if($user->picture){{asset($user->picture)}}@else{{asset('images/avatar128.png')}}@endif" width="75%" class="rounded-circle" alt="">
                        </div>
                        <p class="text-info text-center mt-4">{{$user->first_name}} {{$user->last_name}}</p>
                        <h3 class="text-primary text-center"><span class="badge badge-primary">{{$user->role->name}}</span></h3>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card card-body">                        
                        <form class="form-layout form-layout-1" action="{{route('updateuser')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group my-3">
                                <label class="form-control-label">{{__('page.username')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" value="{{$user->name}}" placeholder="{{__('page.username')}}" required>
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.first_name')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="first_name" value="{{$user->first_name}}" placeholder="{{__('page.first_name')}}" required>
                                @error('first_name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.last_name')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="last_name" value="{{$user->last_name}}" placeholder="{{__('page.last_name')}}" required>
                                @error('last_name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.phone_number')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="phone_number" value="{{$user->phone_number}}" placeholder="{{__('page.phone_number')}}" required>
                                @error('phone_number')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.company')}}: <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="company" class="wd-100" data-placeholder="Select Company">
                                    <option label="{{__('page.select_company')}}" hidden></option>
                                    @foreach ($companies as $item)
                                        <option value="{{$item->id}}" @if($user->company_id == $item->id) selected @endif>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('company')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.picture')}}:</label>                                
                                <label class="custom-file wd-100p">
                                    <input type="file" name="picture" id="file2" class="file-input-styled" accept="image/*">
                                </label>
                            </div> 
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.password')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="password" name="password" placeholder="Password">
                            </div>
                            <div class="form-group mb-2">
                                <label class="form-control-label">{{__('page.confirm_password')}}: <span class="text-danger">*</span></label>
                                <input class="form-control" type="password" name="password_confirmation" placeholder="{{__('page.confirm_password')}}">
                            </div>
                            <div class="form-layout-footer text-right mt-5">
                                <button type="submit" class="btn btn-primary tx-20"><i class="fa fa-floppy-o mr-2"></i> {{__('page.save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>                
    </div>
@endsection

@section('script')
<script src="{{asset('master/plugins/styling/uniform.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('.file-input-styled').uniform({
            fileButtonClass: 'action btn bg-primary text-white'
        });
    });
</script>
@endsection
