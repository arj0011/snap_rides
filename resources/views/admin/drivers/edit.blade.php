@extends('admin.layouts.app')
@section('title', 'Edit Driver')
@section('breadcrumb')
      <h1>
        Edit Driver
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('driver/drivers') }}">Drivers</a></li>
        <li class="active">Edit Driver</li>
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
              <li class="active"><a href="#tab_1" data-toggle="tab">General Informatin</a></li>
              <li><a href="#tab_2" data-toggle="tab">vehicle</a></li>
              <li><a href="#tab_3" data-toggle="tab">Documents</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <div class="panel panel-default">
                  <div class="panel-body">
                    <form data-toggle="validator" role="form" action="{{ route('driver/update') }}" method="post" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-lg-6" >
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <input type="hidden" name="id" value="{{ encrypt($driver->id) }}">
                        <input type="hidden" name="redirects_to" value="{{ URL::previous() }}">
                          <div class="form-group">
                            <label>Name</label><span class="star">&nbsp;*</span>
                            <input name = 'name' class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ $driver->name }}" placeholder="Name" onkeypress="return isAlphaKey(event);" data-required-error="please enter  name" data-error="please enter minimum two charecter name" data-minlength="2"  required>
                             @if ($errors->has('name'))
                                <span class="star help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                             @endif
                             <div class="help-block with-errors"></div>
                          </div>
                          <div class="form-group">
                              <label>Modile No.</label><span class="star">&nbsp;*</span>
                              <input  name = 'mobile' class="form-control {{ $errors->has('mobile') ? ' is-invalid' : '' }}" value="{{ $driver->mobile  }}" placeholder="Mobile No." onkeypress="return isNumberKey(event);" data-required-error="please enter mobile number" data-error="min 9 and max 11 digit required" data-minlength="9" maxlength="11"  required>
                               @if ($errors->has('mobile'))
                                  <span class="star  help-block">
                                      <strong>{{ $errors->first('mobile') }}</strong>
                                  </span>
                               @endif
                              <div class="help-block with-errors"></div>
                          </div>
                          <div class="form-group">
                              <label>Email</label><span class="star">&nbsp;*</span>
                              <input type="email" name = 'email' class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ $driver->email }}" placeholder="email" data-required-error="please enter email address" data-error="please enter valid email address"  required>
                               @if ($errors->has('email'))
                                  <span class="star help-block">
                                      <strong>{{ $errors->first('email') }}</strong>
                                  </span>
                               @endif
                               <div class="help-block with-errors"></div>
                          </div>
                          
                          <div class="form-group">
                                <label>Date of birth</label><span class="star">&nbsp;</span>
                                <input type="date" name="dob" class="form-control {{ $errors->has('dob') ? ' is-invalid' : '' }}" value="{{ $driver->dob }}" placeholder="Date of birth"  data-required-error="please enter date of birth" >
                                 @if ($errors->has('dob'))
                                    <span class="star help-block">
                                        <strong>{{ $errors->first('dob') }}</strong>
                                    </span>
                                 @endif
                                 <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group">
                                <label>Identity Number</label><span class="star">&nbsp;</span>
                                <input type="text" name="identity_no" class="form-control {{ $errors->has('identity_no') ? ' is-invalid' : '' }}" value="{{ $driver->identity_no }}" placeholder="Identity number"  data-required-error="please enter identity number">
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
                                    <input type="radio" name="gender" value="1" @if ($driver->gender == 1)
                                      checked
                                    @endif data-error="please choose gender" required>Male
                                  </label>
                                  <label class="radio-inline">
                                    <input type="radio" name="gender" value="2" @if ($driver->gender == 2)
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
                            <label>Image&nbsp;<small>(Optional)</small></label>
                            <input type="file" name = 'profile_image' class="form-control {{ $errors->has('profile_image') ? ' is-invalid' : '' }}" id="profile_image">
                            <input type="hidden" name="old_profile_image" value="{{ $driver->profile_image }}">
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
                          <select name = 'country' class="form-control" id="country" data-required-error="please select country" id="country"  >
                            <option value="">Select Country</option>
                            @foreach ($countries as $country)
                              <option @if ($driver->country_id == $country->id)
                                {{ 'selected' }}
                              @endif value="{{ $country->id }}">{{  $country->name }}</option>
                            @endforeach
                            <option value="">First</option>
                          </select>
                           @if ($errors->has('country'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('country') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                          <label>Province</label><span class="star">&nbsp;</span>
                          <select name = 'state' class="form-control" id="state" data-required-error="please select province"  >
                          </select>
                           @if ($errors->has('state'))
                              <span class="star help-block">
                                  <strong>{{ $errors->first('state') }}</strong>
                              </span>
                           @endif
                           <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                            <label>City</label><span class="star">&nbsp;</span>
                            <select name = 'city' class="form-control" id="city" data-required-error="please select city"  >
                            </select>
                             @if ($errors->has('city'))
                                <span class="star  help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                </span>
                             @endif
                             <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                            <label>Address</label><span class="star">&nbsp; </span>
                            <input name = 'address' class="form-control {{ $errors->has('address') ? ' is-invalid' : '' }}" value="{{ $driver->address }}" placeholder="address" data-required-error="please enter address"   >
                             @if ($errors->has('address'))
                                <span class="star  help-block">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                             @endif
                             <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                              <label>Zip Code</label>
                              <input name = 'zcode' class="form-control {{ $errors->has('zcode') ? ' is-invalid' : '' }}" value="@if($driver->zip_code){{$driver->zip_code}}
                              @endif" placeholder="zcode" onkeypress="return isNumberKey(event);" data-minlength="4" maxlength="4" data-error="please enter 4 digit zipcode">
                               @if ($errors->has('zcode'))
                                  <span class="star  help-block">
                                      <strong>{{ $errors->first('zcode') }}</strong>
                                  </span>
                               @endif
                               <div class="help-block with-errors"></div>
                        </div>

                          {{-- <div class="form-group">
                                <label>Discount</label>&nbsp;  In (R) Between {{ $discountRange[0] }} To {{ $discountRange[1] }}
                                <input type="number" id="discount" name = 'discount' class="form-control {{ $errors->has('discount') ? ' is-invalid' : '' }}" value="{{ $driver->discount }}" placeholder="Discount" onkeypress="return isNumberKey(event);" data-required-error="please provide discount"  min="{{ $discountRange[0] }}" max="{{ $discountRange[1] }}">
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
                                      <input class="allow_discount" type="radio" name="allow_discount" value="0" @if ( $driver->allow_discount == 0 )
                                        checked
                                      @endif  checked >No
                                    </label>
                                    <label class="radio-inline">
                                      <input class="allow_discount" type="radio" name="allow_discount" value="1" @if ($driver->allow_discount == 1 )
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

                        <div class="form-group">
                          <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                          <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Add Driver">Update</button>
                        </div>
                      </div>
                    </div><!-- row -->
                    </form>
                  </div><!-- panel-body -->
                </div><!-- panel -->
              </div>
              <div class="tab-pane" id="tab_2">
                <div class="panel panel-default">
                  <div class="panel-body">
                     <div class="row">
                       <div class="col-md-6">
                          <form data-toggle="validator" role="form" action="{{ route('vehicle/update') }}" method="post" enctype="multipart/form-data">
           {{ csrf_field() }}
           {{ method_field('PUT') }}
             <input type="hidden" name="driver_id" value="{{ Request::input('id') }}">
             <input type="hidden" name="vehicle_id" value="@if ($driver->vehicle_id)
                  {{ encrypt($driver->vehicle_id) }} @endif">
             <input type="hidden" name="redirects_to" value="{{ URL::previous() }}">
              <div class="form-group">
                <label>Vehicle Type</label><span class="star">&nbsp;*</span>
                <select name ='vehicle_category' class="form-control" data-required-error="please select vehicle type" id="vehicle_category"  required>
                   @if (!empty($vehicle_categories) && $vehicle_categories != Null )
                    @foreach ($vehicle_categories as $vehicle_category)
                      <option @if ($vehicle_category->id == $driver->vehicle_category)
                        {{ "selected" }}
                      @endif value="{{ ($vehicle_category->id) }}">{{ $vehicle_category->name }}</option>
                    @endforeach
                    @if (!empty($assign_category) && $assign_category != Null )
                       <option @if ($assign_category->id == $driver->vehicle_category)
                        {{ "selected" }}
                      @endif value="{{ $assign_category->id }}">{{ $assign_category->name }}</option>
                    @endif
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
                 {{--  <input name = 'vehicle_name' class="form-control" value="{{ $driver->vehicle_name }}" data-required-error="please enter vehicle name"  required> --}}
                    <select name = 'vehicle_name' class="form-control" data-required-error="please enter vehicle name"  required>
                     @if (!empty($vehicle_model) && $vehicle_model != Null )
                      @foreach ($vehicle_model as $model)
                        <option @if ($driver->vehicle_name == $model->name)
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
                <input name = 'registration_number' class="form-control" value="{{ $driver->registration_number }}" data-required-error="please enter registration number"  required>
                 @if ($errors->has('registration_number'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('registration_number') }}</strong>
                    </span>
                 @endif
                  <div class="help-block with-errors"></div> 
                </div>

                <div class="form-group">
                <label>Insurance No.</label><span class="star">&nbsp;*</span>
                <input name = 'insurance_number' class="form-control" value="{{ $driver->insurance_number }}" data-required-error="please enter insurance number"  >
                 @if ($errors->has('insurance_number'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('insurance_number') }}</strong>
                    </span>
                 @endif
                  <div class="help-block with-errors"></div> 
                </div>

                <div class="form-group">
                <label>Year of Car</label><span class="star">&nbsp;</span>
                <select name="car_year" class="form-control" data-required-error="please enter car year">
                  <option>select year</option>
                  @for($i = 2000;$i<=2100;$i++)
                  <option value="{{$i}}" {{($i == $driver->car_year) ? 'selected' : ''}}>{{ $i }}</option>
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
                <input type="color" name="color" class="form-control" value="{{ $driver->color }}" data-required-error="please choose vehicle color"  value="" required> 
                 @if ($errors->has('color'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('color') }}</strong>
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
                 <input type="hidden" name="old_vehicle_image" value="{{ $driver->vehicle_image }}">
              </div>
              <div id="image_preview"></div>

              {{-- <div class="form-group">
                <label>Per Kilometer charges</label><span class="star">&nbsp;*</span> In (R) <small id="charges"></small>
                <input type="number" name = 'per_km_charge' class="form-control" value="{{ $driver->per_km_charge }}" data-required-error="please enter per kilometer charges"   min="" max="" id="per_km_charge" required step="any">
                 @if ($errors->has('per_km_charge'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('per_km_charge') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div> --}}

              <div class="form-group">
                <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Add Vehicle">Update</button>
              </div>
          </form>
                       </div>
                     </div>
                  </div>
                </div>
                
              </div>
              <div class="tab-pane" id="tab_3"">
                <div class="panel panel-default">
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-lg-6" >
                         <form data-toggle="validator" role="form" action="{{ route('driver/update-documents') }}" method="post" enctype="multipart/form-data" method="PUT">
            {{ csrf_field() }}
               {{ method_field('PUT') }}
                <input type="hidden" name="driver_id" value="{{ Request::input('id') }}">
                <input type="hidden" name="document_id" value="@if ($driver->documents_id)
                  {{ encrypt($driver->documents_id) }} @endif">
                <input type="hidden" name="redirects_to" value="{{ URL::previous() }}">
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
                    </div><!-- row -->
                  </div><!-- panel-body -->
                </div><!-- panel -->
              </div>
           </div>
          </form><!-- form -->
       </div><!-- col-md-12-->
     </div><!-- row -->
   </div><!-- page header -->
@endsection
@section('css-file')
 <!-- validatoin css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
@endsection
@section('js-script')
<!-- validator js file -->
  <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('Admin/js/jquery.validate.min.js') }}"></script>
  <script type="text/javascript">

        $.ajax({
            type:'get',
            url:"{{ route('ajax/getStates') }}",
            context: document.body,
            data: {
                country: $('#country').val(),
                state: '{{ $driver->state_id }}',
                '_token': '{{ csrf_token() }}',
            },

            success : function(data){
                var data = JSON.parse(data);
                var option = '<option value="">-- select state --</option>';

                var state = '{{ $driver->state_id}}';
                //alert(state);
                var selected = '';
                for(var i in data){
                    if(state!=null || state!=''){
                      if( state == data[i].id){
                        var selected = 'selected';
                        //alert(data[i].name);
                      }else{
                          var selected = '';
                      }
                    }  
                    option += '<option value="'+data[i].id+'" '+selected+'>'+data[i].name+'</option>';
                }
                $("#state").html(option);
                 
                
            }
        });


         // get cities
          $.ajax({
               type:'get',
               url:"{{ route('ajax/getCities')}}",
               data: {
                    state: '{{$driver->state_id}}',
                    '_token': '{{ csrf_token() }}',
                },
               success : function(data){


                    var data = JSON.parse(data);
                    var city = '{{$driver->city_id}}';
                    var option="";
                    var selected = '';
                    var option = '<option value="">--- Select City ---</option>';
                  if(data.length != 0){

                    for(var i in data){
                        if(city!=null || city!=''){
                            if(city == data[i].id){
                              var selected = 'selected';
                            }else{
                               var selected = '';
                            }
                          }
                          
                        option =option+ '<option value="'+data[i].id+'" '+selected+'>'+data[i].name+'</option>';
                      }
                    
                      $("#city").html(option);
                }else{
                        option += '<option value="city">'+$('#state option:selected').text()+'</option>';
                           $("#city").html(option);
                }
                
                 
               }
            });


    // get states
        $('#country').change(function(){
        
          $.ajax({
             type:'get',
             url:"{{  route('ajax/getStates')}}",
             data: {
                  country: $(this).val(),
                  '_token': '{{ csrf_token() }}',
              },
             success : function(data){
                  var data = JSON.parse(data);
                  var option = '<option value="">--- Select State ---</option>';
                  for(var i in data){
                      option += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                  }
                  $("#state").html(option);
             }
          });
       });

        // get cities

        $('#state').change(function(){
          $.ajax({
             type:'get',
             url:"{{ route('ajax/getCities')}}",
             data: {
                  state: $(this).val(),
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

   @if (Session::has('msg'))
 /*  window.setTimeout(function () { 
   $(".alert-row").fadeOut('slow') }, 2000); */
   @endif

       // get charges value between
          // $.ajax({
          //    type:'get',
          //    url:"{{  route('ajax/getCharges')}}",
          //    data: {
          //         id: '{{ ($vehicle_category->id) }}',
          //         '_token': '{{ csrf_token() }}',
          //     },
          //    success : function(response){

          //        let data = JSON.parse(response);
          //        console.log(data);
          //        let starting = data.data.per_km_charges.split(',')[0];
          //        let  ending   = data.data.per_km_charges.split(',')[1];
          //        let html = 'between '+ starting + '  To '  + ending ;
          //           $('#charges').html(html);
          //           $('#per_km_charge').attr('min' , starting);
          //           $('#per_km_charge').attr('max' , ending);
          //    }
          // });

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


