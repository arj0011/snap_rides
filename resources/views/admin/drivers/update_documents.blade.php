@extends('admin.layouts.app')
@section('title', 'Update Documents')
@section('breadcrumb')
      <h1>
        Update Documents
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('driver/drivers') }}">Drivers</a></li>
        <li class="active">Update Documents</li>
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
    <div class="col-md-6">
  <!-- Default box -->
      <div class="box">
        <div class="box-body">
          <form data-toggle="validator" role="form" action="{{ route('driver/update-documents') }}" method="post" enctype="multipart/form-data" method="PUT">
            {{ csrf_field() }}
               {{ method_field('PUT') }}
            <input type="hidden" name="id" value="{{ encrypt($driver->id) }}">
                <div class="form-group">
                          <label>ID Proof</label><span class="star">&nbsp;*</span><small>&nbsp;Except Driving Licence</small>
                          <input type="file" name = 'id_proof' class="form-control" data-error="please upload ID proof" id="id_proof">
                          <input type="hidden" name="old_id_proof" value="{{ $driver->id_proof }}">
                           @if ($errors->has('id_proof'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('id_proof') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>Driving Licence</label><span class="star">&nbsp;*</span>
                          <input type="file" name = 'driving_licence' class="form-control" data-error="please upload driving Licence" id="driving_licence" data-required-error="please upload driving licence">
                          <input type="hidden" name="old_driving_licence" value="{{ $driver->driving_licence }}">
                           @if ($errors->has('driving_licence'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('driving_licence') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>vehicle Registration</label><span class="star">&nbsp;*</span>
                          <input type="file" name = 'vehicle_registration' class="form-control" data-error="please upload vehicle registration" id="vehicle_registration" data-required-error="please upload vehicle registration" value="{{ old('vehicle_registration') }}">
                          <input type="hidden" name="old_vehicle_registration" value="{{ $driver->vehicle_registration }}">
                           @if ($errors->has('vehicle_registration'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('vehicle_registration') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>Vehicle Insurance</label><span class="star">&nbsp;*</span>
                          <input type="file" name = 'vehicle_insurance' class="form-control" data-error="please upload driving Licence" id="vehicle_insurance" data-required-error="please upload vehicle insurance" value="{{ old('vehicle_insurance') }}">
                          <input type="hidden" name="old_vehicle_insurance" value="{{ $driver->vehicle_insurance }}">
                           @if ($errors->has('vehicle_insurance'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('vehicle_insurance') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                          <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Add Driver">Update</button>
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
 <!-- validatoin css file -->
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