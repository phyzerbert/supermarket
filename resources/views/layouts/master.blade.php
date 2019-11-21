<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8" />
        <title>{{config("app.name")}} - Multi Store Management System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="A fully featured Multi Store Management System for Colomnia" name="description" />
        <meta content="Yuyuan Zhang" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <link rel="shortcut icon" href="{{asset('images/favicon.png')}}">

        <link href="{{asset('master/plugins/notifications/notification.css')}}" rel="stylesheet">
        
        <!-- Custom Files -->
        <link href="{{asset('master/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('master/css/icons.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('master/css/style.css')}}" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" href="{{asset('master/plugins/imageviewer/css/jquery.verySimpleImageViewer.css')}}">
        <link href="{{asset('master/plugins/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">

        <link href="{{asset('master/css/custom.css')}}" rel="stylesheet" type="text/css" />

        <script src="{{asset('master/js/modernizr.min.js')}}"></script>        

        @yield('style')

    </head>


    <body class="fixed-left">
        <div id="ajax-loading" class="text-center">
            <img class="mx-auto" src="{{asset('images/loader.gif')}}" width="70" alt="" style="margin:45vh auto;">
        </div>
        <div id="wrapper">
        
            @include('layouts.header')

            @include('layouts.aside')
                    
            <div class="content-page">
                
                @yield('content')

                <footer class="footer">
                    2019 © {{config('app.name')}}
                </footer>

            </div>
            @if(!Auth::user()->hasRole('buyer'))
                <div id="app">
                    <chat :user="{{auth()->user()}}"></chat>                
                </div>
            @endif
            <div class="modal fade" id="attachModal">
                <div class="modal-dialog" style="margin-top:17vh">
                    <div class="modal-content">
                        <div id="image_preview"></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var resizefunc = [];
        </script>

        <script src="{{asset('master/js/jquery.min.js')}}"></script>
        <script src="{{asset('master/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('master/js/detect.js')}}"></script>
        <script src="{{asset('master/js/fastclick.js')}}"></script>
        <script src="{{asset('master/js/jquery.slimscroll.js')}}"></script>
        <script src="{{asset('master/js/jquery.blockUI.js')}}"></script>
        <script src="{{asset('master/js/waves.js')}}"></script>
        <script src="{{asset('master/js/wow.min.js')}}"></script>
        <script src="{{asset('master/js/jquery.nicescroll.min.js')}}"></script>
        {{-- <script src="{{asset('master/js/jquery.scrollTo.min.js')}}"></script> --}}
        
        <script src="{{asset('master/plugins/notifyjs/dist/notify.min.js')}}"></script>
        <script src="{{asset('master/plugins/notifications/notify-metro.js')}}"></script>
        <script src="{{asset('master/plugins/notifications/notifications.js')}}"></script>
        <script src="{{asset('master/plugins/moment/moment.min.js')}}"></script>
        <script src="{{asset('master/plugins/imageviewer/js/jquery.verySimpleImageViewer.min.js')}}"></script>
        <script src="{{asset('master/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
        @if(!Auth::user()->hasRole('buyer'))
            <script src="{{ asset('js/app.js') }}"></script>
        @endif
        @yield('script')

        <script src="{{asset('master/js/jquery.app.js')}}"></script>

	    <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function showloader(){
                $("#ajax-loading").show();
            }
        </script>

        <script>
            var notification = '<?php echo session()->get("success"); ?>';
            if(notification != ''){
                $.Notification.autoHideNotify('success', 'top right', "{{__('page.success')}}", notification)
            }
            var errors_string = '<?php echo json_encode($errors->all()); ?>';
            errors_string=errors_string.replace("[","").replace("]","").replace(/\"/g,"");
            var errors = errors_string.split(",");
            if (errors_string != "") {
                for (let i = 0; i < errors.length; i++) {
                    const element = errors[i];
                    $.Notification.autoHideNotify('error', 'top right', "{{__('page.error')}}", element)
                } 
            }

            $(".btn-confirm").click(function(e){
                e.preventDefault();
                let url = $(this).attr('href');
                Swal.fire({
                    title: "{{__('page.are_you_sure')}}",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('page.yes')}}",
                    cancelButtonText: "{{__('page.cancel')}}",
                }).then((result) => {
                    if (result.value) {
                        location.href = url
                    }else {
                        console.log('Cancelled')
                    }
                })
            }) 
        </script>
	</body>
</html>