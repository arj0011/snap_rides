@extends('admin.layouts.app')
@section('title', 'Drivers')
@section('breadcrumb')
      <h1>
       Trip  {{$invoice->id}}
      </h1>

      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Trip Invoice </li>
      </ol>
@endsection
@section('content')   
   <div id="page-wrapper">
     <div class="row">
        <div class="col-md-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Customer Information</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-bordered clsinvoice">
                <tr>
                 
                  <th>Customer Name</th>
                  <th>{{ $invoice->rider_name }}</th>
                  
                </tr>
                 <tr>
                 
                  <th>Mobile No.</th>
                  <th>{{ $invoice->rider_mobile }}</th>
                  
                </tr>

                
       
              </table>
            </div>
            <!-- /.box-body -->
           <br /> 
          </div>
          <!-- /.box -->

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Diver Information</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
             <table class="table table-bordered clsinvoice">
                <tr>
                 
                  <th>Diver Name</th>
                  <th>{{ $invoice->driver_name }}</th>
                  
                </tr>
                 <tr>
                 
                  <th>Mobile No.</th>
                  <th>{{ $invoice->driver_mobile }}</th>
                  
                </tr>

                
       
              </table>
            </div>
            <!-- /.box-body -->
            <br /> <br />
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-6">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Invoice Detail</h3>

              <div class="box-tools">
                
                  <h4 class="box-title">Date:&nbsp;  <td nowrap> {{date('D, d-M-Y', strtotime($invoice->booking_time))}} at {{date('h:i A',strtotime($invoice->booking_time))}}</td></h4> 
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <table class="table clsinvoice">
                            <tr>
                            <th width="30%" >Trip Distance </th>
                            <td width="70%" style="text-align: right;">{{ $invoice->actual_distance }}</td>
                            </tr>
                            <tr>
                            <th> Base Fare</th>
                             <td width="70%" style="text-align: right;">R {{ $invoice->basefare }}</td>
                            </tr>

                            <tr>
                            <th> Esitimated Distance</th>
                             <td width="70%" style="text-align: right;">{{ $invoice->estimated_distance }}</td>
                            </tr>

                            <tr>
                            <th  > Per Km Charges</th>
                             <td width="70%" style="text-align: right;">R {{ $invoice->per_km_charges }}</td>
                            </tr>

                            <tr>
                            <th > Total Amount</th>
                             <td width="70%" style="text-align: right;">R {{ $invoice->total_amount }} </td>
                            </tr>

                            <tr>
                            <th  > Discount Amount</th>
                             <td width="70%" style="text-align: right;">R {{ $invoice->discount_amount }}</td>
                            </tr>

                            <tr>
                            <th  > Tax</th>
                            <td width="70%" style="text-align: right;">R {{ $invoice->tax_amount }}</td>
                            </tr>
                             <tr>
                            <th   style="border-top: 1px solid black;"> Final Payable Amount</th>
                             <td width="70%" style="text-align: right;border-top: 1px solid black;">R {{ $invoice->final_amount }}</td>
                            </tr>
              </table>
            </div> 
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

   
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-xs-8">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"> Trip Route </h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <div id="map"></div>  
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <div class="col-xs-4">
       <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Ride Detail's</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-bordered clsinvoice">
                <tr>
                  <th>Pickup Point</th>
                  <td>{{ $invoice->pickup_addrees}}</td>
                  
                </tr>
       
               <tr>
                 
                  <th>Drop Point</th>
                  <td>{{ $invoice->destination_address }}</td>

                </tr>
                <!--  <tr>  
                  <th>Booking Time</th>
                  <td>{{date('d-M-y', strtotime($invoice->booking_time))}} {{date('h:i A',strtotime($invoice->booking_time))}}</td>
                  
                </tr> -->

                <tr>
                  <th>Pickup Time</th>
                  <td>{{date('d-M-y', strtotime($invoice->start_time))}} {{date('h:i A',strtotime($invoice->start_time))}}</td>                  
                </tr>

                 
                 <tr>
                   <th>Drop Time</th>
                  <td>{{date('d-M-y', strtotime($invoice->completed_time))}} {{date('h:i A',strtotime($invoice->completed_time))}}</td>                   
                 </tr>

                 <tr>
                   <th>Booking Status</th>
                  <td>
                    @if($invoice->booking_status == 'in_progress')
                      Running
                    @elseif($invoice->booking_status == 'payment_pending')
                      Payment Pending
                    @else
                      {{ $invoice->booking_status }}
                    @endif</td>                   
                 </tr>
                 
                 
       
              </table>
            </div>
            <!-- /.box-body -->
           <br /> 
          </div>
        </div>
      </div>
{{-- 
      <div class="row">
         <div class="col-md-9">
             <div id="map"></div>  
         </div>
         <div class="col-md-3">
          <b>Pickup Point:</b></br>
            <p>Vijay Nager , indore</p>
          <b>Drop Point:</b></br>
            <p>Rajwada , indore</p>
         </div>
      </div> --}}
</div>
@endsection
   <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 50%;
        width: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
  
    </style>
 <script>
      function initMap() {
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var directionsService = new google.maps.DirectionsService;
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 7,
          center: {lat:20.5937, lng: -78.9629}
        });
        directionsDisplay.setMap(map);
        
         calculateAndDisplayRoute(directionsService, directionsDisplay);

      }

      function calculateAndDisplayRoute(directionsService, directionsDisplay) {
        var start_lat= "<?php echo $invoice->pickup_lat ?>";
        var start_long = "<?php echo $invoice->pickup_long ?>";
        var end_lat= "<?php echo $invoice->drop_lat ?>";
        var end_long = "<?php echo $invoice->drop_long ?>";
        
        var start = start_lat+","+start_long;
        var end = end_lat+","+end_long;

        /*var start = "22.7196,75.8577";
        var end = "23.2599, 77.4126";*/
        directionsService.route({
          origin: start,
          destination: end,
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFwsRyc4HATOYM5ZjS3kFsKfj4EUoFRqs&callback=initMap">
    </script>