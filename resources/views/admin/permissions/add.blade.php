@extends('admin.layouts.app')
@section('title', 'Add Permission')
@section('breadcrumb')
      <h1>
        Add Permission
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('permission/permissions') }}">Permission</a></li>
        <li class="active">Add Permission</li>
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
    <div class="col-md-12">
  <!-- Default box -->
      <div class="box">
        <div class="box-body">
          <form data-toggle="validator" permission="form" action="{{ route('permission/store') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="col-md-6">
               <div class="form-group">
                <label>Permission Name</label><span class="star">&nbsp;*</span>
                <input name='permission_name' id="type" class="form-control" value="{{ old('permission_name') }}" placeholder="permission" data-required-error="please enter permission name"  required>
                 @if ($errors->has('permission_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('permission_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

                <div class="form-group">
                 <label>Permission For</label><span class="star">&nbsp;*</span>
                  <select name = 'permission_for' class="form-control" data-required-error="please select permission for" required>
                    <option value="">Select Permission</option>
                      @forelse ($modules as $module)
                        <option @if ( old('permission_for') == $module->id )
                          {{ 'selected' }}
                        @endif value="{{ $module->id }}">{{ $module->name }}</option>
                      @empty
                         <option value="">data not founds</option>
                      @endforelse
                  </select>
                   @if ($errors->has('permission_for'))
                      <span class="star help-block">
                          <strong>{{ $errors->first('permission_for') }}</strong>
                      </span>
                   @endif
                   <div class="help-block with-errors"></div>
                </div>

              <div class="form-group">
                  <label>Status</label><br>
                     <label class="radio-inline">
                      <input @if ( old('permission_status') == '1')
                       {{ 'checked' }}
                      @endif type="radio" name="permission_status" value="1" checked>Active
                    </label>
                    <label class="radio-inline">
                      <input @if ( old('permission_status') == '0')
                         {{ 'checked' }}
                      @endif type="radio" name="permission_status" value="0">Deactive
                    </label>
                   @if ($errors->has('permission_status'))
                      <span class="star help-block">
                          <strong>{{ $errors->first('permission_status') }}</strong>
                      </span>
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
@endsection
@section('js-script')
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
   <script type="text/javascript">
     @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
   @endif
   </script>
@endsection