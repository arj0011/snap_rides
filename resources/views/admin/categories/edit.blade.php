@extends('admin.layouts.app')
@section('title', 'Edit Vehicle Category')
@section('breadcrumb')
      <h1>
        Edit Vehicle Category
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('category/categories') }}">Vehicle Categories</a></li>
        <li class="active">Edit Vehicle Category</li>
      </ol>
@endsection
@section('content')   
<div id="page-wrapper">
  <!-- Default box -->
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
      <div class="box">
        <div class="box-body">
          <form data-toggle="validator" role="form" action="{{ route('category/update') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <input type="hidden"  name="id"  value="{{ encrypt($category->id) }}">
            <input type="hidden" name="redirects_to" value="{{ URL::previous() }}">
            <div class="col-md-6">
               <div class="form-group">
                <label>Vehicle Type</label><span class="star">&nbsp;*</span>
                <input name='vehicle_name' id="type" class="form-control" value="{{ $category->vehicle_name }}" placeholder="Vehicle Type" data-required-error="please enter vehicle type"  required>
                 @if ($errors->has('vehicle_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

               <div class="form-group">
                <label>Person Capacity</label><span class="star">&nbsp;*</span>
                <input name='vehicle_person_capacity' id="capacity" class="form-control" value="{{ $category->vehicle_person_capacity }}" placeholder="Vehicle Person Capacity" onkeypress="return isNumberKey(event);" data-required-error="please enter person capacity"  required>
                 @if ($errors->has('vehicle_person_capacity'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_person_capacity') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

               {{-- <div class="form-group">
                <label>Base Fare</label><span class="star">&nbsp;*</span>
                <input  name='vehicle_basefare' id="basefare" class="form-control" value="{{ $category->vehicle_basefare  }}" placeholder="Vehicle Base fare" data-required-error="please enter base fare"  required>
                 @if ($errors->has('vehicle_basefare'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_basefare') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div> --}}
              
              <div class="form-group">
                  <label>Per kilometer charges</label>
                  <input type="text" value="{{ $category->per_km_charges }}" name="per_km_charges" class="form-control" required >
                     @if ($errors->has('per_km_charges'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('per_km_charges') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
              </div>
           

              <div class="form-group">
                  <label>Status</label><br>
                     <label class="radio-inline">
                      <input @if ( $category->category_status == '1')
                       {{ 'checked' }}
                      @endif type="radio" name="category_status" value="1" checked>Active
                    </label>
                    <label class="radio-inline">
                      <input @if ( $category->category_status == '0')
                         {{ 'checked' }}
                      @endif type="radio" name="category_status" value="0">Deactive
                    </label>
                   @if ($errors->has('category_status'))
                      <span class="star help-block">
                          <strong>{{ $errors->first('category_status') }}</strong>
                      </span>
                   @endif
              </div>

                <div class="form-group">
                  <div class="col-md-6" style="padding-left: 0px">
                    <label>Category Image&nbsp;</label>
                    <input type="file" name = 'category_image' id="imgInp"  class="form-control" >
                  </div>
                  
                  <div class="col-md-6 text-center">
                             <img style="height: 70px; width: 70px; border: 1px solid gray;text-align: center;" id="blah" @if($category->image)
                                src="{{  asset('Admin/categoryImage/'.$category->image) }}"
                               @else
                                src="#"
                              @endif  alt="your image" class="thumbnail" height="100px" width="100%" /> 
                  </div>
                 @if ($errors->has('category_image'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('category_image') }}</strong>
                    </span>
                 @endif
              </div>

                <div class="form-group"> 
                 <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                  <button type="submit" class="btn  btn-flat btn-primary">Update</button>
                </div>
            </div>
          </form>
       </div>
      </div>
        </div>
    </div>
      <!-- /.box -->
      </div>
@endsection
@section('css-script')
   <!-- bootstrap toggle css -->
   <link rel="stylesheet" type="text/css" href="{{  asset('Admin/css/bootstrap-toggle.css') }}">
    <!-- validatoin css file -->
   <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
     <!--  bootstrap  slider css file -->
   <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/slider.css') }}">
@endsection
@section('js-script')
   <!-- bootstrap toggle script -->
   <script type="text/javascript"  src="{{ asset('Admin/js/bootstrap-toggle.js') }}"></script>
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
   <!-- bootstrap slider js script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrap-slider.js') }}"></script>
   <script type="text/javascript">
   @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
   @endif

     $(function () {
        /* BOOTSTRAP SLIDER */
        $('.slider').slider()
      })

    function readURL(input) {
     if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#blah').attr('src', e.target.result);
    }
        reader.readAsDataURL(input.files[0]);
      }
    }

    $("#imgInp").change(function() {
      readURL(this);
    });
</script> 
@endsection