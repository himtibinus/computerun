<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @component('components.meta')
    @endcomponent
</head>
<body class="is-bootstrap">
    @component("components.navbar")
    @endcomponent
    @if (app('request')->path() == 'home' && Auth::check())
    <div class="container-2 content-top bg-home">
    @else
    <div class="container-2 content-top bg-event">
    @endif
        @if (app('request')->path() == 'home')
            @component("components.navbar-mobile")
            @endcomponent
        @elseif (Str::startsWith(app('request')->path(), 'admin/event/'))
            @component("components.navbar-mobile", ["template" => "admin-event-details"])
            @endcomponent
        @else
            @component("components.navbar-mobile", ["template" => "login-page"])
            @endcomponent
        @endif
        <div class="margin-2 content-divider">
            @if (Str::startsWith(app('request')->path(), 'admin/event/'))
                <h1 class="font-800">
                    {{$event->name}}
                    @if ($event->opened == 1)
                        @component ('components.bootstrap-icons', ['icon' => 'unlock'])
                        @endcomponent
                    @else
                        @component ('components.bootstrap-icons', ['icon' => 'lock-fill'])
                        @endcomponent
                    @endif
                    @if ($event->attendance_opened == 1)
                        @if ($event->attendance_is_exit == 1)
                            @component ('components.bootstrap-icons', ['icon' => 'box-arrow-left'])
                            @endcomponent
                        @else
                            @component ('components.bootstrap-icons', ['icon' => 'box-arrow-in-right'])
                            @endcomponent
                        @endif
                    @endif
                </h1>
                <div class="row">
                    <div class="col-12 col-md-4">
                        <p class="h4 font-700">Seats</p>
                        <p class="display-4">{{$event->currentseats}}/{{$event->seats}}</p>
                    </div>
                    <div class="col-12 col-md-4">
                        <p class="h4 font-700">Attendance</p>
                        <p class="display-4">{{$event->lastattendance}}/{{$event->firstattendance}}</p>
                    </div>
                    <div class="col-12 col-md-4">
                        <p class="h4 font-700">Event Token</p>
                        <p class="display-4">{{$event->totp_key}}</p>
                    </div>
                </div>
                <p class="lead">Welcome! Manage your tickets here.</p>
                {{-- <a class="btn btn-primary" href="/register" role="button">Register</a> --}}
                @if (app('request')->path() == 'home')
                    <a class="btn button button-dark" data-toggle="modal" href="" data-target="#accountSettings" role="button">Profile Settings</a>
                @endif
                <a class="btn button button-white" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                {{ __('Logout') }}</a>
            @else
                @switch (app('request')->path())
                    @case ('register')
                        <p class="display-4 text-center font-800 gradient-text">Create Account</p>
                        @break
                    @case ('password/reset')
                        <p class="display-4 text-center font-800 gradient-text">Reset Password</p>
                        @break
                    @default
                        @guest
                        <p class="display-4 text-center font-800 gradient-text">Login</p>
                        @else
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h5 class="font-800 font-airstrike gradient-text">WELCOME,</h5>
                                    <h1 class="font-800 font-airstrike gradient-text">{{Auth::user()->name}}
                                        @if (Auth::user()->verified == 1)
                                            @component ("components.bootstrap-icons", ["icon" => "patch-check-fll"])
                                            @endcomponent
                                        @endif
                                    </h3>
                                    <h3>{{DB::table('universities')->where('id', Auth::user()->university_id)->first()->name}}</h3>
                                    @if (Auth::user()->university_id >= 2 && Auth::user()->university_id <= 4)
                                        <h5 class="font-700">NIM: {{Auth::user()->nim}}</h5>
                                    @endif
                                </div>
                                <div class="col-12 col-md-6">
                                    {{-- <h4 class="font-800">CONTACT DETAILS</h4>
                                    <h1 class="display-4">{{Auth::user()->email}}</h1> --}}
                                </div>
                            </div>
                            <p class="lead">Welcome! Manage your tickets here.</p>
                            {{-- <a class="btn btn-primary" href="/register" role="button">Register</a> --}}
                            <a class="btn button button-dark" data-toggle="modal" href="" data-target="#accountSettings" role="button">Profile Settings</a>
                            <a class="btn button button-white" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}</a>
                        @endguest
                @endswitch
            @endif
            </span>
        </div>
        <div id="app">
            @if (Auth::check() && (Auth::user()->university_id == 2 || Auth::user()->university_id == 3))
            <nav class="navbar navbar-expand-md navbar-light shadow-sm font-800" style="background: -webkit-linear-gradient(115deg, #37e2bc, #249ef2); color: #22365f;">
                <div class="container">
                    <a class="navbar-brand" href="#">
                        Admin Menu
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mr-auto">

                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto font-700">
                            <!-- Authentication Links -->
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="profileDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        Profile
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                                        <a class="dropdown-item" href="/home#tickets">Your Tickets</a>
                                        <a class="dropdown-item" href="/home#teams">Your Teams</a>
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a id="adminDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        Admin
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="adminDropdown">
                                        <a class="dropdown-item" href="/admin/events">Manage Events</a>
                                        <a class="dropdown-item" href="/admin/users">Manage Users</a>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
            @endif
            <img class="container-clip" src="/img/backgrounds/2.png">


            @if (Auth::check() && (Auth::user()->university_id == 2 || Auth::user()->university_id == 3))
            <main class="margin-1 after-container-clip content-divider">
            @else
            <main class="margin-1 after-container-clip">
            @endif
                @yield('content')
            </main>
            <img class="container-clip for-footer is-bootstrap" src="/img/backgrounds/7.png">

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
    @component('components.footer')
    @endcomponent
</body>
</html>
