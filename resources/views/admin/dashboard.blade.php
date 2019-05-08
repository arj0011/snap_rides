@extends('admin.layouts.app')
@section('content')   
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

    @can('index',App\Booking::class)
        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3>{{ $data['complitedTrip'] }}</h3>

                <p>Completed Trips</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="{{ route('booking/bookings',['filter' => 'completed']) }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
              <div class="inner">
                <h3>{{ $data['acceptTrip'] }}</h3>

                <p>Accept Trips</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="{{ route('booking/bookings',['filter' => 'accept']) }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3>{{ $data['runningTrip'] }}</h3>

                <p>Running Trips</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{ route('booking/bookings',['filter' => 'in_progress']) }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
              <div class="inner">
                <h3>{{ $data['cancelTrip'] }}</h3>

                <p>Canceled Trips</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="{{ route('booking/bookings',['filter' => 'canceled']) }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
       </div>
     @endcan

      <div class="row">
        <div class="col-md-6">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Recent Bookings</h3>
              <div class="box-tools">
                    {{-- <a href="{{ route('booking/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a> --}}
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-condensed">
                <thead>
                  <tr>
                    <th>Booking Id</th>
                    <th>Rider</th>
                    <th>Rider Mobile</th>
                    <th>Driver</th>
                    <th>Booking Date</th>
                    <th>Booking Type</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                @forelse ($data['bookings'] as $booking)
                  <tr>
                    <td>BKID{{ $booking->id }}</td>
                    <td>{{ $booking->customer_name }}</td>
                    <td>{{ $booking->mobile }}</td>
                    <td>{{ $booking->driver_name }}</td>
                    <td>{{date('d-M', strtotime($booking->booking_time))}} {{date('h:i A',strtotime($booking->booking_time))}}</td>
                    @if($booking->schedule_booking == 0)
                      <td>Normal</td>
                    @elseif($booking->schedule_booking == 1)
                      <td>Schedule</td>
                    @endif 
                      @if ($booking->booking_status == 'completed')
                    <td><span class="label label-success">Completed</span></td>
                       @elseif($booking->booking_status == 'in_progress')
                    <td><span class="label label-primary">In Progress</span></td> 
                      @elseif($booking->booking_status == 'canceled')
                    <td><span class="label label-danger">Canceled</span></td> 
                      @elseif($booking->booking_status == 'accept')
                    <td><span class="label label-warning">Accept</span></td> 
                    @elseif($booking->booking_status == 'payment_pending')
                    <td><span class="label label-warning">Payment Pending</span></td> 
                       @else
                    <td><span class="label label-warning">Pending</span></td>
                       @endif  
                  </tr>
                @empty
                  <tr>
                    <td>No any recent bookings available</td>
                    </tr>
                    </tbody>
                @endforelse
              </table>
            </div>
          <!-- /.box-body -->
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Recent Drivers</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-condensed">
               <thead>
                <tr>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($data['drivers'] as $driver)
                  <tr>
                    <td>{{ $driver->name }}</td>
                    <td>{{ $driver->mobile }}</td>
                    <td>{{ $driver->email }}</td>
                    <td><span class="label label-success">Active</span></td>
                  </tr>
                @empty
                  <tr>
                    <td>No any recent drivers available</td>
                  </tr>
                @endforelse
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
        </div>
        </div>
     </div>
@endsection
@section('js-script')
  <!-- response  message alert script -->
  @if (Session::has('msg'))
   <script type="text/javascript">
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 1500); 
   </script>
  @endif
@endsection




