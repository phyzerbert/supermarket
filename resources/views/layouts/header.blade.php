<div class="topbar">
        <div class="topbar-left">
            <div class="text-center">
                <a href="{{route('home')}}" class="logo"><span><img src="{{asset('images/logo.png')}}" height="56" alt=""></span></a>
            </div>
        </div>
        
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <ul class="list-inline menu-left mb-0">
                    <li class="float-left">
                        <a href="#" class="button-menu-mobile open-left">
                            <i class="fa fa-bars"></i>
                        </a>
                    </li>
                </ul>
    
                <ul class="nav navbar-right float-right list-inline">
                    <li class="user-company hide-phone mr-5 pt-2">
                        @if (Auth::user()->hasRole('user') || Auth::user()->hasRole('secretary'))
                            <span class="text-light">{{ Auth::user()->company->name }}</span>
                        @endif
                    </li>
                    <li class="dropdown dropdown-lang">
                        @php $locale = session()->get('locale'); @endphp
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                            @switch($locale)
                                @case('en')
                                    <img src="{{asset('images/lang/en.png')}}" width="30px">&nbsp;&nbsp;<span class="hide-phone"> English</span>
                                    @break
                                @case('es')
                                    <img src="{{asset('images/lang/es.png')}}" width="30px">&nbsp;&nbsp;<span class="hide-phone"> Español</span>
                                    @break
                                @default
                                    <img src="{{asset('images/lang/es.png')}}" width="30px">&nbsp;&nbsp;<span class="hide-phone"> Español</span>
                            @endswitch
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a class="dropdown-item" href="{{route('lang', 'en')}}"><img src="{{asset('images/lang/en.png')}}" class="rounded-circle" width="30px" height="30"> English</a></li>
                            <li><a class="dropdown-item" href="{{route('lang', 'es')}}"><img src="{{asset('images/lang/es.png')}}" class="rounded-circle" width="30px" height="30"> Español</a></li>
                        </ul>
                    </li>
                    <li class="dropdown open">
                        <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="true">
                            <img src="@if (Auth::user()->picture != ''){{asset(Auth::user()->picture)}} @else {{asset('images/avatar128.png')}} @endif" class="wd-32 rounded-circle" alt="">
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('profile')}}" class="dropdown-item"><i class="fa fa-user mr-2"></i> {{__('page.my_profile')}}</a></li>
                            <li>
                                <a href="#"
                                    class="dropdown-item"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();" 
                                ><i class="fa fa-sign-out mr-2"></i> {{__('page.sign_out')}}</a>
                            </li>                        
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </li>
                    @if(!Auth::user()->hasRole('buyer'))
                        <li class="">
                            <a href="#" class="right-bar-toggle waves-effect waves-light">
                                <i class="md md-chat"></i>
                                <span class="badge badge-pill badge-xs badge-danger" id="total_unreads"></span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>
    </div>