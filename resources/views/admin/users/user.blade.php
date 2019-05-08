@extends('admin.layouts.app')
@section('title', 'Dispatcher Info')
@section('breadcrumb')
      <h1>
         Dispatcher Information
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('user/users') }}">Users</a></li>
        <li class="active">Dispatcher Info</li>
      </ol>
@endsection
@section('content')   
 <div id="page-wrapper">
   <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-body box-profile">
          <div class="col-md-2">
            @if (!empty($user->profile_image) && file_exists('Admin/profileImage/'.$user->profile_image))
              <img class="profile-user-img img-responsive " src="{{  asset('Admin/profileImage/'.$user->profile_image) }}" alt="User profile picture" style="width: 100%; height: 200px" >
            @else
             <img class="profile-user-img img-responsive " src="{{  asset('Admin/profileImage/unknown.png') }}" alt="User profile picture" style="width: 100%; height: 200px" >
            @endif
          </div>
          <div class="col-md-5">
            <ul class="list-group list-group-unbordered">
              <li class="list-group-item li-border-top">
                <b>Username</b><a class="pull-right">{{ $user->user_username }}</a>
              </li>
              <li class="list-group-item">
                <b>Name</b> <a class="pull-right">{{ $user->user_name }}</a>
              </li>
              <li class="list-group-item">
                <b>Email</b> <a class="pull-right">{{ $user->user_email }}</a>
              </li>
              <li class="list-group-item">
                <b>Mobile</b> <a class="pull-right">{{ $user->user_mobile }}</a>
              </li>
              <li class="list-group-item">
                <b>Role</b> <a class="pull-right">{{ $user->user_role }}</a>
              </li>
              <li class="list-group-item">
                <b>Registration Date:</b> <a class="pull-right">{{date('d-M-y', strtotime($user->created_at))}} {{date('h:i A',strtotime($user->created_at))}}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
   </div>
   </div>
@endsection
@section('css-script')
<style type="text/css">
    .li-border-top{
     border-top:none !important;
  }
       .checked {
    color: orange;
    }
     @media only screen and (max-width: 768px) {
    .li-border-top {
        margin-top: -18px;
     }
    }
</style>
@endsection


