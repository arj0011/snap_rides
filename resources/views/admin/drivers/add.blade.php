@extends('admin.layouts.app')
@section('title', 'Add Driver')
@section('breadcrumb')
      <h1>
        Add Driver
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('driver/drivers') }}">Drivers</a></li>
        <li class="active">Add Driver</li>
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
              <li class="active"><a href="#tab_1" data-toggle="tab">General Information</a></li>
              <li><a href="#" data-toggle="">Vehicle</a></li>
              <li><a href="#" data-toggle="">Documents</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <div class="panel panel-default">
                  <div class="panel-body">
                    <form data-toggle="validator" role="form" action="{{ route('driver/store') }}" method="post" enctype="multipart/form-data">
                      <div class="row">
                        <div class="col-lg-6" >
                          {{ csrf_field() }}
                            <div class="form-group">
                              <label>Name</label><span class="star">&nbsp;*</span>
                              <input name = 'name' class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" placeholder="Name" onkeypress="return isAlphaKey(event);" data-required-error="please enter  name" data-error="please enter minimum two charecter name" data-minlength="2"  required>
                               @if ($errors->has('name'))
                                  <span class="star help-block">
                                      <strong>{{ $errors->first('name') }}</strong>
                                  </span>
                               @endif
                               <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label>Mobile No.</label><span class="star">&nbsp;*</span>
                                <input  name = 'mobile' class="form-control {{ $errors->has('mobile') ? ' is-invalid' : '' }}" value="{{ old('mobile') }}" placeholder="Mobile no." onkeypress="return isNumberKey(event);" data-required-error="please enter mobile number" data-error="min 9 and max 11 digit required" data-minlength="9" maxlength="11"  required>
                                 @if ($errors->has('mobile'))
                                    <span class="star  help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                 @endif
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label>Email</label><span class="star">&nbsp;*</span>
                                <input type="email" name = 'email' class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="email" data-required-error="please enter email address" data-error="please enter valid email address"  required>
                                 @if ($errors->has('email'))
                                    <span class="star help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                 @endif
                                 <div class="help-block with-errors"></div>
                            </div>

                                 <div class="form-group">
                                    <label>Password</label><span class="star">&nbsp;*</span>
                                    <input type="password" name = 'password' class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" value="{{ old('password')}}" placeholder="Password" data-error="min 6 charecters required" data-required-error="please enter password" data-minlength="6"  id="inputPassword"  required >
                                     @if ($errors->has('password'))
                                        <span class="star help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                     @endif
                                     <div class="help-block with-errors"></div>
                                  </div>

                            <div class="form-group">
                                <label>Confirm Password</label><span class="star">&nbsp;*</span>
                                <input type="password" name = 'confirm_password' class="form-control {{ $errors->has('confirm_password') ? ' is-invalid' : '' }}" value="{{ old('confirm_password') }}" placeholder="Confirm password" data-match-error="Whoops, these don't match" data-match="#inputPassword" data-required-error="please enter confirm password"  required>
                                 @if ($errors->has('confirm_password'))
                                    <span class="star help-block">
                                        <strong>{{ $errors->first('confirm_password') }}</strong>
                                    </span>
                                 @endif
                                 <div class="help-block with-errors"></div>
                            </div>
                            
                            <div class="form-group">
                                <label>Date of birth</label><span class="star">&nbsp;</span>
                                <input type="date" name="dob" class="form-control {{ $errors->has('dob') ? ' is-invalid' : '' }}" value="{{ old('dob') }}" placeholder="Date of birth"  data-required-error="please enter date of birth" >
                                 @if ($errors->has('dob'))
                                    <span class="star help-block">
                                        <strong>{{ $errors->first('dob') }}</strong>
                                    </span>
                                 @endif
                                 <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group">
                                <label>Identity Number</label><span class="star">&nbsp;</span>
                                <input type="text" name="identity_no" class="form-control {{ $errors->has('identity_no') ? ' is-invalid' : '' }}" value="{{ old('identity_no') }}" placeholder="Identity number"  data-required-error="please enter identity number">
                                 @if ($errors->has('identity_no'))
                                    <span class="star help-block">
                                        <strong>{{ $errors->first('identity_no') }}</strong>
                                    </span>
                                 @endif
                                 <div class="help-block with-errors"></div>
                            </div>

                          <div class="form-group">
                                <label>Gender</label><span class="star">&nbsp;*</span><br><br>
                                  <div style="margin-top:-5px">
                                     <label class="radio-inline">
                                      <input type="radio" name="gender" value="1" @if (old('gender'))
                                        checked
                                      @endif data-error="please choose gender" required>Male
                                    </label>
                                    <label class="radio-inline">
                                      <input type="radio" name="gender" value="2" @if (old('gender'))
                                        checked
                                      @endif data-error="please choose gender" required>Female
                                    </label>
                                  </div>
                                 @if ($errors->has('gender'))
                                    <span class="star help-block">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                 @endif
                                 <div class="help-block with-errors"></div>
                            </div>  

                          <div class="form-group">
                              <label>Image&nbsp;<small>(Optional)</small></label><br>
                              <input type="file" name = 'profile_image' class="form-control {{ $errors->has('profile_image') ? ' is-invalid' : '' }}">
                               @if ($errors->has('profile_image'))
                                  <span class="star help-block">
                                      <strong>{{ $errors->first('profile_image') }}</strong>
                                  </span>
                               @endif
                               <div class="help-block with-errors"></div>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group">
                           <label>Country</label><span class="star">&nbsp;</span>
                            <select name = 'country' class="form-control" id="country" data-required-error="please select country" onchange="getStates(this.value)"  >
                              <option value="0">Select Country</option>
                              @foreach ($countries as $country)
                                <option @if ($country->id == old('country'))
                                  {{ 'selected ' }}
                                @endif value="{{ $country->id }}">{{  $country->name }}</option>
                              @endforeach
                              
                            </select>
                             @if ($errors->has('country'))
                               <!--  <span class="star help-block">
                                    <strong>{{ $errors->first('country') 
                                    }}
                                    </strong>
                                </span> -->
                             @endif
                             <div class="help-block with-errors"></div>
                          </div>
                          <div class="form-group">
                            <label>Province</label><span class="star">&nbsp;</span>
                            <select name = 'state' class="form-control" id="state" data-required-error="please select province" onchange="getCities(this.value)" >
                              <option value="0">Select Province</option>
                            </select>
                             @if ($errors->has('state'))
                                <span class="star help-block">
                                  <!--   <strong>{{ $errors->first('state') }}</strong> -->
                                </span>
                             @endif
                             <div class="help-block with-errors"></div>
                          </div>
                          <div class="form-group">
                              <label>City</label><span class="star">&nbsp;</span>
                              <select name = 'city' class="form-control" id="city" data-required-error="please select city" >
                                <option value="0">Select City</option>
                              </select>
                               @if ($errors->has('city'))
                                  <span class="star  help-block">
                                   <!--    <strong>{{ $errors->first('city') }}
                                      </strong> -->
                                  </span>
                               @endif
                               <div class="help-block with-errors"></div>
                          </div>
                          <div class="form-group">
                              <label>Address</label><span class="star">&nbsp; </span>
                              <input name = 'address' class="form-control {{ $errors->has('address') ? ' is-invalid' : '' }}" value="{{ old('address') }}" placeholder="address" data-required-error="please enter address"   >
                               @if ($errors->has('address'))
                                  <span class="star  help-block">
                                     <!--  <strong>{{ $errors->first('address') }}</strong> -->
                                  </span>
                               @endif
                               <div class="help-block with-errors"></div>
                          </div>

                          <div class="form-group">
                              <label>Zip Code</label>
                              <input name = 'zcode' class="form-control {{ $errors->has('zcode') ? ' is-invalid' : '' }}" value="{{ old('zcode') }}" placeholder="zipcode" onkeypress="return isNumberKey(event);" data-minlength="4" maxlength="4" data-error="please enter 4 digit zipcode">
                               @if ($errors->has('zcode'))
                                  <span class="star  help-block">
                                      <strong>{{ $errors->first('zcode') }}</strong>
                                  </span>
                               @endif
                               <div class="help-block with-errors"></div>
                          </div>

                          {{-- <div class="form-group">
                                <label>Discount</label>&nbsp; In (&#8377;) Between {{ $discountRange[0] }} To {{ $discountRange[1] }}
                                <input type="number" id="discount" name = 'discount' class="form-control {{ $errors->has('discount') ? ' is-invalid' : '' }}" value="{{ $discountRange[0] }}" placeholder="Discount" onkeypress="return isNumberKey(event);" data-required-error="please provide discount" min="{{ $discountRange[0] }}" max="{{ $discountRange[1] }}">
                                 @if ($errors->has('discount'))
                                    <span class="star  help-block">
                                        <strong>{{ $errors->first('discount') }}</strong>
                                    </span>
                                 @endif
                                <div class="help-block with-errors"></div>
                          </div>

                          <div class="form-group">
                                <label>Allow Discount Before 15 Trips:</label><span>&nbsp;In (Yes/No)</span><br><br>
                                  <div style="margin-top:-5px">
                                     <label class="radio-inline">
                                      <input class="allow_discount" type="radio" name="allow_discount" value="0" @if (old('allow_discount'))
                                        checked
                                      @endif  checked >No
                                    </label>
                                    <label class="radio-inline">
                                      <input class="allow_discount" type="radio" name="allow_discount" value="1" @if (old('allow_discount'))
                                        checked
                                      @endif >Yes
                                    </label>
                                  </div>
                                 @if ($errors->has('allow_discount'))
                                    <span class="star help-block">
                                        <strong>{{ $errors->first('allow_discount') }}</strong>
                                    </span>
                                 @endif
                                 <div class="help-block with-errors"></div>
                            </div> --}}
                     
                          <div class="form-group" style="margin-top: 28px;">
                             <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                             <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Add Vehicle">Add</button>
                          </div>

                        </div>
                      </div><!-- row -->
                    </form>
                  </div><!-- panel-body -->
                </div><!-- panel -->
              </div>
              <div class="tab-pane" id="tab_2">
              </div> 
               <div class="tab-pane" id="tab_3">
              </div> 
            </div>
       </div><!-- col-md-12-->
     </div><!-- row -->
     </div>
@endsection
@section('css-file')
 <!-- validatoin css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
 <style type="text/css">
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
 </style>
@endsection
@section('js-script')
<!-- validator js file -->
  <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('Admin/js/jquery.validate.min.js') }}"></script>
  <script type="text/javascript">

   // profile Image Preview

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
    
  
         $('#country').change(function(){
            var value = $(this).val();
            $.ajax({
               type:'get',
               url:"{{  route('ajax/getStates')}}",
               data: {
                  country: value,
                  '_token': '{{ csrf_token() }}',
              },
             success : function(data){
                  var data = JSON.parse(data);
                  var option = '<option value="0">--- Select State ---</option>';
                  for(var i in data){
                      option += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                  }
                  $("#state").html(option);
              }
          });     
       });
       
        $('#state').change(function(){
           var value = $(this).val();
          $.ajax({
             type:'get',
             url:"{{ route('ajax/getCities')}}",
             data: {
                  state: value,
                  '_token': '{{ csrf_token() }}',
              },
             success : function(data){
                  var require = true;
                  var cities = JSON.parse(data);
                  if(cities.length != 0){
                  var option = '<option value="">--- Select City ---</option>';
                      for(var i in cities){
                          option += '<option value="'+cities[i].id+'">'+cities[i].name+'</option>';
                      }
                  }else{
                             option += '<option value="city">'+$('#state option:selected').text()+'</option>';
                  } 
                  $("#city").html(option);
             }
          });
       }); 
  });  

</script>
@endsection


