@extends('admin.layouts.app')
@section('title', 'Edit Plan')
@section('breadcrumb')
      <h1>
        Edit Plan
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('plan/plans') }}">Plans</a></li>
        <li class="active">Edi Plan</li>
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
          <form data-toggle="validator" id="planForm" role="form" action="{{ route('plan/update') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <input type="hidden" name="redirects_to" value="{{ URL::previous() }}">
            <div class="col-md-6">
               <div class="form-group">
                <label>Plan Name</label><span class="star">&nbsp;*</span>
                <input type="hidden" name="id" value="{{ encrypt($plan->id) }}">
                <input name='plan_name' id="type" class="form-control" value="{{ $plan->plan_name }}" placeholder="Plan Name" data-required-error="please enter your plane"  required>
                 @if ($errors->has('plan_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

               <div class="form-group">
                <label>Plan Description</label><span class="star">&nbsp;*</span>
                <textarea class="form-control"  name='plan_description' id="PlanTextarea" class="form-control"  placeholder="Please enter description"  data-required-error="please enter plan description"  required>
                 {{ $plan->plan_description }}
                </textarea>
                 @if ($errors->has('plan_description'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_description') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

                <div class="form-group">
                        <label>Plan Type</label><span class="star">&nbsp;*</span>
                        <select name = 'plan_type' class="form-control"  data-required-error="please select plan type"  required>
                          <option value="">--select plan--</option>
                          <option @if ($plan->plan_type == '1' )
                            {{ 'selected' }}
                      @endif value="1">Weekly</option>
                          <option @if ($plan->plan_type == '2' )
                         {{ 'selected' }}
                      @endif value="2">Monthly</option>
                          <option @if ($plan->plan_type == '3')
                         {{ 'selected' }}
                      @endif value="3">Yearly</option>
                        </select>
                         @if ($errors->has('plan_type'))
                            <span class="star  help-block">
                                <strong>{{ $errors->first('plan_type') }}</strong>
                            </span>
                         @endif
                         <div class="help-block with-errors"></div>
                </div>

                <div class="form-group">
                  <label>Plan Status</label><br>
                     <label class="radio-inline">
                      <input @if ($plan->plan_status == 1)
                        {{ 'checked' }} 
                      @endif type="radio" name="plan_status" value="1">Active
                    </label>
                    <label class="radio-inline">
                      <input @if ($plan->plan_status == 0)
                        {{ 'checked' }}
                      @endif type="radio" name="plan_status" value="0">Deactive
                    </label>
                   @if ($errors->has('plan_status'))
                      <span class="star help-block">
                          <strong>{{ $errors->first('plan_status') }}</strong>
                      </span>
                   @endif
                </div>

               <div class="form-group">
                <label>Plan Cost</label><span class="star">&nbsp;*</span ><small> In (R)</small><br>
                <input  onkeypress="return isNumberKey(event);" value="{{ $plan->plan_cost }}" name='plan_cost' id="plan_cost" class="form-control" value="{{ old('plan_cost') }}" placeholder="Enter Plan Cost">
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

              <div class="form-group"> 
                  <button id="resetButton" type="button" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                  <button type="submit" class="btn btn-md btn-flat btn-primary">update</button>
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
 <!-- validatoin css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
 <!-- toggle css file -->
 <link rel="stylesheet" type="text/css" href="{{  asset('Admin/css/bootstrap-toggle.css') }}">

 <style type="text/css">
    textarea#PlanTextarea {
      width:100%;
      box-sizing:border-box;
      display:block;
      max-width:100%;
      line-height:1.5;
      padding:15px 15px 30px;
    }
 </style>
@endsection
@section('js-script')
   <!-- bootstrap toggle script -->
   <script type="text/javascript"  src="{{ asset('Admin/js/bootstrap-toggle.js') }}"></script>
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
   <!-- texarea auto sizer -->
   <script type="text/javascript" src="{{ asset('Admin/js/texarea-autosizer.js') }}"></script>
   <script type="text/javascript">
    $(function() {
      $('#toggle').bootstrapToggle();
    });
     @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
   @endif
      autosize($("#PlanTextarea"));

      $('#resetButton').click(function() {
         location.reload(); 
      });

    </script>
@endsection