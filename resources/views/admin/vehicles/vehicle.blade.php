@extends('admin.layouts.app')
@section('title', 'Vehicle Info')
@section('breadcrumb')
      <h1>
        Vehicle Information
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('vehicle/vehicles') }}">Vehicles</a></li>
        <li class="active">Vehicle Information</li>
      </ol>
@endsection
@section('content')   
  
  @if (Session::has('msg'))
  <div class="row alert-row">
    <div class="col-md-12">
       <div class="box">
         <div class="box-header">
              <div  class="alert alert-{{ Session::get('color') }} alert-custome">
                 <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                 <p>{{ Session::get('msg') }}</p>
              </div>
         </div>
       </div>
    </div>
  </div>
 @endif

   <div class="row">
     <div class="col-md-12">
  <!-- Default box -->
      <div class="box">
        <div class="box-body">
            <div class="box-body box-profile">
              <div class="col-md-2">
                 @if (!empty($vehicle->vehicle_image && file_exists('Admin/vehicleImage/'.$vehicle->vehicle_image)))
                    <img src="{{  asset('Admin/vehicleImage/'.$vehicle->vehicle_image) }}" style="width: 100%; height: 200px">
                @else
                    <img src="{{  asset('Admin/vehicleImage/unknownVehicle.png') }}" style="width: 100%; height: 200px">
                @endif
              </div>
                <div class="col-md-5">
  		          <ul class="list-group list-group-unbordered">
  		            <li class="list-group-item li-border-top">
  		              <b>Vehicle Type</b> <a class="pull-right">{{ $vehicle->vehicle_category }}</a>
  		            </li>
                  <li class="list-group-item">
                    <b>Name</b> <a class="pull-right">{{ $vehicle->vehicle_name }}</a>
                  </li>
  		            <li class="list-group-item">
  		              <b>Registration No.</b> <a class="pull-right">{{ $vehicle->registration_number }}</a>
  		            </li>
  		            <li class="list-group-item">
  		              <b>Insurance No. </b> <a class="pull-right">{{ $vehicle->insurance_number }}</a>
  		            </li>
  		          </ul>
                </div>
                  <div class="col-md-5">
                <ul class="list-group list-group-unbordered">
                  <li class="list-group-item li-border-top">
                    <b>Color</b> <div class="pull-right" style="background: {{ $vehicle->color }} ; width: 60px ; height: 20px;"></div>
                  </li>
                  <li class="list-group-item">
                    <b>Base Fare</b> <a class="pull-right">{{ $vehicle->basefare }}  Per/Km</a>
                  </li>
                  <li class="list-group-item">
                    <b>Driver</b> <a href="{{ route('driver/show' , ['id' => encrypt($vehicle->driver_id)]) }}" class="pull-right">{{ $vehicle->driver_name }}</a>
                  </li>
                  <li class="list-group-item">
                    <b>Registration Date</b><a class="pull-right">{{ date('d-M-y', strtotime($vehicle->registration_date))}}  {{ date('h:i A',strtotime($vehicle->registration_date)) }}</a>
                  </li>
                </ul>
                </div>
            
              </div>
       </div>
      </div>
      <!-- /.box -->
      </div>
   </div>
@endsection
@section('js-script')
 <script type="text/javascript">
  function confirmDelete(delUrl) {
      console.log(delUrl);
            if (confirm("Are you sure you want to delete this vehicle?")) {
                document.location = delUrl;
             }
        }
        
    @if (Session::has('msg'))
          window.setTimeout(function () { 
           $(".alert-row").fadeOut('slow') }, 1500); 
    @endif
 </script>
@endsection


