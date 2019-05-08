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
@section('content')   
  <div id="page-wrapper">
    <?php
     //print_r($categories);die;
      /*print_r($drivers);
      print_r($pickuplatlng);
      print_r($booking_id);
      print_r($booking);die;
      print_r($categories);
      print_r($vehicle_type);
      echo $booking->pickup_addrees;
      echo $booking->pickup_lat;
      echo $booking->pickup_long;
      echo $booking->destination_address;
      echo $booking->drop_lat;
      echo $booking->drop_long;die;*/
    ?>
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
              <li><a href="#tab_1" data-toggle="tab">Tab 1</a></li>
              <li class="active"><a href="#tab_2" id="tab2" data-toggle="tab">Tab 2</a></li>
            </ul>  

      <div class="tab-content" style="height: 70%;">
            
            {{-- Tab 1 --}}
            <div class="tab-pane" id="tab_1">
                <form data-toggle="validator" role="form" action="{{ route('booking/store') }}" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
    
                  <input type="hidden" name="booking_id" value="{{ $booking_id }}">

                  <div class="col-lg-6">
  
                    <div class="form-group">
                      <label>Mobile No.</label><span class="star">&nbsp;*</span>
                      <input  name = 'mobile' value="{{ (isset($booking->mobile) ? $booking->mobile : '') }}" class="form-control {{ $errors->has('mobile') ? ' is-invalid' : '' }}" value="8109856885" placeholder="Mobile no." onkeypress="return isNumberKey(event);" data-required-error="please enter mobile number" data-error="min 9 and max 11 digit required" data-minlength="9" maxlength="11"  required>
                       @if ($errors->has('mobile'))
                          <span class="star  help-block">
                              <strong>{{ $errors->first('mobile') }}</strong>
                          </span>
                       @endif
                      <div class="help-block with-errors"></div>
                    </div>
                     <div class="form-group">
                      <label>Name</label><span class="star">&nbsp;*</span>
                      <input name = 'name' value="{{ (isset($booking->name) ? $booking->name : '') }}" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"  placeholder="Name" onkeypress="return isAlphaKey(event);" data-required-error="please enter  name" data-error="please enter minimum two charecter name" data-minlength="2"  required>
                       @if ($errors->has('name'))
                          <span class="star help-block">
                              <strong>{{ $errors->first('name') }}</strong>
                          </span>
                       @endif
                       <div class="help-block with-errors"></div>
                    </div>
                              
                    <div class="form-group" id="pickupDiv">
                        <label>Pickup Location</label><span class="star">&nbsp;*</span>
                        <input  name = 'pickup_loc' value="{{ (isset($booking->pickup_addrees) ? $booking->pickup_addrees : '') }}" class="form-control {{ $errors->has('pickup_loc') ? ' is-invalid' : '' }}"  id="autocomplete1" placeholder="Pickup Location" data-required-error="please enter pickup location"  required>
                        <input id="pickup_lat" name="pickup_lat" value="{{ (isset($booking->pickup_lat) ? $booking->pickup_lat : '') }}" type="hidden" value="" />
                        <input id="pickup_long" name="pickup_long" value="{{ (isset($booking->pickup_long) ? $booking->pickup_long : '') }}" type="hidden" value="" />

                         @if ($errors->has('pickup_loc'))
                            <span class="star  help-block">
                                <strong>{{ $errors->first('pickup_loc') }}</strong>
                            </span>
                         @endif
                        <div class="help-block with-errors"></div>
                    </div>

                    <div class="form-group" id="dropDiv">
                      <label>Drop Location</label><span class="star">&nbsp;*</span>
                      <input name = 'drop_loc' value="{{ (isset($booking->destination_address) ? $booking->destination_address : '') }}" id="autocomplete2" class="form-control {{ $errors->has('drop_loc') ? ' is-invalid' : '' }}" placeholder="Drop Location" data-required-error="please enter drop location" required>
                      <input id="drop_lat" name="drop_lat" value="{{ (isset($booking->drop_lat) ? $booking->drop_lat : '') }}" type="hidden"/>
                      <input id="drop_long" name="drop_long" value="{{ (isset($booking->drop_long) ? $booking->drop_long : '') }}" type="hidden"/>
                       @if ($errors->has('drop_loc'))
                          <span class="star help-block">
                              <strong>{{ $errors->first('drop_loc') }}</strong>
                          </span>
                       @endif
                       <div class="help-block with-errors"></div>
                    </div>

                    <div class="form-group" style="margin-top: 28px;">
                     <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                     <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Update">Update</button>
                    </div>
                  
                  </div>
                  
                  <div class="col-lg-6">
                    
                    <div class="form-group">
                      <label>Vehicle Type</label><span class="star">&nbsp;*</span>
                      <select name = 'vehicle_type' class="form-control {{ $errors->has('vehicle_type') ? ' is-invalid' : '' }}" data-required-error="please select vehicle type" required>
                        <option value="">Select</option>
                        @foreach($categories as $category)
                          <option value="{{ $category->id }}" {{ ($vehicle_type == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
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
                      <input type="date" id="datetimepicker1" name = 'booking_date' class="form-control {{ $errors->has('booking_date') ? ' is-invalid' : '' }}" value="{{ date('d/m/Y',strtotime($booking->booking_time)) }}" placeholder="Booking Date" data-required-error="please enter booking date" required>

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
                        <input id="timepicker1" value="{{ date('G:i a',strtotime($booking->booking_time)) }}" name="booking_time" type="text" class="form-control input-small" data-required-error="please enter booking date" required >
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
            <div class="tab-pane active" id="tab_2">

              <div class="col-lg-6">
                <div class="box">
                  <div class="box-header">
                    <h3 class="box-title">Driver Tracking</h3>
                  </div>
                  <div class="box-body table-responsive no-padding">
                    <div id="map"></div>        
                  </div>
                  <!-- /.box-body -->
                </div>
                <!-- /.box -->
              </div>

              <div class="col-md-6">
                <div class="box">
                  <div class="box-header">
                    <h3 class="box-title">Available Drivers</h3>
                  </div>
                  <div class="box-body table-responsive no-padding">
                      <table class="table table-hover table-condensed">
                        <thead>
                          <tr>
                            <th>Driver</th>
                            <th>Mobile</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        @forelse ($drivers as $driver)
                          <tr>
                            <td>{{ $driver->name }}</td>
                            <td>{{ $driver->mobile }}</td>
                            <td><a href="">Assign</a></td>
                          </tr>
                        @empty
                          <tr>
                            <td>No drivers available</td>
                            </tr>
                            </tbody>
                        @endforelse
                      </table>  
                  </div>
                  <!-- /.box-body -->
                </div>
                <!-- /.box -->
              </div>


            </div>
            {{-- Tab 2 End--}}    
          
          </div> {{--Tab Content --}}
          </div> {{--nav-tabs Tab Content --}}

    </div>{{--col-md-12 --}}
    </div>{{--Row --}}
  </div> {{--Page wrapper --}}
  @php 
  $base_url = URL::to('/');
  $pickup_lat = -33.927987;
  $pickup_lng = 18.421647;
  if(!empty($pickuplatlng)){
    $pickup_lat = $pickuplatlng['pickup_lat'];
    $pickup_lng = $pickuplatlng['pickup_long'];  
  }
  
  $tab = 1;
  if(!empty($drivers) && !empty($booking_id)){
    $tab = 2;
  }
  @endphp
@endsection
<link rel="stylesheet" href="{{ asset('Admin/css/bootstrap-timepicker.min.css')}}">
@section('css-script')
<style>
/* Always set the map height explicitly to define the size of the div
 * element that contains the map. */
#map {
  height: 85%;
  width: 100%;
}
/* Optional: Makes the sample page fill the window. */
</style>
@endsection     
  <!-- Google Map-->
  <script>
    pickup_lat = {{ $pickup_lat }};
    pickup_lng = {{ $pickup_lng }};

    function initMap() {
      
      var map = new google.maps.Map(document.getElementById('map'), {
        center: new google.maps.LatLng(22.776350, 75.903210),
        zoom: 12
      });
      var infoWindow = new google.maps.InfoWindow;

      var data = '<?php echo json_encode($drivers);?>';
      driver = JSON.parse(data);
        
      for (var i = 0; i < driver.length; i++){ 

        var name = driver[i].name;
        var mobile = driver[i].mobile;
        var type = driver[i].type;
        
        var point = new google.maps.LatLng(
            parseFloat(driver[i].latitude),
            parseFloat(driver[i].longitude)
        );

        var infowincontent = document.createElement('div');
        var strong = document.createElement('strong');
        strong.textContent = name;
        infowincontent.appendChild(strong);
        infowincontent.appendChild(document.createElement('br'));

        var text = document.createElement('text');
        text.textContent = mobile;
        infowincontent.appendChild(text);

        icon_path = '<?php echo $base_url;?>'  + '/Admin/categoryImage/';
        
        var icon = {
          url: icon_path + driver[i].type_img,
          // This marker is 20 pixels wide by 32 pixels high.
          size: new google.maps.Size(100, 60),
          // The origin for this image is (0, 0).
          origin: new google.maps.Point(0, 0),
          // The anchor for this image is the base of the flagpole at (0, 32).
          anchor: new google.maps.Point(0, 40),
          scaledSize: new google.maps.Size(40, 40)
        };

        var marker = new google.maps.Marker({
          map: map,
          position: point,
          icon:icon
        });

        //Attach click event to the marker.
        dataa = driver[i];
        (function (marker, dataa) {
          google.maps.event.addListener(marker, "click", function (e) {
          infoWindow.setContent("<div style = 'width:200px;min-height:40px'>Driver : <b>" + dataa.name + "</b><br />Mobile : <b>"+dataa.mobile+"</b><img scr='"+dataa.vehicle_img+"' height='25' width='25' /><br />Vehicle type : <b>"+dataa.vehicle_type+"</b></div>");
          
          infoWindow.open(map, marker);
          });
        })(marker, dataa);
      }
    }

</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFwsRyc4HATOYM5ZjS3kFsKfj4EUoFRqs&callback=initMap">
</script>
<!-- End-->
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
      tab = {{$tab}};
      if(tab == 2){
        $('#tab2').trigger('click');
      }
    });

</script>
@endsection