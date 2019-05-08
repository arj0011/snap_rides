@extends('admin.layouts.app')
@section('title', 'Change Password')
@section('breadcrumb')
      <h1>
       Change Password
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('profile/show',['id' => Auth::user()->id  ])}}">Profile</a></li>
        <li class="active">Change Password</li>
      </ol>
@endsection
@section('content')   
     <!-- Page Content -->
            <div id="page-wrapper">
                <!-- /.row -->
                <div class="col-lg-12">
                        <div class="panel panel-default">
                            @if (Session::has('success'))
                            <div class="panel-heading">
					             <div class="alert alert-success">
					                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					                <p>{{ Session::get('success') }}</p>
					             </div>
                            </div>
					        @endif
					         @if (Session::has('failed'))
					          <div class="panel-heading">
		                    <div class="alert alert-warning fade in alert-dismissable">
		                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		                        <p>{{ Session::get('failed') }}</p>
		                    </div>
		                    </div>
		                    @endif
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6" >
                                        <form role="form" action="{{ route('update-password') }}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('PUT') }}
                                            <div class="form-group">
                                                <label>Current Password</label><span class="star">&nbsp;*</span>
                                                <input type="password" name = 'oldPassword' class="form-control {{ $errors->has('oldPassword') ? ' is-invalid' : '' }}" value="" placeholder="current Password">
                                                 @if ($errors->has('oldPassword'))
						                            <span class="star help-block">
						                                <strong>{{ $errors->first('oldPassword') }}</strong>
						                            </span>
                                                 @endif
                                            </div>
                                            

                                            <div class="form-group">
                                                <label>New Password</label><span class="star">&nbsp;*</span>
                                                <input type="password" name = 'newPassword' class="form-control {{ $errors->has('newPassword') ? ' is-invalid' : '' }}" value="" placeholder="New Password">
                                                 @if ($errors->has('newPassword'))
						                            <span class="star help-block">
						                                <strong>{{ $errors->first('newPassword') }}</strong>
						                            </span>
                                                 @endif
                                            </div>

                                            <div class="form-group">
                                                <label>Confirm Password</label><span class="star">&nbsp;*</span>
                                                <input type="password" name = 'cPassword' class="form-control {{ $errors->has('cPassword') ? ' is-invalid' : '' }}" value="" placeholder="Confirm Password">
                                                 @if ($errors->has('cPassword'))
						                            <span class="star help-block">
						                                <strong>{{ $errors->first('cPassword') }}</strong>
						                            </span>
                                                 @endif
                                            </div>

                                            <button type="reset" class="btn btn-flat margin" data-toggle="tooltip" title="Reset">Reset</button>
                                            <button type="submit" class="btn bg-olive btn-flat margin" data-toggle="tooltip" title="Update Password">Update</button>
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
@endsection


