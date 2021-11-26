@extends('layouts.app')

@section('content')
<div class="container">
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
    <h1 class="full-underline">Competition Pages</h1><br><h5 style="text-align: center"><b>Your Team Name:</b> {{$requests[0]->team_name}} ({{$teamid}})</h5>
    {{-- <p class="h4 text-center content-divider-short">Once the documents have been approved, you will eligible for participating in:</p> --}}
    <div class="row justify-content-center content-divider-short">
        <div class="col-md-8 p-0 row justify-content-center">
        @foreach ($requests as $request)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <b>Ticket No:</b> {{$request->id}}
                    </div>
                    <div class="card-body">
                        <h4 class="font-700">{{$request->event_name}}</h4>
                        <h5>for <b>{{$request->user_name}}</b></h5>
                        <b>Role:</b> {{$request->remarks}}<br>
                        <b>Uploaded: </b>
                        @if($request->file_id != null && $request->file_id != '' && $request->answer_path != null)
                             <a href="/user/downloadFile/1/{{$requests[0]->team_id}}/{{$request->file_id}}" target="_blank">Download File</a>
                        @else
                            No Uploaded File
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        </div>

        <div class="col-md-8">
            <h1 class="full-underline content-divider-short">Instructions</h1>
            <div class="card content-divider-short">
                <div class="text-center">
                    @foreach ($requests as $item)
                        @if ($item->event_id == 3 && $item->ticket_id == Auth::user()->id)
                            <h1 class="m-2">Business-IT Case - Elimination Round</h1>
                            <a href="/user/downloadFile/cp/{{$teamid}}" class="btn btn-dark m-3">download PDF</a>
                        @elseif ($item->event_id == 4 && $item->ticket_id == Auth::user()->id)
                            <h1 class="m-2">Web Design - Elimination Round</h1>
                            <a href="/user/downloadFile/cp/{{$teamid}}" class="btn btn-dark m-3">download PDF</a>
                        @elseif ($item->event_id == 5 && $item->ticket_id == Auth::user()->id)
                            <h1 class="m-2">Business-IT Case - Final Round</h1>
                            <a href="/user/downloadFile/cp/{{$teamid}}" class="btn btn-dark m-3">download PDF</a>
                        @elseif ($item->event_id == 6 && $item->ticket_id == Auth::user()->id)
                            <h1 class="m-2">Web Design - Final Round</h1>
                            <a href="/user/downloadFile/cp/{{$teamid}}" class="btn btn-dark m-3">download PDF</a>
                        @endif
                    @endforeach
                </div>
            </div>

            <h1 class="full-underline content-divider-short">Upload Answer</h1>
            <div class="card content-divider-short">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        @foreach ($requests as $item)
                            @if($item->ticket_id == Auth::user()->id)
                                <div class="form-group row">
                                    <label for="file" class="col-md-4 col-form-label text-md-right">File (JPG, PNG, PDF, or ZIP)</label>
                                    <div class="col-md-6">
                                        <input id="file" type="file" class="form-control @error('file') is-invalid @enderror" name="file" value="{{ old('file') }}" required accept="application/zip,image/*,application/pdf">

                                        @error('file')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <p class="red-text h5 text-center">You will be able to upload files before the due date. If you wish to reupload, your previous uploads will be overriden!</p>

                                <div class="form-group text-center">
                                    <button type="submit" class="button button-gradient">
                                        Upload
                                    </button>
                                </div>
                            @elseif($item->status >= 2 && $item->ticket_id == Auth::user()->id)
                                <p class="red-text h5 text-center">Your Team already Accepted</p>
                            @endif
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
