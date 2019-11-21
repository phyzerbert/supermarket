@extends('layouts.auth')
@section('style')
    <style>
        li {
            display: inline-block;
            font-size: 1.2em;
            list-style-type: none;
            text-transform: uppercase;
        }

        li span {
            font-size: 1.5rem;
            margin-right: 10px;
        }
    </style>
@endsection
@section('content')
    <div class="wrapper-page">
        <div class="card card-pages">

            <div class="card-header" style="background-image: url('images/phone_verification.jpg'); height:180px;"> 
                <div class="bg-overlay"></div>
            </div> 

            <div class="card-body pt-5">
                <form method="post" action="{{ route('verify') }}" class="text-center">
                    @csrf
                    <div class="form-group m-b-3"> 
                        <div class="input-group"> 
                            <input type="number" id="code" class="form-control"  placeholder="{{__('page.verification_code')}}" required autofocus /> 
                            <span class="input-group-append">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">{{__('page.verify')}}</button>
                            </span> 
                        </div> 
                    </div>
                    <div class="form-group">
                        <ul class="p-2 text-center">
                            <li class="px-2"><span id="minutes"></span>Min</li>
                            <li><span id="seconds"></span>Sec</li>
                        </ul>
                    </div>                
                </form>
            </div>            
        </div>
    </div>

@endsection

@section('script')
    <script>

        let countDown = 300,

        x = setInterval(function() {

            countDown -= 1;            
            document.getElementById('minutes').innerText = pad2(Math.floor(countDown / 60)),
            document.getElementById('seconds').innerText = pad2(Math.floor(countDown % 60));
        
            if (countDown == 0) {
                clearInterval(x);
                window.location.href = "{{route('login')}}";
            }

        }, 1000);

        function pad2(number) {   
            return (number < 10 ? '0' : '') + number        
        }

    </script>
@endsection
