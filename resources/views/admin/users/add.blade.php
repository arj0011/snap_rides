@extends('admin.layouts.app')
@section('title', 'Add Dispatcher')
@section('breadcrumb')
      <h1>
        Add Dispatcher User
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('user/users') }}">Dispatcher Users</a></li>
        <li class="active">Add Dispatcher User</li>
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
  <!-- Default box -->
      <div class="box">
        <div class="box-body">
          <form data-toggle="validator" role="form" action="{{ route('user/store') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="col-md-6">

              <div class="form-group">
                <label>Username</label><span class="star">&nbsp;*</span>
                <input name='user_username' class="form-control" value="{{ old('user_username') }}" placeholder="Username" data-required-error="please enter username"  required>
                 @if ($errors->has('user_username'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('user_username') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

               <div class="form-group">
                <label>Name</label><span class="star">&nbsp;*</span>
                <input name='user_name' class="form-control" value="{{ old('user_name') }}" placeholder="User name" data-required-error="please enter name"  required>
                 @if ($errors->has('user_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('user_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

               <div class="form-group">
                <label>Mobile</label><span class="star">&nbsp;*</span>
                <input name='user_mobile' class="form-control" value="{{ old('user_mobile') }}" placeholder="User mobile" onkeypress="return isNumberKey(event);" data-required-error="please enter mobile number"  required>
                 @if ($errors->has('user_mobile'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('user_mobile') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>


               <div class="form-group">
                <label>Email</label><span class="star">&nbsp;*</span>
                <input  type="email" name='user_email' class="form-control" value="{{ old('user_email') }}" placeholder="User email" data-required-error="please enter email"  required>
                 @if ($errors->has('user_email'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('user_email') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>


              <div class="form-group">
                <label>Password</label><span class="star">&nbsp;*</span>
                <input  type="password" name='user_password' class="form-control" value="{{ old('user_password') }}" placeholder="User Password" data-required-error="please enter password"  required>
                 @if ($errors->has('user_password'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('user_password') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

             <div class="form-group">
                 <label>Assign Role</label><span class="star">&nbsp;*</span>
                  <select name = 'user_role' class="form-control" data-required-error="please select role" required>
                    <option value="">--Select Role --</option>
                    @forelse ($roles as $role)
                      <option @if (old('user_role') == $role->id)
                        {{ 'selected' }}
                      @endif  value="{{ encrypt($role->id) }}">{{ $role->name }}</option>
                    @empty
                      <option value="">-- No any role available --</option>
                    @endforelse
                  </select>
                   @if ($errors->has('user_role'))
                      <span class="star help-block">
                          <strong>{{ $errors->first('user_role') }}</strong>
                      </span>
                   @endif
                   <div class="help-block with-errors"></div>
              </div>

              <div class="form-group">
                  <label>Status</label><br>
                     <label class="radio-inline">
                      <input @if ( old('user_status') == '1')
                       {{ 'checked' }}
                      @endif type="radio" name="user_status" value="1" class="minimal-red" checked> &nbsp;&nbsp;Active
                    </label>
                    <label class="radio-inline">
                      <input @if ( old('user_status') == '0')
                         {{ 'checked' }}
                      @endif type="radio" name="user_status" class="minimal-red" value="0"> &nbsp;&nbsp;Deactive
                    </label>
                   @if ($errors->has('user_status'))
                      <span class="star help-block">
                      <strong>{{ $errors->first('user_status') }}</strong>
                   @endif
               </div>

              <div class="form-group"> 
                  <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                  <button type="submit" class="btn btn-md btn-flat btn-primary">Add</button>
              </div>
            </div>
            <div class="col-md-6"> </div>
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

 <!-- Icheck css script -->
<link rel="stylesheet" type="text/css" href="{{ asset('Admin/iCheck/all.css') }}">

@endsection
@section('js-script')
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>

      <!-- bootstrap icheck script -->
   <script type="text/javascript" src="{{ asset('Admin/iCheck/icheck.min.js') }}"></script>

   <script type="text/javascript">

     @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
     @endif

    // icheck script

    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    })
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass   : 'iradio_minimal-red'
    })
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })
   </script>

@endsection

