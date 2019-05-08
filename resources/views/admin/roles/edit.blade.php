@extends('admin.layouts.app')
@section('title', 'Edit Role')
@section('breadcrumb')
      <h1>
        Edit Role
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('role/roles') }}">Role</a></li>
        <li class="active">Edit Role</li>
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
          <form data-toggle="validator" role="form" action="{{ route('role/update') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <input type="hidden" name="id" value="{{ encrypt($role->id) }}">
            <input type="hidden" name="redirects_to" value="{{ URL::previous() }}">
            <div class="row">
              <div class="col-md-12">
                 <div class="form-group">
                  <label>Role Name</label><span class="star">&nbsp;*</span>
                  <input name='role_name' id="type" class="form-control" value="{{ $role->name }}" placeholder="Role" data-required-error="please enter role name"  required>
                   @if ($errors->has('role_name'))
                      <span class="star help-block">
                          <strong>{{ $errors->first('role_name') }}</strong>
                      </span>
                   @endif
                   <div class="help-block with-errors"></div>
              </div>

               <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-12">
                      <label>Permissions</label><span>&nbsp;&nbsp;<input type="checkbox" class="minimal-red" id="ckbCheckAll" /></span>
                      </div>

                      <div class="col-md-3">
                          <h4>Rider Module</h4>
                            <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Riders')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>

                      @if (decrypt(Request::input('id')) == 1 )

                       <div class="col-md-3">
                          <h4>Roles Module</h4>
                            <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Roles')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>

                       <div class="col-md-3">
                          <h4>User Module</h4>
                            <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Users')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>

                      @endif



                      <div class="col-md-3">
                        <h4>Driver Module</h4>
                          <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Drivers')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>


                      <div class="col-md-3">
                        <h4>Bookings Module</h4>
                          <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Bookings')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>
                      
                      <div class="col-md-3">
                        <h4>Vehicle Categoies Module</h4>
                          <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Vehicle Categoies')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>


                      {{-- <div class="col-md-3">
                       <h4>Plan Module</h4>
                        <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Plans')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div> --}}

                        <div class="col-md-3">
                       <h4>Notification Module</h4>
                        <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Notifications')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>


                      <div class="col-md-3">
                       <h4>SMS Module</h4>
                        <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'SMS')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>

                        <div class="col-md-3">
                       <h4>FAQ Module</h4>
                        <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'FAQs')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>



                      <div class="col-md-3">
                     <h4>Setting Module</h4>
                       <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Settings')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>

                      <div class="col-md-3">
                        <h4>Offer Module</h4>
                        <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Offers')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div>  

                      <div class="col-md-3">
                        <h4>Payment Module</h4>
                        <div class="scroll-div">
                        @foreach ($permissions as $permission)
                          @if ($permission->permission_for == 'Payment')
                              <div class="checkbox">
                               <label>
                               <input type="checkbox" class="minimal-red" name="permission[]" value="{{ $permission->id }}"
                                        @foreach ($role->permissions as $role_permit)
                                          @if ($role_permit->id == $permission->id)
                                            checked
                                          @endif
                                        @endforeach
                                        >&nbsp;&nbsp;{{ $permission->name }}</label>
                              </div>
                          @endif
                        @endforeach
                        </div>
                      </div> 



                  </div>
                </div> 
               
                <div class="col-md-12">
                  <div class="form-group">
                      <label>Status</label><br>
                         <label class="radio-inline">
                          <input @if ( $role->is_active == '1')
                           {{ 'checked' }}
                          @endif type="radio" name="role_status" value="1" checked>&nbsp;&nbsp;Active
                        </label>
                        <label class="radio-inline">
                          <input @if ( $role->is_active == '0')
                             {{ 'checked' }}
                          @endif type="radio" name="role_status" value="0">&nbsp;&nbsp;Deactive
                        </label>
                       @if ($errors->has('role_status'))
                          <span class="star help-block">
                              <strong>{{ $errors->first('role_status') }}</strong>
                          </span>
                       @endif
                  </div>
                </div>
                
                <div class="col-md-12">
                  <div class="form-group"> 
                      <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                      <button type="submit" class="btn btn-md btn-flat btn-primary">Update</button>
                  </div>
                </div>
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
 <!-- validatoin css srcipt -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
<!-- Icheck css script -->
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('Admin/iCheck/all.css') }}"> --}}
 <style type="text/css">
   .scroll-div{
      height : 200px;
      overflow-y: scroll;
   }
 </style>
@endsection
@section('js-script')
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
   <!-- bootstrap icheck script -->
  {{--  <script type="text/javascript" src="{{ asset('Admin/iCheck/icheck.min.js') }}"></script> --}}
  
   <!-- custome java script -->
    <script type="text/javascript">
     @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
   @endif
  
     // cheack all cheakboxes
      $(document).ready(function () {
        $("#ckbCheckAll").click(function () {
            $(".minimal-red").attr('checked', true);
        });
    });

      // cheack all cheakboxes
      $(document).ready(function () {
        $("#ckbCheckAll").click(function () {
           let checkedStatus = this.checked;
            $(".minimal-red").attr('checked', checkedStatus);
        });
    });


   // icheck script

    //Flat red color scheme for iCheck
 /*   $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })*/
   </script>
@endsection