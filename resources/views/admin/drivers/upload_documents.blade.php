@extends('admin.layouts.app')
@section('title', 'Upload Documents')
@section('breadcrumb')
      <h1>
        Upload Documents
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('driver/drivers') }}">Drivers</a></li>
        <li class="active">Upload Documents</li>
      </ol>
@endsection
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
              <li><a href="#" data-toggle="">General Information</a></li>
              <li ><a href="#" data-toggle="">Vehicle</a></li>
              <li class="active"><a href="#tab_3" data-toggle="tab">Documents</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane" id="tab_1">
              </div> 
              <div class="tab-pane" id="tab_2">
              </div>
               <div class="tab-pane active" id="tab_3">
                     <form data-toggle="validator" role="form" action="{{ route('driver/upload-documents') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="driver_id" value="{{ Request::input('id') }}">
              <div class="form-group">
                          <label>ID Proof</label><span class="star">&nbsp;*</span><small>&nbsp;Except Driving Licence</small>
                          <input type="file" name = 'id_proof' class="form-control" data-error="please upload ID proof" id="id_proof" required>
                           @if ($errors->has('id_proof'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('id_proof') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>Driving Licence</label><span class="star">&nbsp;*</span>
                          <input type="file" name = 'driving_licence' class="form-control" data-error="please upload driving Licence" id="driving_licence" data-required-error="please upload driving licence" required>
                           @if ($errors->has('driving_licence'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('driving_licence') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>vehicle Registration</label><span class="star">&nbsp;*</span>
                          <input type="file" name = 'vehicle_registration' class="form-control" data-error="please upload vehicle registration" id="vehicle_registration" data-required-error="please upload vehicle registration" value="{{ old('vehicle_registration') }}" required>
                           @if ($errors->has('vehicle_registration'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('vehicle_registration') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>Vehicle Insurance</label><span class="star">&nbsp;*</span>
                          <input type="file" name = 'vehicle_insurance' class="form-control" data-error="please upload driving Licence" id="vehicle_insurance" data-required-error="please upload vehicle insurance" value="{{ old('vehicle_insurance') }}" required>
                           @if ($errors->has('vehicle_insurance'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('vehicle_insurance') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                          <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Add Driver">Add</button>
                        </div>
            </div>
          </form>
              </div> 
            </div>
       </div><!-- col-md-12-->
     </div><!-- row -->

 {{--  <div class="row">
    <div class="col-md-6">
  <!-- Default box -->
      <div class="box">
        <div class="box-body">
          <form data-toggle="validator" role="form" action="{{ route('driver/upload-documents') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ encrypt($id) }}">
              <div class="form-group">
                          <label>ID Proof</label><span class="star">&nbsp;*</span><small>&nbsp;Except Driving Licence</small>
                          <input type="file" name = 'id_proof' class="form-control" data-error="please upload ID proof" id="id_proof" required>
                           @if ($errors->has('id_proof'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('id_proof') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>Driving Licence</label><span class="star">&nbsp;*</span>
                          <input type="file" name = 'driving_licence' class="form-control" data-error="please upload driving Licence" id="driving_licence" data-required-error="please upload driving licence" required>
                           @if ($errors->has('driving_licence'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('driving_licence') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>vehicle Registration</label><span class="star">&nbsp;*</span>
                          <input type="file" name = 'vehicle_registration' class="form-control" data-error="please upload vehicle registration" id="vehicle_registration" data-required-error="please upload vehicle registration" value="{{ old('vehicle_registration') }}" required>
                           @if ($errors->has('vehicle_registration'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('vehicle_registration') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>Vehicle Insurance</label><span class="star">&nbsp;*</span>
                          <input type="file" name = 'vehicle_insurance' class="form-control" data-error="please upload driving Licence" id="vehicle_insurance" data-required-error="please upload vehicle insurance" value="{{ old('vehicle_insurance') }}" required>
                           @if ($errors->has('vehicle_insurance'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('vehicle_insurance') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                          <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Add Driver">Add</button>
                        </div>
            </div>
          </form>
       </div>
      </div>
      <!-- /.box -->
    </div>
  </div>  --}}
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