<nav class="navbar navbar-expand-lg navbar-display navbar-light sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand discreet" href="/">
            <img src="/img/icons/2021/mainlogo/c-transparent.png" alt="" style="width: 4vw; min-width: 80px;">
        </a>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#commandpalette"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0" style="font-size: 1.4rem;">
                <li class="nav-item">
                    <a class="nav-link" href="/" style="color:#8D2A97;">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" style="color:#8D2A97;">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" style="color:#8D2A97;">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" style="color:#8D2A97;">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" style="color:#8D2A97;">Guidebook</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#commandpalette" aria-label="Toggle navigation" style="color:#8D2A97;">
                        <i class="bi bi-person-circle" style="color: #8D2A97"></i>&nbsp;
                        <span>{{ Auth::user()->name ?? 'Guest' }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Command Palette Modal -->
<div class="modal fade" id="commandpalette" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
            <div class="modal-header card-img-top">
                <div class="input-group d-none">
                    <span class="input-group-text h3 mb-0" id="basic-addon1"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control h3 mb-0" placeholder="Search..." aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="container bg-light p-3">
                <div class="row">
                    <div class="col-8">
                        @if (Auth::check())
                            <h3 class="font-800">{{Auth::user()->name ?? 'Guest'}}
                                @if (Auth::user()->verified == 1)
                                    @component ("components.bootstrap-icons", ["icon" => "patch-check-fll"])
                                    @endcomponent
                                @endif
                            </h3>
                            <h5>{{ DB::table('universities')->where('id', Auth::user()->university_id)->first()->name ?? '' }}
                                @if (Auth::user()->university_id >= 4)
                                    &bull;Student ID/NIM: {{ Auth::user()->nim }}
                                @endif
                            </h5>
                        @else
                            <h3 class="font-800">Guest</h3>
                            <h5>Sign in to register to our events</h5>
                        @endif
                    </div>
                    <div class="col-4 text-right">
                        @if (Auth::check())
                            <a href="/home" class="button button-gradient">Profile</a>
                        @else
                            <a href="/login" class="button button-gradient">Sign In</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="container">
                <p class="my-2 d-none"><b>Tip:</b> Use <kbd>/</kbd> or <kbd>Ctrl+K</kbd> to trigger this awesome menu!</p>
                <div id="commandpallete-result" class="d-none">
                    <h4 class="full-underline my-3"><i class="bi bi-search" aria-hidden="true"></i> Search Results</h4>
                    <div class="list-group">
                        <a href="#" class="discreet list-group-item list-group-item-action"><i class="bi bi-calendar-event"></i> Opening Ceremony</a>
                        <a href="#" class="discreet list-group-item list-group-item-action"><i class="bi bi-gear-wide-connected"></i> Settings</a>
                        <a href="#" class="discreet list-group-item list-group-item-action"><i class="bi bi-calendar-event"></i> Webinar 1</a>
                        <a href="#" class="discreet list-group-item list-group-item-action"><i class="bi bi-book-half"></i> Webinar Guidebook</a>
                    </div>
                </div>
                <div id="commandpallete-mainmenu">
                    <h4 class="full-underline my-3"><i class="bi bi-list" aria-hidden="true"></i> Main Menu
                    </h4>
                    <div class="row text-center justify-content-center">
                        <a class="col-3 m-2 discreet" href="/">
                            <i class="bi bi-house-fill h1"></i>
                            <p class="h5">Home</p>
                        </a>
                        <div class="col-3 m-2">
                            <i class="bi bi-info-circle h1"></i>
                            <p class="h5">About</p>
                        </div>
                        <div class="col-3 m-2">
                            <i class="bi bi-calendar-week h1"></i>
                            <p class="h5">Events</p>
                        </div>
                        <div class="col-3 m-2">
                            <i class="bi bi-chat-quote h1"></i>
                            <p class="h5">Contact</p>
                        </div>
                        <div class="col-3 m-2">
                            <i class="bi bi-book-half h1"></i>
                            <p class="h5">Guidebook</p>
                        </div>
                        <div class="col-3 m-2">
                            <i class="bi bi-person-circle h1"></i>
                            <p class="h5">Profile</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
