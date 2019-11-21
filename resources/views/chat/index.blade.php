@extends('layouts.master')

@section('style')    
    <link rel="stylesheet" href="{{asset('master/plugins/imageviewer/css/jquery.verySimpleImageViewer.css')}}">
    <style>
        #image_preview {
            max-width: 600px;
            height: 600px;
        }
        .image_viewer_inner_container {
            width: 100% !important;
        }
    </style>
    <script src="{{ asset('js/app.js') }}" defer></script>
@endsection

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="pull-left page-title"><i class="fa fa-wechat"></i> {{__('page.chatting')}}</h3>
                    <ol class="breadcrumb pull-right">
                        <li><a href="{{route('home')}}">{{__('page.home')}}</a></li>
                        <li class="active">{{__('page.chatting')}}</li>
                    </ol>
                </div>
            </div>
            <div class="" id="chatbox">
                <chat-component :user="{{auth()->user()}}"></chat-component>
            </div>
        </div>                
    </div>  
    
    <div class="modal fade" id="attachModal">
        <div class="modal-dialog" style="margin-top:17vh">
            <div class="modal-content">
                <div id="image_preview"></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('master/plugins/imageviewer/js/jquery.verySimpleImageViewer.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $(".attachment-image").click(function(e){
                // e.preventDefault();
                let path = $(this).href('src');
                console.log(path)
                // $("#attachment").attr('src', path);
                $("#image_preview").html('')
                $("#image_preview").verySimpleImageViewer({
                    imageSource: path,
                    frame: ['100%', '100%'],
                    maxZoom: '900%',
                    zoomFactor: '10%',
                    mouse: true,
                    keyboard: true,
                    toolbar: true,
                });
                $("#attachModal").modal();
            });
    
        });
    </script>
@endsection