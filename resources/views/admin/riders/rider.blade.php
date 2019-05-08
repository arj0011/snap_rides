@extends('admin.layouts.app')
@section('title', 'rider Info')
@section('breadcrumb')
      <h1>
         Rider Info
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('rider/riders') }}">Riders</a></li>
        <li class="active">Rider Info</li>
      </ol>
@endsection
@section('content')   
     <div id="page-wrapper">
       <div class="row">
        <!-- /.col -->
        <div class="col-md-12">
                    <div class="box box-primary">
			            <div class="box-body box-profile">
			              <div class="col-md-2">
			               

			                @if (!empty($rider->profile_image) && file_exists('Admin/customerimg/'.$rider->profile_image))
			              	  <img class="profile-user-img img-responsive " src="{{  asset('Admin/customerimg/'.$rider->profile_image) }}" alt="User profile picture" style="width: 100%; height: 200px" >
			                @else
			                 <img class="profile-user-img img-responsive " src="{{  asset('Admin/profileImage/unknown.png') }}" alt="User profile picture" style="width: 100%; height: 200px" >
			                @endif
			              </div>
			              <div class="col-md-5">
			              	
			              <ul class="list-group list-group-unbordered">
			                <li class="list-group-item li-border-top">
			                  <b>Name</b> <a class="pull-right">{{ $rider->name }}</a>
			                </li>
			                {{-- <li class="list-group-item">
			                  <b>Email</b> <a class="pull-right">{{ $rider->email }}</a>
			                </li> --}}
			                <li class="list-group-item">
			                  <b>Mobile</b> <a class="pull-right">{{ $rider->mobile }}</a>
			                </li>
			                {{-- <li class="list-group-item">
			                  <b>City</b> <a class="pull-right">{{ $rider->city_name }}</a>
			                </li> --}}
			              </ul>
			              </div>
			                <div class="col-md-5">
			              <ul class="list-group list-group-unbordered">
			              
			              </ul>
			              </div>
			            </div>
			        </div>
              </div>
              <!-- /.tab-pane -->
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


