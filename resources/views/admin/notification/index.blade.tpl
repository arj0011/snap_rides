@extends('admin.layouts.app')
@section('title', 'Setting')
@section('breadcrumb')
      <h1>
        Settings
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Setting</li>
      </ol>
@endsection
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

  <div class="row">
    <div class="col-md-12">
  <!-- Default box -->
      <div class="box">
        <div class="box-body">
          <form data-toggle="validator" role="form" action="{{ route('setting/update') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <div class="col-md-6">
              
              @foreach($settings as $setting)

                @if ( $setting->key == 'base_km' )
                  <div class="form-group">
                    <label>Base Fare kilometer</label><small>&nbsp;In (kilometer)</small>
                    <input name='setting_base_fare_km' onkeypress="return isNumberKey(event);"  class="form-control" value="{{ $setting->value }}" placeholder="Base fare kilometer">
                     @if ($errors->has('setting_base_fare_km'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('setting_base_fare_km') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                  </div>
                @endif
                
                @if ($setting->key == 'nearest_km')
                  <div class="form-group">
                    <label>Nearest Kilometer</label><small>&nbsp;In (kilometer)</small>
                    <input name='setting_nearest_km' class="form-control" value="{{ $setting->value }}" placeholder="Nearest Kilometer" onkeypress="return isNumberKey(event);" data-required-error="please enter nearest kilometer">
                     @if ($errors->has('setting_nearest_km'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('setting_nearest_km') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                  </div>
                @endif
                
                @if ($setting->key == 'driver_request_timeout')
                  <div class="form-group">
                    <label>Request Time Out</label><small>&nbsp;In (H:M)</small>
                    <input type="time" name='setting_time_out' class="form-control" value="{{ $setting->value }}" placeholder="Request time out" data-required-error="please enter base fare">
                     @if ($errors->has('setting_time_out'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('setting_time_out') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                  </div>
                @endif
             
              @endforeach
                  <div class="form-group"> 
                     <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                      <button type="submit" class="btn  btn-flat btn-success">Update</button>
                  </div>

            </div>
          </form>
       </div>
      </div>
      <!-- /.box -->
    </div>
  </div> 
  </div>
@endsection
@section('css-script')
 <!--  bootstrap  validatoin css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
@endsection
@section('js-script')
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
   <script type="text/javascript">
     @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
      @endif

   </script>
@endsection