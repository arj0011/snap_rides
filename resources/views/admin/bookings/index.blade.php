@extends('admin.layouts.app')
@section('title', 'Drivers')
@section('breadcrumb')
      <h1>
       Bookings
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Bookings</li>
      </ol>
@endsection
@section('content')   
  <div id="page-wrapper">
     <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
              <div class="box-body">
            <form class="form-inline" action="{{  route('booking/search') }}" method="get" autocomplete="off">
              {{ csrf_field() }}
                <div class="form-group">
                  <label for="bookingType" class="">Booking Type</label><br>
                  <select class="form-control" name="type" onchange='if(this.value != 0) { this.form.submit(); }' id="bookingType">
                    <option @isset ($type) @if($type == '') {{ "selected" }} @endif @endisset value="all">All</option>
                    <option @isset ($type) @if($type == 'normal') {{ "selected" }} @endif @endisset value="normal">Normal</option>
                    <option @isset ($type) @if($type == 'schedule') {{ "selected" }} @endif @endisset value="schedule">Schedule</option>
                  </select>
                </div>  

                {{-- <div class="input-group input-group-xs"> --}}
                  <div class="form-group">
                    <label for="selectInput" class="">Search By</label><br>
                    <select name="p" class="form-control" id="selectInput">
                      <option value="">All</option>
                      <option  @isset ($p) @if($p == 'id') {{ "selected" }} @endif @endisset value="id">Booking Id</option>
                      <option  @isset ($p) @if($p == 'driver') {{ "selected" }} @endif @endisset value="driver">Driver Name</option>
                      <option  @isset ($p) @if($p == 'customer') {{ "selected" }} @endif @endisset value="customer">Customer Name</option>
                      <option  @isset ($p) @if($p == 'booking') {{ "selected" }} @endif @endisset value="booking">Booking date</option>
                       <option @isset ($p) @if($p == 'from_date') {{ "selected" }} @endif @endisset value="from_date">From date</option>
                    </select>
                  </div>
                
                   <input name="q" type="text" style="vertical-align: bottom;"  class="form-control" value="@if(isset($q)){{ $q }} @endif" id="textInput">
                {{-- </div> --}}
                
                <div class="form-group" style="vertical-align: bottom;">
                     <button type="submit" class="btn btn-flat" data-toggle="tooltip" title="Search">Search</button>
                    <a href="{{ route('booking/bookings') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
                </div>
            </form>
         
              </div>
              <!-- /.box-body -->
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>  
     </div>


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
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title"><b>Bookings Filter</b>
               <form action="{{ route('booking/bookings') }}" method="get" role="form" style="display: inline-block;margin-top: -5px;margin-bottom: -18px;">
                <div class="form-group">
                  <select class="form-control" name="filter" onchange='if(this.value != 0) { this.form.submit(); }'>
                    
                    <option @isset ($filter) @if($filter == 'all') {{ "selected" }} @endif @endisset value="all">All</option>
                    <option @isset ($filter) @if($filter == 'completed') {{ "selected" }} @endif @endisset value="completed">Completed</option>
                    <option @isset ($filter) @if($filter == 'in_progress') {{ "selected" }} @endif  @endisset value="in_progress">Running</option>
                    <option @isset ($filter) @if($filter == 'canceled') {{ "selected" }} @endif @endisset value="canceled">Canceled</option>
                    <option @isset ($filter) @if($filter == 'accept') {{ "selected" }} @endif @endisset value="accept">Accept</option>
                    <option @isset ($filter) @if($filter == 'payment_pending') {{ "selected" }} @endif @endisset value="payment_pending">Payment Pending</option>
                  </select>
                </div>
               </form>
            </h3>
            <div class="box-tools">
                 <!--  <a href="{{ route('driver/create') }}" type="submit" class="btn btn-primary btn-flat" data-toggle="tooltip" title="Add New"><i class="fa fa-plus"></i>&nbsp;Add</a> -->
            </div>
          </div>
          <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                  <table class="table table-hover">
                    <thead style="background:#F7F7F7;">
                    <tr>
                      <th>Sr.</th>
                      <th nowrap>Booking Id</th>
                      <th nowrap><a href="#">Driver Name</a></th>
                      <th nowrap><a href="#">Customer Name</a></th>
                      <th nowrap>Customer Mobile</a></th>
                      <th nowrap>Vehicle Type</th>
                      <th nowrap>Status</th>
                      <th nowrap>Booking Date & Time</th>
                      <th nowrap>Booking Type</th>
                      <th nowrap>Total Amount</th>
                      {{-- <th nowrap>Discount</th> --}}
                      {{-- <th nowrap>Tax </th> --}}
                      <th nowrap>Final Amount</th>
                      @can('invoice', App\Booking::class)
                        <th nowrap>Invoice</th>
                      @endcan
                      @can('delete' , App\Booking::class)
                        <th nowrap>Action</th>
                      @endcan
                    </tr>
                    </thead>
                    <tbody>
                      @forelse ($bookings as $booking)
                        <tr>
                          <td>{{  $i++  }}</td>
                          <td>{{ 'BKID'.$booking->id }}</td>
                          <td>
                               <a href="{{ route('driver/show',['id' => encrypt($booking->driver_id) ]) }}">{{ $booking->driver_name }}
                              </a>
                          </td>
                          <td>
                              <a href="{{ route('rider/show',['id' => encrypt($booking->rider_id) ]) }}">{{ $booking->rider_name }}
                              </a>
                          </td>
                          <td>{{ (!empty($booking->mobile) ? $booking->mobile : '' ) }}</td>
                          <td>{{ (!empty($booking->vehicle_name) ? $booking->vehicle_name : '' ) }}</td>
                          <td>
                          @if($booking->booking_status == 'in_progress')
                            Running
                          @elseif($booking->booking_status == 'payment_pending')
                            Payment Pending
                          @else
                            {{ $booking->booking_status }}
                          @endif
                          </td>
                          <td nowrap> {{date('D, d-M-Y', strtotime($booking->booking_time))}} at {{date('h:i A',strtotime($booking->booking_time))}}</td>
                          <td>{{ ($booking->schedule_booking == 1) ? 'Schedule' : 'Normal' }}</td>
                          <td>{{ $booking->total_amount }}</td>
                          {{-- <td>{{ $booking->discount_amount }}</td> --}}
                          {{-- <td>{{ $booking->tax_amount }}</td> --}}
                          <td>{{ $booking->final_amount }}</td>
                      @can( 'invoice' , App\Booking::class)
                          <td>
                            <a href="{{  route('booking/invoice',['id' => encrypt($booking->id) ]) }}" class="btn  btn-block btn-xs btn-info btn-flat">Invoice</a>
                          </td>
                      @endcan

                      @can( 'delete', App\Booking::class)
                          <td>
                          <a data-toggle="tooltip"  class="btn btn-flat btn-xs btn-danger" title="delete Booking" href="javascript:confirmDelete('{{ route('booking/destroy',['id' => encrypt($booking->id)]) }}')"><i class="fa fa-trash"></i></a>
                          </td>
                      @endcan
                        </tr>
                        @empty
                        <tr>
                          <td colspan="6">
                             No any trip record available
                          </td>
                        </tr>
                     @endforelse
                    </tbody>
                  </table>
                </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div><!-- col-xs-12 -->
       <div clasw="col-md-6">
            {{ $bookings->appends(request()->query())->links()  }}
     </div>
   </div>
 </div>
@endsection
@section('css-script')
   <link href="{{ asset('Admin/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection
@section('js-script')
    <script src="{{ asset('Admin/js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript">
     $('#selectInput').change(function(){
         var x = $(this).val();
             if( x == 'booking' || x == 'from_date'){
                 $('#textInput').datepicker({format:'yyyy-mm-dd'});
             }else{
                 $('#textInput').datepicker("destroy");
                 $('#textInput').attr('type' , 'text');
             }
     });
     $('#selectInput').click(function(){
         var x = $(this).val();
             if( x == 'booking' || x == 'from_date'){
                 $('#textInput').datepicker({format:'yyyy-mm-dd'});
             }else{
                 $('#textInput').datepicker("destroy");
                 $('#textInput').attr('type' , 'text');
             }
     });
     
    $('#searchReset').click(function(){
                 $('#selectInput option:eq(1)');
                 $('#textInput').attr('value' , '');
     });

  $('#searchReset').click(function(){
    $('#textInput').attr('value' , '');
  });
  
  @can( 'delete' , App\Booking::class)
    function confirmDelete(delUrl) {
      console.log(delUrl);
            if (confirm("Are you sure you want to delete this booking?")) {
                document.location = delUrl;
        }
    }
  @endcan

  @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 1500); 
  @endif
 
  @isset ($p)
    @if ($p == 'booking' || $p == 'from_date')
            $('#textInput').datepicker({format:'yyyy-mm-dd'});
    @endif
  @endisset

  </script>
@endsection
