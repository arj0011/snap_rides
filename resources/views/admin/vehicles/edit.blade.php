@extends('admin.layouts.app')
@section('title', 'Edit Vehicle')
@section('breadcrumb')
      <h1>
        Edit Vehicle
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('driver/drivers') }}">Vehicles</a></li>
        <li class="active">Edit Vehicle</li>
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
          <form data-toggle="validator" role="form" action="{{ route('vehicle/update') }}" method="post" enctype="multipart/form-data">
           {{ csrf_field() }}
           {{ method_field('PUT') }}
           <input type="hidden" name="id" value="{{ encrypt($vehicle->id) }}">
           <input type="hidden" name="redirects_to" value="{{ URL::previous() }}">
            <div class="col-md-6">
              <div class="form-group">
                <label>Type</label><span class="star">&nbsp;*</span>
                <select name ='vehicle_category' class="form-control" data-required-error="please select vehicle type"  required>
                   @if (!empty($vehicle_categories) && $vehicle_categories != Null )
                    @foreach ($vehicle_categories as $vehicle_category)
                      <option @if ($vehicle_category->id == $vehicle->vehicle_category)
                        {{ "selected" }}
                      @endif value="{{ $vehicle_category->id }}">{{ $vehicle_category->name }}</option>
                    @endforeach
                   @else
                      <option value="">No any vehicle type available</option>
                   @endif
                </select>
                 @if ($errors->has('vehicle_type'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_type') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div> 
              </div>

                <div class="form-group">
                  <label>Vehicle Name</label><span class="star">&nbsp;*</span>
                  <input name = 'vehicle_name' class="form-control" value="{{ $vehicle->vehicle_name }}" data-required-error="please enter vehicle name"  required>
                   @if ($errors->has('vehicle_name'))
                      <span class="star help-block">
                          <strong>{{ $errors->first('vehicle_name') }}</strong>
                      </span>
                   @endif
                   <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                <label>Registration No.</label><span class="star">&nbsp;*</span>
                <input name = 'registration_number' class="form-control" value="{{ $vehicle->registration_number }}" data-required-error="please enter registration number"  required>
                 @if ($errors->has('registration_number'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('registration_number') }}</strong>
                    </span>
                 @endif
                  <div class="help-block with-errors"></div> 
                </div>

                <div class="form-group">
                <label>Insurance No.</label><span class="star">&nbsp;*</span>
                <input name = 'insurance_number' class="form-control" value="{{ $vehicle->insurance_number }}" data-required-error="please enter insurance number"  required>
                 @if ($errors->has('insurance_number'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('insurance_number') }}</strong>
                    </span>
                 @endif
                  <div class="help-block with-errors"></div> 
                </div>

               <div class="form-group">
                <label>Color</label><span class="star">&nbsp;*</span>
                <input type="color" name="color" class="form-control" value="{{ $vehicle->color }}" data-required-error="please choose vehicle color"  value="" required> 
                 @if ($errors->has('color'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('color') }}</strong>
                    </span>
                 @endif
                  <div class="help-block with-errors"></div> 
              </div>

              <div class="form-group">
                <label>Driver</label><span class="star">&nbsp;*</span>
                <select name ='vehicle_driver' class="form-control" data-required-error="please select driver"  required>
                   @if (!empty($drivers) && $drivers != Null )
                     <option value="">-- Select driver --</option>
                    @foreach ($drivers as $driver)
                      <option @if ($driver->id == $vehicle->driver_id)
                        {{ 'selected' }}
                      @endif value="{{ $driver->id }}">{{ $driver->driver_name }}</option>
                    @endforeach
                   @else
                      <option value="">No any driver available</option>
                   @endif
                </select>
                 @if ($errors->has('vehicle_driver'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_driver') }}</strong>
                    </span>
                 @endif
                  <div class="help-block with-errors"></div> 
              </div>

               <div class="form-group">
                <label>Vehicle Image&nbsp;</label>
                <input type="file" name = 'vehicle_image' id='files' class="form-control" multiple>
                 @if ($errors->has('vehicle_image'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_image') }}</strong>
                    </span>
                 @endif
                 <input type="hidden" name="old_vehicle_image" value="{{ $vehicle->vehicle_image }}">
              </div>
              <div id="image_preview"></div>
              <div class="form-group">
                <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Add Vehicle">Update</button>
              </div>
            </div>
          </form>
       </div>
      </div>
      <!-- /.box -->
    </div>
  </div>
    
@endsection
@section('css-script')
  <!-- validatoin css file -->
  <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
  <style type="text/css">
   input[type="file"] {
  display: block;
}
.imageThumb {
  max-height: 75px;
  width: 150px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
.pip {
  display: inline-block;
  margin: 10px 10px 0 0;
}
.remove {
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove:hover {
  background: white;
  color: black;
}
  </style>
@endsection
@section('js-script')
  <!-- bootstrap validation script -->
  <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function(e) {
      var files = e.target.files,
        filesLength = files.length;
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          $("<span class=\"pip\">" +
            "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
            "<br/><span class=\"remove\">Remove image</span>" +
            "</span>").insertAfter("#files");
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
          });
        });
        fileReader.readAsDataURL(f);
      }
    });
  } else {
    alert("Your browser doesn't support to File API")
  }
});

  @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 1500); 
  @endif
  
</script>
@endsection
