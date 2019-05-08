@extends('admin.layouts.app')
@section('title', 'Payments')
@section('breadcrumb')
      <h1>
       Payments
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Payments</li>
      </ol>
@endsection
@section('content')   
  <div id="page-wrapper">
     <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
              <div class="box-body">
            <form class="form-inline" action="{{  route('payment/search') }}" method="get" autocomplete="off">
              {{ csrf_field() }}
                
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
                
                   <input name="q" type="text" style="vertical-align: bottom;" class="form-control" value="@if(isset($q)){{ $q }} @endif" id="textInput">
                  
                <div class="form-group" style="vertical-align: bottom;">
                     <button type="submit" class="btn btn-flat" data-toggle="tooltip" title="Search">Search</button>
                    <a href="{{ route('payment/payments') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
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
            {{-- <h3 class="box-title"><b>Payment Filter</b> --}}
               {{-- <form action="{{ route('payment/payments') }}" method="get" role="form" style="display: inline-block;margin-top: -5px;margin-bottom: -18px;">
                <div class="form-group">
                  <select class="form-control" name="filter" onchange='if(this.value != 0) { this.form.submit(); }'>
                    
                    <option @isset ($filter) @if($filter == 'all') {{ "selected" }} @endif @endisset value="all">All</option>
                    <option @isset ($filter) @if($filter == 'completed') {{ "selected" }} @endif @endisset value="completed">Completed</option>
                    <option @isset ($filter) @if($filter == 'in_progress') {{ "selected" }} @endif  @endisset value="in_progress">Running</option>
                    <option @isset ($filter) @if($filter == 'canceled') {{ "selected" }} @endif @endisset value="canceled">Canceled</option>
                  </select>
                </div>
               </form> --}}
            </h3>
            <div class="box-tools">
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
                      <th nowrap>Amount</th>
                      <th nowrap>Payment Status</th>
                      <th nowrap>Payment date</th>
                    </tr>
                    </thead>
                    <tbody>
                      @forelse ($payments as $payment)
                        <tr>
                          
                <td>{{  $i++  }}</td>
                <td>{{ 'BKID'.$payment->booking_id }}</td>
                <td>
                    <a href="{{ route('driver/show',['id' => encrypt($payment->driver_id) ]) }}">{{ $payment->driver_name }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('rider/show',['id' => encrypt($payment->rider_id) ]) }}">{{ $payment->rider_name }}
                    </a>
                </td>
                <td>{{ (!empty($payment->mobile) ? $payment->mobile : '' ) }}</td>
                <td>{{ $payment->amount }}</td>
                  <?php
                  if($payment->payment_status == 0){
                    $payment_status = 'Pending';  
                  }
                  elseif($payment->payment_status == 1){
                    $payment_status = 'Complete';  
                  }
                  elseif($payment->payment_status == 2){
                    $payment_status = 'Failed'; 
                  }  
                  ?>
                  <td>{{ $payment_status }}</td>
                <td>{{date('D, d-M-Y', strtotime($payment->created_at))}} at {{date('h:i A',strtotime($payment->created_at))}}</td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="6">
                             No any payment record available
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
            {{ $payments->appends(request()->query())->links()  }}
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
  
  
  @isset ($p)
    @if ($p == 'booking' || $p == 'from_date')
      $('#textInput').datepicker({format:'yyyy-mm-dd'});
    @endif
  @endisset

  </script>
@endsection
