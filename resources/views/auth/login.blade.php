@extends('layouts.auth')

@section('content')
    @php
        $verify_messages = [
            '10' => __('page.concurrent_verifications_to_the_same_number_are_not_allowed'),
            '4' => __('page.invalid_credentials_were_provided'),
            '5' => __('page.internal_error'),
        ];
    @endphp

    <div class="wrapper-page">
        <div class="card card-pages">
            <div class="card-header" style="background-image: url('images/sign_in.jpg')"> 
                <div class="bg-overlay"></div>
                <h3 class="text-center m-t-10 text-white"> {{__('page.sign_in')}} <strong>{{ config("app.name") }}</strong> </h3>
            </div> 

            <div class="card-body">
                @error('phone')
                    <span class="text-danger mt-2" role="alert">
                        <strong>
                            @if (isset($verify_messages[$message]))
                                {{ $verify_messages[$message] }}
                            @else
                                {{__('page.invalid_verification_request')}}
                            @endif
                        </strong>
                    </span>
                @enderror
                <form class="form-horizontal m-t-20" action="{{route('login')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="{{__('page.username')}}">
                        @error('name')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" placeholder="{{__('page.password')}}">
    
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="checkbox-signup">
                                    {{ __('page.remember_me') }}
                                </label>
                            </div>                            
                        </div>
                    </div>
                    
                    <div class="form-group row text-center mt-3">
                        <div class="col-md-6 pt-3">
                            <a href="{{route('lang', 'en')}}" class="btn btn-outline p-0 @if(config('app.locale') == 'en') border-primary border-2 @endif" title="English"><img src="{{asset('images/lang/en.png')}}" width="45px"></a>
                            <a href="{{route('lang', 'es')}}" class="btn btn-outline ml-2 p-0 @if(config('app.locale') == 'es') border-primary border-2 @endif" title="Spanish"><img src="{{asset('images/lang/es.png')}}" width="45px"></a>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary btn-lg w-lg waves-effect waves-light mt-2" type="submit"><i class="fa fa-sign-in"></i> {{__('page.sign_in')}}</button>
                        </div>
                    </div>

                    
                </form> 
            </div>                
        </div>
    </div>
@endsection

@section('script')
    <script>
        var notification = '<?php echo session()->get("ip_restriction"); ?>';
        if(notification != ''){
            Swal.fire({
                type: 'error',
                title: notification,
            })
        }
    </script>
@endsection
