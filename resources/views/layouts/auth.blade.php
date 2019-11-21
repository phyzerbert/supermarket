<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{{config("app.name")}} - Multi Store Management System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="A fully featured Multi Store Management System for Colomnia" name="description" />
        <meta content="Yuyuan Zhang" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <link rel="shortcut icon" href="{{asset('master/images/favicon.png')}}">

        <!-- Custom Files -->
        <link href="{{asset('master/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('master/css/icons.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('master/css/style.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('master/plugins/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">

        <script src="{{asset('master/js/modernizr.min.js')}}"></script>
        
        @yield('style')
        
    </head>
    <body>

        @yield('content')
        
    	<script>
            var resizefunc = [];
        </script>

        <!-- Main  -->
        <script src="{{asset('master/js/jquery.min.js')}}"></script>
        <script src="{{asset('master/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('master/js/detect.js')}}"></script>
        <script src="{{asset('master/js/fastclick.js')}}"></script>
        <script src="{{asset('master/js/jquery.slimscroll.js')}}"></script>
        <script src="{{asset('master/js/jquery.blockUI.js')}}"></script>
        <script src="{{asset('master/js/waves.js')}}"></script>
        <script src="{{asset('master/js/wow.min.js')}}"></script>
        <script src="{{asset('master/js/jquery.nicescroll.js')}}"></script>
        <script src="{{asset('master/js/jquery.scrollTo.min.js')}}"></script>
        <script src="{{asset('master/plugins/sweetalert2/sweetalert2.min.js')}}"></script>

        <script src="{{asset('master/js/jquery.app.js')}}"></script>
        
        @yield('script')
	</body>
</html>