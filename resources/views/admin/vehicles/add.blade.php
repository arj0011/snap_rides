@extends('admin.layouts.app')
@section('title', 'Add Vehicle')
@section('breadcrumb')
      <h1>
        Add Vehicle
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('driver/drivers') }}">Vehicles</a></li>
        <li class="active">Add Vehicle</li>
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
              <li class="active"><a href="#tab_2" data-toggle="tab">Vehicle</a></li>
              <li><a href="#" data-toggle="">Documents</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane" id="tab_1">
              </div> 
              <div class="tab-pane active" id="tab_2">
                <div class="panel panel-default">
                  <div class="panel-body">
                       <form  data-toggle="validator" role="form" action="{{ route('vehicle/store') }}" method="post" enctype="multipart/form-data">
           {{ csrf_field() }}
           <input type="hidden" name="vehicle_driver" value="{{ Request::get('id') }} ">
            <div class="col-md-6">
              <div class="form-group">
                <label>Vehicle Type</label><span class="star">&nbsp;*</span>
                  <select name ='vehicle_category' class="form-control" data-required-error="please select vehicle type" id="vehicle_category" required>
                     <option value="">-- Select Vehicle Type --</option>
                     @if (!empty($vehicle_categories) && $vehicle_categories != Null )
                      @foreach ($vehicle_categories as $vehicle_category)
                        <option @if (old('vahicle_category') == $vehicle_category->id)
                          {{ 'selected' }}
                        @endif value="{{ ($vehicle_category->id) }}">{{ $vehicle_category->name }}</option>
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
                  <label>Vehicle Model</label><span class="star">&nbsp;*</span>
                  {{-- <input name = 'vehicle_name' class="form-control" value="{{ old('vehicle_name') }}" data-required-error="please enter vehicle name"  required> --}}
                  <select name = 'vehicle_name' class="form-control" data-required-error="please enter vehicle name"  required>
                   <option value="">-- Select Vehicle Model --</option>
                     @if (!empty($vehicle_model) && $vehicle_model != Null )
                      @foreach ($vehicle_model as $model)
                        <option @if (old('vehicle_name') == $model->name)
                          {{ 'selected' }}
                        @endif value="{{ ($model->name) }}">{{ $model->name }}</option>
                      @endforeach
                     @else
                        <option value="">No any vehicle model available</option>
                     @endif
                  </select>
                   @if ($errors->has('vehicle_name'))
                      <span class="star help-block">
                          <strong>{{ $errors->first('vehicle_name') }}</strong>
                      </span>
                   @endif
                   <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                <label>Registration No.</label><span class="star">&nbsp;*</span>
                <input name = 'registration_number' class="form-control" value="{{ old('registration_number') }}" data-required-error="please enter registration number"  required>
                 @if ($errors->has('registration_number'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('registration_number') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                <label>Insurance No.</label><span class="star">&nbsp;*</span>
                <input name = 'insurance_number' class="form-control" value="{{ old('insurance_number') }}" data-required-error="please enter insurance number"  required>
                 @if ($errors->has('insurance_number'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('insurance_number') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                <label>Year of Car</label><span class="star">&nbsp;</span>
                <select name="car_year" class="form-control" data-required-error="please select year of car">
                  <option>select year</option>
                  @for($i = 2000;$i<=2100;$i++)
                  <option value="{{$i}}" {{($i == old('insurance_number')) ? 'selected' : ''}}>{{ $i }}</option>
                  @endfor
                </select>  
                 @if ($errors->has('car_year'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('car_year') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
                </div>

               <div class="form-group">
                <label>Color</label><span class="star">&nbsp;*</span>
                <input type="color" name="color" class="form-control" data-required-error="please choose vehicle color"  value="" required> 
                 @if ($errors->has('color'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('color') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div> 
              </div>

               <div class="form-group">
                <label>Vehicle Image&nbsp;</label>
                <input type="file" name = 'vehicle_image' id='files' onchange="preview_image();" class="form-control" >
                 @if ($errors->has('vehicle_image'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_image') }}</strong>
                    </span>
                 @endif
              </div>

              {{-- <div class="form-group">
                <label>Per Kilometer charges</label><span class="star">&nbsp;*   </span> In (&#8377;) <small id="charges"></small>
                <input type="number" name = 'per_km_charge' class="form-control" value="{{ old('per_km_charge') }}" data-required-error="please enter per kilometer charges"   min="" max="" id="per_km_charge" required>
                 @if ($errors->has('per_km_charge'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('per_km_charge') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div> --}}


              <div id="image_preview"></div>
              <div class="form-group">
                <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Add Vehicle">Add</button>
              </div>
            </div>
          </form>
                  </div><!-- panel-body -->
                </div><!-- panel -->
              </div>
               <div class="tab-pane" id="tab_3">
              </div> 
            </div>
       </div><!-- col-md-12-->
     </div><!-- row -->
     </div>
  </div>
@endsection
@section('css-script')
  <!-- validatoin css file -->
  <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
    <style>
    input[type="file"] {
    display: block;
    }
    .imageThumb {
    max-height: 75px;
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
   
    // get charges value between
    // $('#vehicle_category').change(function(){
    //    if(this.value != '' && this.value != null){
    //       $.ajax({
    //          type:'get',
    //          url:"{{  route('ajax/getCharges')}}",
    //          data: {
    //               id: $(this).val(),
    //               '_token': '{{ csrf_token() }}',
    //           },
    //          success : function(response){
    //              let data = JSON.parse(response);
    //              let starting = data.data.per_km_charges.split(',')[0];
    //              let  ending   = data.data.per_km_charges.split(',')[1];
    //              let html = 'between '+ starting + '  To '  + ending ;
    //                 $('#charges').html(html);
    //                 $('#per_km_charge').attr('min' , starting);
    //                 $('#per_km_charge').attr('max' , ending);
    //          }
    //       });
    //    }
    // })

  </script>
@endsection

