@extends('admin.layouts.app')
@section('title', 'Profile')
@section('breadcrumb')
      <h1>
         Profile
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Profile</li>
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
           <!-- /.col -->
             <div class="col-md-12">
                <div class="box box-primary">
                  <div class="box-body box-profile">
                    <div class="col-md-2">
                        <img class="profile-user-img img-responsive " src="{{  asset('Admin/profileImage/'.$data->profile_image) }}" alt="User profile picture" style="width: 100%; height:200px;">
                    </div>
                    <div class="col-md-5">
                      <ul class="list-group list-group-unbordered">
                        <li class="list-group-item li-border-top">
                          <b>Username</b> <a class="pull-right">{{ $data->username }}</a>
                        </li>
                        <li class="list-group-item">
                          <b>Name</b> <a class="pull-right">{{ ucfirst($data->name) }}</a>
                        </li>
                        <li class="list-group-item">
                          <b>Email</b> <a class="pull-right">{{ $data->email }}</a>
                        </li>
                        <li class="list-group-item">
                          <b>Mobile</b> <a class="pull-right">{{ $data->mobile }}</a>
                        </li>
                        <li class="list-group-item">
                          <b>Gender</b> <a class="pull-right">{{ ($data->gender == 1) ? 'Male' : 'Female' }}</a>
                        </li>
                          <li class="list-group-item">
                          <b>Role</b> <a class="pull-right">@if ($data->role_name)
                            {{ ucfirst($data->role_name) }} @else {{ 'Role not assign' }}
                          @endif</a>
                        </li>
                      </ul>
                      <a href="{{ route('profile/edit') }}" class="btn bg-orange btn-flat margin btn-block" data-toggle="tooltip" title="Edit Profile"><i class="fa fa-fw" aria-hidden="true">&#xf044&nbsp;Edit</i></a>
                    </div>
                  </div>
                </div>
             </div>
           <!-- /.tab-pane -->
            </div>
                <!-- /.tab-content -->
@endsection
@section('css-script')
<style type="text/css">
  .li-border-top{
     border-top:none !important;
  }
</style>
@endsection
@section('js-script')
  <!-- response  message alert script -->
  @if (Session::has('msg'))
   <script type="text/javascript">
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 1500); 
   </script>
  @endif
@endsection





