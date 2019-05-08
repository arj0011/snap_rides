@extends('admin.layouts.app')
@section('title', 'Add Vehicle')
@section('breadcrumb')
      <h1>
        Add New Invite
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('plan/plans') }}">Plans</a></li>
        <li class="active">Add Plan</li>
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
          <form data-toggle="validator" role="form" action="{{ route('plan/store') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="col-md-6">

               <div class="form-group">
                <label>Offer Title</label><span class="star">&nbsp;*</span>
                <input name='plan_name' id="type" class="form-control" value="{{ old('plan_name') }}" placeholder="Plan Name" data-required-error="Please enter your Offer title"  required>
                 @if ($errors->has('plan_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

                 

              <div class="form-group">
                <label>Offer Cost (Rs)</label><span class="star">&nbsp;*</span ><small> In (&#8377;)</small>
                <input  onkeypress="return isNumberKey(event);"  name='plan_cost' id="plan_cost" class="form-control" value="{{ old('plan_cost') }}" placeholder="Enter Plan Cost" data-required-error="Please select offer cost"  required>
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>



               <div class="form-group">
                <label>Plan Extention(Days)</label><span class="star">&nbsp;*</span ><small> In (&#8377;)</small>
                <input  onkeypress="return isNumberKey(event);"  name='plan_cost' id="plan_cost" class="form-control" value="{{ old('plan_cost') }}" placeholder="Enter Plan Cost" data-required-error="Please enter plan Extention Days"  required>
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>


              <div class="form-group">
                <label>Image</label><span class="star">&nbsp;*</span ><small> In (&#8377;)</small>
                <input  onkeypress="return isNumberKey(event);"  name='plan_cost' id="plan_cost" class="form-control" value="{{ old('plan_cost') }}" placeholder="Enter Plan Cost" data-required-error="Please select plan cost"  required>
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>
              

               <div class="form-group">
                <label>Offer Description</label><span class="star">&nbsp;*</span>
                <input name='plan_name' id="type" class="form-control" value="{{ old('plan_name') }}" placeholder="Plan Name" data-required-error="Please enter offer Description"  required>
                 @if ($errors->has('plan_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

            

              
              

              <div class="form-group"> 
                  <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                  <button type="submit" class="btn btn-md btn-flat btn-primary">Add</button>
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
 <!-- edit css file -->
 <style type="text/css">
   label{
          color: #222d32 !important;
   }
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
<!-- <script type="text/javascript" src="{{ asset('Admin/js/jquery.min.js') }}"></script> -->
   <!-- bootstrap toggle script -->
   <script type="text/javascript"  src="{{ asset('Admin/js/bootstrap-toggle.js') }}"></script>
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
   <!-- texarea auto sizer -->
   <script type="text/javascript" src="{{ asset('Admin/js/texarea-autosizer.js') }}"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>

     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css">
 
 
 
   </script>

    <script type="text/javascript">
            $(function () {
                $('#start_date').datepicker();
                $('#end_date').datepicker();

            });
             
             
  $("#end_date").change(function() {
    var startDate = document.getElementById("start_date").value;
    var endDate = document.getElementById("end_date").value;

    if ((Date.parse(endDate) <= Date.parse(startDate))) {
      alert("End date should be greater than Start date");
      document.getElementById("end_date").value = "";
    }
  });

           

        </script>


@endsection