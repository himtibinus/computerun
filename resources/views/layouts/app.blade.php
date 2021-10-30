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
    <?php
      if (Auth::check()){
        $user = Auth::user();

        $verified = DB::table('user_properties')->where('user_id', $user->id)->where('field_id', 'verified')->first();
        $user->verified = ($verified ? $verified->value : 0);

        $nim = DB::table('user_properties')->where('user_id', $user->id)->where('field_id', 'university.nim')->first();
        $user->verified = ($nim ? $nim->value : null);
      }
    ?>
    @if (app('request')->path() == 'home' && Auth::check())
    <div class="container-2 content-top bg-home pb-0">
    @else
    <div class="container-2 content-top bg-event pb-0">
    @endif
        <div class="margin-2 content-divider">
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
                                <h1 class="font-800 font-airstrike gradient-text">{{$user->name}}
                                    @if ($user->verified == 1)
                                        @component ("components.bootstrap-icons", ["icon" => "patch-check-fll"])
                                        @endcomponent
                                    @endif
                                </h3>
                                <h3>{{DB::table('universities')->where('id', $user->university_id)->first()->name}}</h3>
                                @if ($user->university_id >= 2 && $user->university_id <= 4)
                                    <h5 class="font-700">NIM: {{$user->nim}}</h5>
                                @endif
                            </div>
                            <div class="col-12 col-md-6">
                                {{-- <h4 class="font-800">CONTACT DETAILS</h4>
                                <h1 class="display-4">{{$user->email}}</h1> --}}
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
            </span>
        </div>
        <div id="app">
            <img class="container-clip" src="/img/backgrounds/2.png">


            @if (Auth::check() && ($user->university_id == 2 || $user->university_id == 3))
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
        @component('components.imagecompat', ['src' => '/img/accessories/2021/wave-end-1.svg', 'class' => 'w-100 p-0 mt-5 mb-0'])
        @endcomponent
    </div>
    @component('components.footer')
    @endcomponent
    <script>
        // Utility function to use Ctrl+K or /
        var commandPalette = new bootstrap.Modal(document.getElementById('commandpalette'))
        var macOS = navigator.userAgent.indexOf('Mac OS X') != -1;
        document.addEventListener('keydown', function (event) {
            if (
                (
                    (macOS && event.metaKey) ||
                    (!macOS && event.ctrlKey)
                ) && event.key === 'k'
            ) {
                event.preventDefault();
                commandPalette.show();
            }
        });
    </script>
</body>
</html>
