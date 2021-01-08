@extends('layouts.app')

@section('content')
<form class="container" method="POST">
    @csrf
    @if (session('status'))
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            </div>
        </div>
        <?php
            Session::forget('status');
        ?>
    @endif
    @if (session('error'))
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            </div>
        </div>
        <?php
            Session::forget('error');
        ?>
    @endif

    <!-- Participants -->
    <h1 class="full-underline {{(session('status') || session('error')) ? 'content-divider' : ''}}" id="participants">Participants</h1>
    @if (count($registration) > 0)
        <table class="table margin-0 content-divider-short">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Contact</th>
                    @if (Auth::user()->university_id == 2)
                        <th scope="col">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($registration as $list)
                    <tr>
                        <th scope="row">{{$list->id}}</th>
                        <td>
                            <b class="h5 font-700">{{$list->name}}</b>
                            <br>{{$list->university_name}}
                            @if ($list->nim != null && $list->nim != '')
                                <br><b>NIM:</b> {{$list->nim}}
                            @endif
                            @if ($list->team_id != null && $list->team_id > 0)
                                <br><b>Team ID:</b> {{$list->team_id}}
                            @endif
                            @if ($list->remarks != null && $list->remarks != '')
                                <br>{{$list->remarks}}
                            @endif
                        </td>
                        <td>
                            <b class="font-700 h5">
                            @switch ($list->status)
                                @case (0)
                                    Pending
                                    @break
                                @case (1)
                                    Rejected
                                    @break
                                @case (2)
                                    Approved
                                    @break
                                @case (3)
                                    Cancelled
                                    @break
                                @case (4)
                                    Attending
                                    @break
                                @case (5)
                                    Attended
                                    @break
                                @default
                                    Unknown
                            @endswitch
                             ({{$list->status}})
                            </b>
                            @if($list->payment_code != null && $list->payment_code != '')
                                <br>
                                <b>Payment Code:</b> {{$list->payment_code}}
                            @endif
                            @if($list->file_id != null && $list->file_id != '' && is_object($file) && $file->name != null)
                                <br>
                                <a href="/admin/downloadFile/1/{{$list->file_id}}" target="_blank">Download Supporting File</a>
                            @endif
                            @if($list->file_id != null && $list->file_id != '' && is_object($file) && $file->answer_path != null)
                                <br>
                                <a href="/admin/downloadFile/2/{{$list->file_id}}" target="_blank">Download Case Answer</a>
                            @endif
                        </td>
                        <td>
                            <b>{{$list->email}}</b>
                            <br><b>Phone:</b> {{$list->phone}}
                            @if ($list->line != null && $list->line != '')
                                <br><b>LINE:</b> {{$list->line}}
                            @endif
                            @if ($list->whatsapp != null && $list->whatsapp != '')
                                <br><b>WhatsApp:</b> {{$list->whatsapp}}
                            @endif
                        </td>
                        @if (Auth::user()->university_id == 2)
                            <td>
                                <b>Override Status</b><br>
                                <select name="status-{{$list->id}}" id="status-{{$list->id}}">
                                    <option value="-1">Unchanged</option>
                                    <option value="0">0: Not yet accepted</option>
                                    <option value="1">1: Rejected</option>
                                    <option value="2">2: Approved</option>
                                    <option value="3">3: Cancelled</option>
                                    <option value="4">4: Attending</option>
                                    <option value="5">5: Attended</option>
                                </select><br>
                                @if($list->remarks != 'EMAIL SENT!' && $list->status > 1)
                                    <a href="/admin/sendemail/{{ $list->id }}">Send Notif Email</a>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="placeholder-sponsors content-divider-short text-center">
            <h2 class="font-800">No one's registered yet.</h2>
        </div>
    @endif

    <!-- Teams -->
    <h1 class="full-underline content-divider" id="teams">Teams</h1>
    @if (count($teams) > 0)
        <table class="table margin-0 content-divider-short">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teams as $list)
                    <tr>
                        <th scope="row">{{$list->id}}</th>
                        <td>{{$list->name}}</td>
                        <td>{{$list->score}}</td>
                        <td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="placeholder-sponsors content-divider-short text-center">
            <h2 class="font-800">No Teams Found.</h2>
        </div>
    @endif

    <!-- Committees -->
    <h1 class="full-underline content-divider" id="committees">Committees</h1>
    @if (count($committees) > 0)
        <table class="table margin-0 content-divider-short">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Status</th>
                    @if (Auth::user()->university_id == 2)
                        <th>Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($committees as $list)
                    <tr>
                        <th scope="row">{{$list->id}}</th>
                        <td>
                            <b class="h4 font-700">{{$list->name}}</b>
                            <br>{{$list->email}}
                            @if ($list->nim != null && $list->nim != '')
                                <br><b>NIM:</b> {{$list->nim}}
                            @endif
                            @if ($list->remarks != null && $list->remarks != '')
                                <br>{{$list->remarks}}
                            @endif
                        </td>
                        <td>
                            @switch ($list->status)
                                @case (0)
                                    Pending
                                    @break
                                @case (1)
                                    Rejected
                                    @break
                                @case (2)
                                    Approved
                                    @break
                                @case (3)
                                    Cancelled
                                    @break
                                @case (4)
                                    Attending
                                    @break
                                @case (5)
                                    Attended
                                    @break
                                @default
                                    Unknown
                            @endswitch
                                ({{$list->status}})
                            </b>
                        </td>
                        @if (Auth::user()->university_id == 2)
                            <td>
                                <b>Override Status</b><br>
                                <select name="status-{{$list->id}}" id="status-{{$list->id}}">
                                    <option value="-1">Unchanged</option>
                                    <option value="0">0: Not yet accepted</option>
                                    <option value="1">1: Rejected</option>
                                    <option value="2">2: Approved</option>
                                    <option value="3">3: Cancelled</option>
                                    <option value="4">4: Attending</option>
                                    <option value="5">5: Attended</option>
                                </select><br><br>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="placeholder-sponsors content-divider-short text-center">
            <h2 class="font-800">No Committees Found.</h2>
        </div>
    @endif

    @if (Auth::user()->university_id == 2)
        <!-- Event Settings -->
        <h1 class="full-underline content-divider" id="eventsettings">Event Settings</h1>
        <fieldset class="content-divider-short">
            <label for="action-registration-status"><b>Registration Status</b> (Current: {{($event->opened) ? 'Enabled' : 'Disabled'}})</label><br>
            <select name="action-registration-status" id="action-registration-status">
                <option value="-1">Unchanged</option>
                <option value="enabled">Enabled</option>
                <option value="disabled">Disabled</option>
            </select><br>
            <label for="action-attendance-status"><b>Attendance Status</b> (Current: {{($event->attendance_opened) ? 'Enabled' : 'Disabled'}})</label><br>
            <select name="action-attendance-status" id="action-attendance-status">
                <option value="-1">Unchanged</option>
                <option value="enabled">Enabled</option>
                <option value="disabled">Disabled</option>
            </select><br>
            <label for="action-attendance-type"><b>Attendance Type</b> (Curent: {{($event->attendance_is_exit) ? 'Exit' : 'Entrance'}})</label><br>
            <select name="action-attendance-type" id="action-attendance-type">
                <option value="-1">Unchanged</option>
                <option value="entrance">Entrance</option>
                <option value="exit">Exit</option>
            </select><br>
            <label for="action-update-name"><b>New Event Name</b> (Current: {{$event->name}})</label><br>
            <input type="text" id="action-update-name" name="action-update-name"><br>
            <label for="action-update-location"><b>New Location Name</b> (Current: {{$event->location}})</label><br>
            <input type="text" id="action-update-location" name="action-update-location"><br>
            <label for="action-update-link"><b>New URL Link</b> (Current: {{$event->url_link}})</label><br>
            <input type="text" id="action-update-link" name="action-update-link"><br>
            <label for="action-update-seats"><b>New Seat Count</b> (Current: {{$event->currentseats}}/{{$event->seats}})</label><br>
            <input type="number" id="action-update-seats" name="action-update-seats"><br>
            <label for="action-update-eventtoken"><b>New Event Token</b> (Current: {{$event->totp_key}})</label><br>
            <input type="text" id="action-update-eventtoken" name="action-update-eventtoken"><br>
        </fieldset>
        <div class="text-center content-divider-short">
            <button class="btn button button-gradient" type="submit">Save</button>
        </div>
    @endif
</form>

<script src="/js/vendor/qrcode.min.js"></script>
{{-- @if (session('register'))
    <script>
        document.getElementById("event_id").value = {{session('register')}};
        $('document').ready(function (){
            $('#register').modal("show");
        })
    </script>
    <?php
        Session::forget("register");
    ?>
@endif --}}
@endsection
