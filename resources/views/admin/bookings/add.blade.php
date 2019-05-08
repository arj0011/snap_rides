@extends('admin.layouts.app')
@section('title', 'Add Booking')
@section('breadcrumb')
<h1>Add Booking</h1>
  <ol class="breadcrumb">
    <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
    <li><a href="{{route('booking/bookings') }}">Booking</a></li>
    <li class="active">Add Booking</li>
  </ol>
@endsection
<?php //print_r($drivers);print_r($pickuplatlng);die;?>
@section('content')   
  <div id="page-wrapper">
    @if (Session::has('msg'))
      <div class="row alert-row">
        <div class="col-md-12">
            <div  class="alert alert-{{ Session::get('color') }} alert-custome">
               <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
               <p>{{ Session::get('msg') }}</p>
            </div>
        </div>
      </div>
    @endif

    <div class="row">
      <div class="col-md-12">
      
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab">Tab 1</a></li>
              <li><a href="#tab_2" id="tab2" data-toggle="tab">Tab 2</a></li>
            </ul>  

      <div class="tab-content" style="height: 70%;">
            
            {{-- Tab 1 --}}
            <div class="tab-pane active" id="tab_1">
                <form data-toggle="validator" role="form" action="{{ route('booking/store') }}" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  
                  <div class="col-lg-6">
                    
                    <div class="form-group">
                      <label>Mobile No.</label><span class="star">&nbsp;*</span>
                      <input  name = 'mobile' class="form-control {{ $errors->has('mobile') ? ' is-invalid' : '' }}" value="8109856885" placeholder="Mobile no." onkeypress="return isNumberKey(event);" data-required-error="please enter mobile number" data-error="min 9 and max 11 digit required" data-minlength="9" maxlength="11"  required>
                       @if ($errors->has('mobile'))
                          <span class="star  help-block">
                              <strong>{{ $errors->first('mobile') }}</strong>
                          </span>
                       @endif
                      <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                      <label>Name</label><span class="star">&nbsp;*</span>
                      <input name = 'name' class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" placeholder="Name" onkeypress="return isAlphaKey(event);" data-required-error="please enter  name" data-error="please enter minimum two charecter name" data-minlength="2"  required>
                       @if ($errors->has('name'))
                          <span class="star help-block">
                              <strong>{{ $errors->first('name') }}</strong>
                          </span>
                       @endif
                       <div class="help-block with-errors"></div>
                    </div>
                              
                    <div class="form-group" id="pickupDiv">
                        <label>Pickup Location</label><span class="star">&nbsp;*</span>
                        <input  name = 'pickup_loc' class="form-control {{ $errors->has('pickup_loc') ? ' is-invalid' : '' }}" value="{{ old('pickup_loc') }}" id="autocomplete1" placeholder="Pickup Location" data-required-error="please enter pickup location"  required>
                        <input id="pickup_lat" name="pickup_lat" type="hidden" value="" />
                        <input id="pickup_long" name="pickup_long" type="hidden" value="" />

                         @if ($errors->has('pickup_loc'))
                            <span class="star  help-block">
                                <strong>{{ $errors->first('pickup_loc') }}</strong>
                            </span>
                         @endif
                        <div class="help-block with-errors"></div>
                    </div>

                    <div class="form-group" id="dropDiv">
                      <label>Drop Location</label><span class="star">&nbsp;*</span>
                      <input name = 'drop_loc' id="autocomplete2" class="form-control {{ $errors->has('drop_loc') ? ' is-invalid' : '' }}" value="{{ old('drop_loc') }}" placeholder="Drop Location" data-required-error="please enter drop location" required>
                      <input id="drop_lat" name="drop_lat" value="" type="hidden"/>
                      <input id="drop_long" name="drop_long" value="" type="hidden"/>
                       @if ($errors->has('drop_loc'))
                          <span class="star help-block">
                              <strong>{{ $errors->first('drop_loc') }}</strong>
                          </span>
                       @endif
                       <div class="help-block with-errors"></div>
                    </div>

                    <div class="form-group" style="margin-top: 28px;">
                     <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                     <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Add">Add</button>
                    </div>
                  
                  </div>
                  
                  <div class="col-lg-6">
                    
                    <div class="form-group">
                      <label>Vehicle Type</label><span class="star">&nbsp;*</span>
                      <select name = 'vehicle_type' class="form-control {{ $errors->has('vehicle_type') ? ' is-invalid' : '' }}" data-required-error="please select vehicle type" required>
                        <option value="">Select</option>
                        <option value="1">Sedan</option>
                        <option value="2">7 Seater</option>
                        <option value="3">Delux</option>
                      </select>
                        @if($errors->has('booking_time'))
                          <span class="star help-block">
                              <strong>{{ $errors->first('booking_time') }}</strong>
                          </span>
                       @endif
                       <div class="help-block with-errors"></div>
                    </div>  

                    <div class="form-group">
                      <label>Booking Date</label><span class="star">&nbsp;*</span>
                      <input type="date" id="datetimepicker1" name = 'booking_date' class="form-control {{ $errors->has('booking_date') ? ' is-invalid' : '' }}" value="{{ old('booking_date') }}" placeholder="Booking Date" data-required-error="please enter booking date" required>

                       @if ($errors->has('booking_date'))
                          <span class="star help-block">
                              <strong>{{ $errors->first('booking_date') }}</strong>
                          </span>
                       @endif
                       <div class="help-block with-errors"></div>
                    </div>

                    <div class="form-group">
                      <label>Time Picker:</label><span class="star">&nbsp;*</span>
                      <div class="input-group bootstrap-timepicker timepicker">
                        <input id="timepicker1" name="booking_time" type="text" class="form-control input-small" data-required-error="please enter booking date" required >
                        <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                      </div>
                      @if ($errors->has('booking_date'))
                          <span class="star help-block">
                              <strong>{{ $errors->first('booking_date') }}</strong>
                          </span>
                       @endif
                       <div class="help-block with-errors"></div>
                    </div>

                  </div>
              </form>
              </div>
            {{-- Tab 1 End--}}


            {{-- Tab 2 --}}
            <div class="tab-pane" id="tab_2">
            </div>
            {{-- Tab 2 End--}}    
          
          </div> {{--Tab Content --}}
          </div> {{--nav-tabs Tab Content --}}

    </div>{{--col-md-12 --}}
    </div>{{--Row --}}
  </div> {{--Page wrapper --}}
@endsection

<link rel="stylesheet" href="{{ asset('Admin/css/bootstrap-timepicker.min.css')}}">

@section('css-script')
@endsection     
  
<!-- AutoComplete Address-->
<!-- End -->
<script type="text/javascript" src="{{ asset('Admin/js/moment.min.js')}}"></script>

@section('js-script')
  <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('Admin/js/jquery.validate.min.js') }}"></script>

  <!-- bootstrap time picker -->
  <script src="{{ asset('Admin/js/bootstrap-timepicker.min.js')}}"></script>

  <script type="text/javascript">
    
    //Timepicker
    $('#timepicker1').timepicker({
      showInputs: false
    });

    $(document).ready(function(){
      
    });

    var autocomplete1,autocomplete2;
    function initAutocomplete() 
    {
      // Create the autocomplete object, restricting the search predictions to
      // geographical location types.
      autocomplete1 = new google.maps.places.Autocomplete(
        document.getElementById('autocomplete1'), {types: ['geocode']});
        geocoder = new google.maps.Geocoder();
        autocomplete1.addListener('place_changed', function(){
          input1 = document.getElementById('autocomplete1');
          geocodeAddress(input1,geocoder);  
        });

      autocomplete2 = new google.maps.places.Autocomplete(
        document.getElementById('autocomplete2'), { types: [ 'geocode' ] });
        google.maps.event.addListener(autocomplete2, 'place_changed', function() {
          input2 = document.getElementById('autocomplete2');
        geocodeAddress(input2,geocoder);
      });

    }

    function geocodeAddress(input,geocoder)
    {
      // var address = document.getElementById('autocomplete').value;
      var address = input.value;
      console.log(address);
      geocoder.geocode({'address': address}, function(results, status) {
        if (status === 'OK') {
          console.log(results[0].geometry.location.lat()); 
          console.log(results[0].geometry.location.lng()); 
        
          var divid = input.closest('div').id;
          console.log(divid);
          if(divid == "pickupDiv"){
            document.getElementById('pickup_lat').value = results[0].geometry.location.lat();
            document.getElementById('pickup_long').value = results[0].geometry.location.lng();
          }else if(divid == "dropDiv"){
            document.getElementById('drop_lat').value = results[0].geometry.location.lat();
            document.getElementById('drop_long').value = results[0].geometry.location.lng();
          }
              
        } else {
          alert('Geocode was not successful for the following reason: ' + status);
        }
      });
    }
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCT9TNyKIIhR5o4VO4byUy9RIe9f8EQV0M&libraries=places&callback=initAutocomplete"
        async defer></script>
@endsection