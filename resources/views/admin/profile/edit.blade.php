@extends('admin.layouts.app')
@section('title', 'Edit Profile')
@section('breadcrumb')
      <h1>
        Edit Profile
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('profile/show')}}">Profile</a></li>
        <li class="active">Edit Profile</li>
      </ol>
@endsection
@section('content')   
     <!-- Page Content -->
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
                <!-- /.row -->
              <div class="row">
                <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6" >
                                       <form data-toggle="validator" role="form" action="{{ route('profile/update') }}" method="post" enctype="multipart/form-data">
                                                            {{ csrf_field() }}
                                                            {{ method_field('PUT') }}
                                                            <input type="hidden" name="old_profile_image" value="{{ $data->profile_image }}">
                                                            
                                                              <div class="form-group">
                                                                <label>Profile Image</label><span class="star">&nbsp;*</span>
                                                                <input type="file" name = 'profile_image' class="form-control {{ $errors->has('profile_image') ? ' is-invalid' : '' }}" value="" >
                                                                 @if ($errors->has('profile_image'))
                                                                    <span class="star help-block">
                                                                        <strong>{{ $errors->first('profile_image') }}</strong>
                                                                    </span>
                                                                 @endif
                                                              </div>

                                                              <div class="form-group">
                                                                <label>Username</label><span class="star">&nbsp;*</span>
                                                                <input name = 'username' class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" value="{{ $data->username }}" placeholder="fist name" data-required-error="please enter your username" min="4" max="10"  required>
                                                                 @if ($errors->has('username'))
                                                                    <span class="star help-block">
                                                                        <strong>{{ $errors->first('username') }}</strong>
                                                                    </span>
                                                                 @endif
                                                                 <div class="help-block with-errors"></div>
                                                              </div>
                                                              
                                                              <div class="form-group">
                                                                <label>First Name</label><span class="star">&nbsp;*</span>
                                                                <input name = 'first_name' class="form-control {{ $errors->has('first_name') ? ' is-invalid' : '' }}" onkeypress="return isAlphaKey(event);" value="{{ $data->first_name }}" placeholder="fist name" data-required-error="please enter your first name"  required>
                                                                 @if ($errors->has('first_name'))
                                                                    <span class="star help-block">
                                                                        <strong>{{ $errors->first('first_name') }}</strong>
                                                                    </span>
                                                                 @endif
                                                                 <div class="help-block with-errors"></div>
                                                              </div>

                                                              <div class="form-group">
                                                                <label>Last Name</label><span class="star">&nbsp;*</span>
                                                                <input name = 'last_name' class="form-control {{ $errors->has('last_name') ? ' is-invalid' : '' }}" onkeypress="return isAlphaKey(event);" value="{{ $data->last_name }}" placeholder="last name" data-required-error="please enter your last name"  required>
                                                                 @if ($errors->has('last_name'))
                                                                    <span class="star help-block">
                                                                        <strong>{{ $errors->first('last_name') }}</strong>
                                                                    </span>
                                                                 @endif
                                                                 <div class="help-block with-errors"></div>
                                                              </div>

                                                              <div class="form-group">
                                                                  <label>Email</label><span class="star">&nbsp;*</span>
                                                                  <input type="email" name = 'email' class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ $data->email }}" placeholder="email" data-required-error="please enter your email address"  required>
                                                                   @if ($errors->has('email'))
                                                                      <span class="star help-block">
                                                                          <strong>{{ $errors->first('email') }}</strong>
                                                                      </span>
                                                                   @endif
                                                                   <div class="help-block with-errors"></div>
                                                              </div>

                                                              <div class="form-group">
                                                                  <label>Modile No.</label><span class="star">&nbsp;*</span>
                                                                  <input name = 'mobile' class="form-control {{ $errors->has('mobile') ? ' is-invalid' : '' }}" onkeypress="return isNumberKey(event);" value="{{ $data->mobile }}" placeholder="Mobile No." data-required-error="please enter mobile number"  required>
                                                                   @if ($errors->has('mobile'))
                                                                      <span class="star  help-block">
                                                                          <strong>{{ $errors->first('mobile') }}</strong>
                                                                      </span>
                                                                   @endif
                                                                   <div class="help-block with-errors"></div>
                                                              </div>

                                                              <div clast="form-group">
                                                                <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</button>
                                                                <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Update Profile">Update</button>
                                                              </div>
                                       </form>
                                    </div>
                                    <!-- /.col-lg-6 (nested) -->
                                </div>
                                <!-- /.row (nested) -->
                            </div>
                            <!-- /.panel-body -->
                        </div>
                        <!-- /.panel -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->
            </div>
@endsection
@section('css-script')
  <!-- validatoin css file -->
  <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
@endsection
@section('js-script')
  <!-- bootstrap validation script -->
  <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
  <!-- response  message alert script -->
  <!-- js custome script -->
  <script type="text/javascript">
    @if (Session::has('msg'))
        window.setTimeout(function () { 
         $(".alert-row").fadeOut('slow') }, 1500); 
    @endif
  </script>
@endsection


