<?php $event = DB::table('events')->where('id', $event_id)->first() ?>
<div class="row margin-1 justify-content-center">
  @if($event)
    <?php $registration_valid = true; ?>
    <div class="col-md-6">
      <div class="card my-3">
        <div class="card-img-top p-3 text-center button-gradient">
          <p class="h4 m-0 fw-bold">Before you register...</p>
        </div>
        <div class="card-body">
          <p><b>Please kindly fill in the following information</b> to {{ $additional_fields_purpose ?? 'be eligible to participate in our event' }}</p>
          <form action="/changeaccountdetails" method="post">
            @csrf
            @foreach($additional_fields as $field)
              <?php
                $field['value'] = DB::table('user_properties')->where('user_id', Auth::user()->id)->where('field_id', $field['type'])->first();
                $field['name'] = DB::table('fields')->where('id', $field['type'])->first()->name;
                if (!$field['value']) $registration_valid = false;
              ?>
              <div class="mb-3">
                <label for="action-change-{{ $field['type'] }}" class="form-label">{{ $field['name'] }} <span class="text-danger">*</span></label>
                @if (isset($field['choices']))
                  <select class="form-select" aria-label="Default select example" name="action-change-{{ str_replace('.', '_', $field['type']) }}" id="action-change-{{ $field['type'] }}" required>
                    @foreach($field['choices'] as $choice)
                      <option value="{{ $choice['value'] }} @if(isset($field['value']->value) && $field['value']->value == $choice['value']) selected @endif">{{ $choice['label'] }}</option>
                    @endforeach
                  </select>
                @else
                  <input type="text" class="form-control" name="action-change-{{ str_replace('.', '_', $field['type']) }}" id="action-change-{{ $field['type'] }}" value="{{ $field['value']->value ?? '' }}" required>
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
    <div class="col-md-6">
      <div class="card my-3">
        <div class="card-img-top p-3 text-center button-gradient-2">
          <p class="h4 m-0 fw-bold">Pay and Register</p>
        </div>
        <div class="card-body">
          @if(isset($price_packages))
            <p class="h4 fw-bold text-center">How many participants?</p>
            @foreach($price_packages as $pax)
              <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="package" id="package-{{ $pax['participants'] }}" value="{{ $pax['participants'] }}">
                <label class="form-check-label" for="package-{{ $pax['participants'] }}" value="{{ $pax['participants'] }}">
                  {{ $pax['participants'] }} pax: Rp. {{ $pax['price'] }}
                </label>
              </div>
            @endforeach
            <p class="text-center">You will be redirected to our payment form to upload your payment receipt.</p>
          @else
            <p class="h2 fw-bold text-center mb-0">Price: FREE</p>
          @endif
          <p class="fw-bold text-center">By registering and participating into this event, you agree to our rules and regulations.</p>
          <div class="text-center">
            <button type="submit" class="button button-gradient-2">REGISTER</button>
          </div>
        </div>
      </div>
    </div>
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
