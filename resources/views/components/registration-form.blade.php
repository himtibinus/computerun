<?php $event = DB::table('events')->where('id', $event_id)->first() ?>
<div class="row margin-1 justify-content-center">
  @if($event)
    <?php
      $registration_valid = true;
      $registration_list = DB::table('registration')->where('ticket_id', Auth::user()->id)->where('event_id', $event_id)->get();
    ?>
    @component('components.status-badge')
    @endcomponent
    @if(count($registration_list) == 0)
      @if($event->opened)
        @if(isset($additional_fields))
          <div class="col-md-6">
            <div class="card my-3">
              <div class="card-img-top p-3 text-center button-gradient">
                <p class="h4 m-0 fw-bold">Before you register...</p>
              </div>
              <div class="card-body">
                <p><b>Please kindly fill in the following information</b> to {{ $additional_fields_purpose ?? 'be eligible to participate in our event' }}</p>
                <form action="/changeaccountdetails" method="post">
                  @csrf
                  <input type="hidden" name="redirect_to" value="{{ $form_redirect_to }}">
                  @foreach($additional_fields as $field)
                    <?php
                      $field['value'] = DB::table('user_properties')->where('user_id', Auth::user()->id)->where('field_id', $field['type'])->first();
                      $field['name'] = DB::table('fields')->where('id', $field['type'])->first()->name;
                      if (!$field['value']) $registration_valid = false;
                      $required = !(isset($field['optional']) && $field['optional'] == true);
                    ?>
                    <div class="mb-3">
                      <label for="action-change-{{ $field['type'] }}" class="form-label">{{ $field['name'] }}<span class="text-danger">*</span></label>
                      @if (isset($field['choices']))
                        <select class="form-select" aria-label="Default select example" name="action-change-{{ str_replace('.', '_', $field['type']) }}" id="action-change-{{ $field['type'] }}" @if($required) required @endif>
                          @foreach($field['choices'] as $choice)
                            <option value="{{ $choice['value'] }}" @if(isset($field['value']->value) && $field['value']->value == $choice['value']) selected @endif>{{ $choice['label'] ?? $choice['value'] }}</option>
                          @endforeach
                        </select>
                      @else
                        <input type="text" class="form-control" name="action-change-{{ str_replace('.', '_', $field['type']) }}" id="action-change-{{ $field['type'] }}" value="{{ $field['value']->value ?? '' }}" @if($required) required @endif>
                      @endif
                    </div>
                  @endforeach
                  <div class="text-center">
                    <button type="submit" class="button button-gradient">Save Details</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endif
        <div class="col-md-6">
          <div class="card my-3">
            <div class="card-img-top p-3 text-center button-gradient-2">
              <p class="h4 m-0 fw-bold">@if(isset($price_packages) || isset($price)) Pay and @endif Register</p>
            </div>
            <form class="card-body" action="/registerevent" method="post">
              @csrf
              <input type="hidden" name="event_id" value="{{ $event_id }}">
              <input type="hidden" name="redirect_to" value="{{ $form_redirect_to }}">
              @if(isset($price_packages))
                <p class="h4 fw-bold text-center">How many participants?</p>
                @foreach($price_packages as $pax)
                  <div class="form-check mb-3" onclick="provideFields({{ $pax['participants'] }}, {{ $pax['reserve'] ?? 'false' }})">
                    <input class="form-check-input" type="radio" name="package" id="package-{{ $pax['participants'] }}" value="{{ $pax['participants'] }}">
                    <label class="form-check-label" for="package-{{ $pax['participants'] }}" value="{{ $pax['participants'] }}">
                      {{ $pax['participants'] }} pax: Rp. {{ $pax['price'] }}
                    </label>
                  </div>
                @endforeach
                <div id="additionalParticipants"></div>
                <p class="text-center">You will be redirected to our payment form to upload your payment receipt.</p>
                <div class="text-center" id="submit-validation"></div>
              @else
                <p class="h2 fw-bold text-center mb-0">Price: @if(isset($price))Rp {{ $price }} @else FREE @endif</p>
                <div class="text-center">
                  <div class="text-center"><b class="red-text">By registering to this competition, you agree to our rules and regulations.<br></b><button type="submit" class="button button-gradient-2 content-divider-short" onclick="this.form.submit();this.setAttribute('disabled','disabled');">Submit</button></div>
                </div>
              @endif
            </form>
          </div>
        </div>
        <script>
          var fieldContainer = document.getElementById("additionalParticipants");
          fieldContainer.innerHTML = "";
          var isMemberValid = [];
          var isReserveMemberValid = [];
          provideFields(document.getElementById);
          function provideFields(number, reserve){
            if (Number.isNaN(number)) return;
            isMemberValid = [];
            isReserveMemberValid = [];
            var draft = "";

            // Show that the event requires team forming
            draft += '<input type="hidden" name="create_team" value="true">';

            // Create the "Team Details" heading
            draft += '<h3 class="content-divider-short full-underline">Team Details</h3>';

            // Show "Team Name" field
            draft += '<div class="form-group mb-3"><label for="team_name">Team Name<b class="red-text">*</b></label><input type="text" class="form-control" name="team_name" id="team_name" @if(!isset($use_random_team_name) || $use_random_team_name !== false) value="Team #{{ uniqid() }}" @endif required></div>'

            // Create dummy "Team Leader"
            draft += '<div class="form-group mb-3"><label for="team_leader">Team Leader<b class="red-text">*</b></label><input type="text" class="form-control" id="team_leader" disabled value="{{Auth::user()->email}}"></div>'

            // Show respective "Team Members" field
            var i;
            for (i = 0; i < number - 1; i++){
              if (reserve == true) isReserveMemberValid[i] = false;
              else isMemberValid[i] = false;
              draft += '<div class="form-group mb-3"><label for="team_member_' + (reserve == true ? 'reserve_' : '' ) + (i + 1) + '">Team Member ' + (i + 1) + '\'s registered Email Address<b class="red-text">*</b></label><input type="email" class="form-control" name="team_member_' + (reserve == true ? 'reserve_' : '' ) + (i + 1) + '" id="team_member_' + (reserve == true ? 'reserve_' : '' ) + (i + 1) + '" required onChange=\'validateUser("team_member_' + (reserve == true ? 'reserve_' : '' ) + (i + 1) + '")\'><span role="alert" id="team_member_' + (reserve == true ? 'reserve_' : '' ) + (i + 1) + '_validation" style="display: none"></span></div>'
            }
            fieldContainer.innerHTML = draft;
            validateRegistration();
          }

          var radio = document.getElementsByName("package");
          var radioValue = 1;
          var i;
          for (i = 0; i < radio.length; i++){
            if (radio[i].checked){
              radioValue = radio[i].value;
            }
          }

          provideFields(radioValue)

          function validateUser(input){
            var selected = document.getElementById(input).value;
            var xhr = new XMLHttpRequest();
            var params = JSON.stringify({ email: selected, allowSelf: false,  eventId: {{ $event_id }} });
            xhr.open("POST", "/getuserdetails");
            xhr.setRequestHeader("X-CSRF-TOKEN", "{!! csrf_token() !!}");
            xhr.setRequestHeader("Content-type", "application/json; charset=utf-8");
            // xhr.setRequestHeader("Content-length", params.length);
            // xhr.setRequestHeader("Connection", "close");
            xhr.onload = function() {
              if (xhr.status != 200) {
                setErrorMessage(input, 'Error ' + xhr.status + ': ' + xhr.statusText);
              } else {
                // Check whether the output is JSON
                try {
                  var json = JSON.parse(xhr.responseText);
                  // Check if the JSON data displays an error
                  if (json.error) throw json.error;
                  // Check whether game account is required
                  console.log(json);
                  if (json.incomplete.length > 0 || json.invalid.length > 0){
                    var errorDraft = "The participant you are looking currently ";
                    var j;
                    if (json.incomplete.length > 0){
                      errorDraft += "has not set his/her";
                      for (j = 0; j < json.incomplete.length; j++) errorDraft += " " + json.incomplete[j];
                    }
                    if (json.incomplete.length * json.invalid.length === 0) errorDraft += " and ";
                    if (json.invalid.length > 0){
                      errorDraft += "has invalid";
                      for (j = 0; j < json.invalid.length; j++) errorDraft += " " + json.invalid[j];
                    }
                    errorDraft += ".";
                    throw errorDraft;
                  } else {
                    // Send to UI
                    setSuccessMessage(input, "User Found: " + json.name, selected);
                    validateRegistration();
                  }
                } catch (e) {
                  setErrorMessage(input, 'Error: ' + e);
                  validateRegistration();
                }
              }
            };
            xhr.send(params);
          }
          function setErrorMessage(input, message){
            var match = input.match(/team_member_([1-9][0-9]*)/);
            if (match && match[1]){
              isMemberValid[match[1] - 1] = false;
            }
            match = input.match(/team_member_reserve_([1-9][0-9]*)/)
            if (match && match[1]){
              isReserveMemberValid[match[1] - 1] = false;
            }
            var element = document.getElementById(input + "_validation");
            element.style.display = "block";
            element.style.color = "#ff0000";
            element.innerHTML = "<strong>" + message + "</strong>";
            validateRegistration();
          }
          function setSuccessMessage(input, message, selected){
            var match = input.match(/team_member_([1-9][0-9]*)/);
            if (match && match[1]){
              isMemberValid[match[1] - 1] = selected;
            }
            match = input.match(/team_member_reserve_([1-9][0-9]*)/)
            if (match && match[1]){
              isReserveMemberValid[match[1] - 1] = selected;
            }
            var element = document.getElementById(input + "_validation");
            element.style.display = "block";
            element.style.color = "#249ef2";
            element.innerHTML = "<strong>" + message + "</strong>";
            validateRegistration();
          }
          function validateRegistration(){
            var emails = isMemberValid.concat(isReserveMemberValid);
            var i, invalidMembers = 0;
            for (i = 0; i < emails.length; i++){
              if (emails[i] === false) invalidMembers++;
            }
            var emailSet = new Set(emails);
            if (invalidMembers > 0){
              document.getElementById("submit-validation").innerHTML = '<div class="alert alert-danger">All member details should be added.</div>';
            } else if (emails.length > emailSet.size){
              document.getElementById("submit-validation").innerHTML = '<div class="alert alert-danger">Error: No duplicate emails allowed.</div>';
            } else {
              document.getElementById("submit-validation").innerHTML = `<div class="text-center"><b class="red-text">By registering to this competition, you agree to our rules and regulations.<br></b><button type="submit" class="button button-gradient-2 content-divider-short" onclick="this.form.submit();this.setAttribute('disabled','disabled');">Submit</button></div>`;
            }
          }
        </script>
      @else
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <p class="mb-0">Registrations for this event is currently closed.</p>
            </div>
          </div>
        </div>
      @endif
    @else
      @foreach($registration_list as $registration)
        <div class="col-md-6">
          @if($registration->status >= 2 && strlen($event->description_private) > 0)
            <div class="card mb-4">
              <div class="card-header h4 bg-info text-white">
                <i class="bi bi-info-circle"></i> Important Information
              </div>
              <div class="card-body text-dark">
                {!! (new Parsedown())->text($event->description_private) !!}
              </div>
            </div>
          @elseif(strlen($event->description_pending) > 0)
            <div class="card mb-4">
              <div class="card-header h4 bg-info text-white">
                <i class="bi bi-info-circle"></i> Important Information
              </div>
              <div class="card-body text-dark">
                {!! (new Parsedown())->text($event->description_pending) !!}
              </div>
            </div>
          @endif
          <div class="card mb-4">
            <div class="card-header h4 bg-primary text-white">
              <i class="bi bi-card-heading"></i> Ticket #{{ $registration->id }}
            </div>
            <div class="card-body text-dark">
              <p class="card-title">
                <b>Status:</b>
                @switch($registration->status)
                  @case(0)
                    Pending
                    @break
                  @case(1)
                    Rejected
                    @break
                  @case(2)
                    Accepted
                    @break
                  @case(3)
                    Cancelled
                    @break
                  @case(4)
                    Attending
                    @break
                  @case(5)
                    Attended
                    @break
                  @default
                    Unknown ({{ $registration->status }})
                @endswitch
                @if(strlen($registration->remarks) > 0)
                  <br>
                  <b>Remarks: </b> {{ $registration->remarks }}
                @endif
                @if(strlen($registration->team_id) > 0)
                  <br>
                  <b>Team: </b> {{ DB::table('teams')->where('id', $registration->team_id)->first()->name }} <b>(Team ID: {{ $registration->team_id }})</b>
                @endif
                @if(strlen($registration->payment_code) > 0)
                  <br>
                  <b>Payment Code: </b> {{ $registration->payment_code }}</b>
                @endif
              </p>
              @if($registration->status < 2 && strlen($registration->payment_code) > 0)
                <div class="btn-toolbar" role="toolbar">
                  <div class="btn-group mr-2" role="group">
                    @if(strlen($event->payment_link) > 0)
                      <a class="btn btn-primary text-white" href="{{ App\Http\Controllers\EventController::getPaymentLink($event, $registration) }}">
                    @else
                      <a class="btn btn-primary text-white" href="/pay/{{ $registration->payment_code }}">
                    @endif
                      <i class="bi bi-credit-card"></i> Pay
                    </a>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    @endif
  @else
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <p class="mb-0">Error: Event ID not found</p>
        </div>
      </div>
    </div>
  @endif
</div>
