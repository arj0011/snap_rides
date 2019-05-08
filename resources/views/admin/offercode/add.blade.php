@extends('admin.layouts.app')
@section('title', 'Add Vehicle')
@section('breadcrumb')
      <h1>
        Add New Offer
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('plan/plans') }}">Offers</a></li>
        <li class="active">Add Offer</li>
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
          <form data-toggle="validator" role="form" action="{{ route('saveOffer') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="col-md-6">

               <div class="form-group">
                <label>Offer Code</label><span class="star">&nbsp;*</span>
                <input name='offer_code' id="offer_code" class="form-control" value="{{ (!empty($get_det->id) ? $get_det->offer_code : old('plan_name') )  }}" placeholder="Offer Code" data-required-error="Please enter your Offer Code"  required>
                 @if ($errors->has('plan_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>
                
              <div class="form-group">
                <label>start Date  </label><span class="star">&nbsp;*</span>
                <input name='start_date' autocomplete="off" id="start_date" class="form-control" value="{{ (!empty($get_det->id) ? $get_det->start_date : old('plan_name')) }}" placeholder="start Date" data-required-error="Please select offer start date"  required>
                 @if ($errors->has('plan_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

              <div class="form-group">
                <label>End Date  </label><span class="star">&nbsp;*</span>
                <input name='end_date'  autocomplete="off" id="end_date" class="form-control"  value="{{ ( !empty($get_det->id) ? $get_det->end_date : old('end_date')) }}" placeholder="End Date" data-required-error="Please select offer end date"  required>
                 @if ($errors->has('plan_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

               <div class="form-group">
                <label>Offer Title</label><span class="star">&nbsp;*</span>
                <input name='title' id="title" class="form-control" value="{{ (!empty($get_det->id) ? $get_det->title  : old('description')) }}" placeholder="Offer title" data-required-error="Please enter title"  required>
                 @if ($errors->has('plan_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

            
                
                <div class="form-group">
                <label>Offer Description</label><span class="star">&nbsp;*</span>
                <textarea name='description' id="description" class="form-control" rows="5"  placeholder="Offer Description" data-required-error="Please enter offer Description"  required>{{ (!empty($get_det->id) ? $get_det->description  : old('description')) }}</textarea>
                 @if ($errors->has('plan_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>  

              <?php
               if(!empty($get_det->id)){
                ?>
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="offer_id"  value="<?php echo $get_det->id; ?>">
              <?php
               }
              ?>
              

              <div class="form-group"> 
                  <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                  <button type="submit" class="btn btn-md btn-flat btn-primary">Add</button>
              </div>

            </div>

            <div class="col-md-6">
                          
              {{-- <div class="form-group">
                <label>Offer Type</label><span class="star">&nbsp;*</span>
                <select name='offer_type' id="offer_type" class="form-control"   data-required-error="Please select offer type"  required>
                  <option value="">Select Offer Type</option>
                   <option value="promo" selected="selected">Promo Code</option>
                   <option value="invite">Invite Code</option>
                </select>
                 @if ($errors->has('plan_name'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_name') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div> --}}

              <div class="form-group">
                <label>Offer Type</label><span class="star">&nbsp;*</span>
                <select name='offer_type' id="offer_type" class="form-control" data-required-error="Please select offer type"  required>
                  <option value="">Select Offer Type</option>
                   <option value="FIXED">Fixed</option>
                   <option value="PERCENT">Percent</option>
                </select>
                 @if ($errors->has('offer_type'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('offer_type') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

              <div class="form-group" id="percentTypeDiv" style="display:none;">
                <label>Percent Type</label><span class="star">&nbsp;*</span >
                <select name='percent_type' id="percent_type" class="form-control" data-required-error="Please select percent type">
                  <option value="FLAT">Flat</option>
                  <option value="UPTO">Up to</option>
                </select>
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

              <div class="form-group" id="percentDiv" style="display:none;">
                <label>Percent</label><span class="star">&nbsp;*</span ><small> In (%)</small>
                <input  onkeypress="return isNumberKey(event);"  name='offer_percent' id="offer_percent" class="form-control" value="{{  (!empty($get_det->id) ? $get_det->percent :old('percent')) }}" placeholder="Enter Offer Percent" data-required-error="Please select offer percent">
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>

              <div class="form-group" id="costDiv" style="display:none;">
                <label>Offer Cost</label><span class="star">&nbsp;*</span ><small> In (R)</small>
                <input  onkeypress="return isNumberKey(event);"  name='offer_cost' id="offer_cost" class="form-control" value="{{  (!empty($get_det->id) ? $get_det->amount :old('plan_cost')) }}" placeholder="Enter Offer Cost" data-required-error="Please select offer cost"  required>
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div>


               {{-- <div class="form-group">
                <label>Offer Days</label><span class="star">&nbsp;*</span ></small>
                <input  onkeypress="return isNumberKey(event);"  name='extend_day' id="extend_day" class="form-control" value="{{ (!empty($get_det->id) ? $get_det->plan_extends_for_days :  old('extend_day')) }}" placeholder="Offer Extention" data-required-error="Please enter plan Extention Days"  required>
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
              </div> --}}

              <div class="form-group">
                <label>Limit Per User</label><span class="star">&nbsp;*</span >
                <input  onkeypress="return isNumberKey(event);"  name='no_limit' id="extend_day" class="form-control" value="{{ (!empty($get_det->id) ? $get_det->used_limit :  old('no_limit')) }}" placeholder="Per User Limit" data-required-error="Please enter per user limit."  required>
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                <div class="help-block with-errors"></div>
              </div>

              <div class="form-group">
                <label>Image</label><span class="star">*</span >
                <input type="file" name='image' id="image" class="form-control" value="{{ old('plan_cost') }}" placeholder="Enter Plan Cost" data-required-error="Please select plan cost"  >
                <?php
                if(!empty($get_det->image)){
                ?>
                <div style="width:100px;height:100px">
                    <img src="{{ asset('/public/offers')."/".$get_det->image }}" height="100" width="100"   >         
                </div>
                <?php
                }
                ?>
                 @if ($errors->has('plan_cost'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('plan_cost') }}</strong>
                    </span>
                 @endif
                 <div class="help-block with-errors"></div>
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
      var dateToday = new Date();    
 
      $("#start_date").datepicker({ 
        format: 'yyyy-mm-dd',
        startDate: "+0d" 
      });
     
      $("#end_date").datepicker({ 
        format: 'yyyy-mm-dd',
        startDate: "+0d" 
      });
    
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

      $(document).ready(function(){
        $(document).on('change','#offer_type',function(){
          var offertype = $(this).val();
          if(offertype == "PERCENT"){
            $('#percentTypeDiv').css('display','block');

            $('#offer_cost').val('');
            $('#offer_cost').removeAttr('required');
            $('#costDiv').css('display','none');
            
            $('#offer_percent').val('');
            $('#percentDiv').css('display','block');

            $('#percent_type').attr('required','');
            $('#offer_percent').attr('required','');

          }
          if(offertype == "FIXED"){
            $('#offer_cost').val('');
            $('#offer_cost').attr('required','');
            $('#costDiv').css('display','block');

            $('#offer_percent').val('');

            $('#percent_type').removeAttr('required');
            $('#offer_percent').removeAttr('required');

            $('#percentTypeDiv').css('display','none');
            $('#percentDiv').css('display','none');
          }
          
        });
      });

      $(document).ready(function(){
        $(document).on('change','#percent_type',function(){
          var percenttype = $(this).val();
          console.log(percenttype);
          if(percenttype == "FLAT"){
            $('#offer_percent').val('');
            $('#offer_percent').attr('required','');
            $('#percentDiv').css('display','block');
            
            $('#offer_cost').val('');
            $('#offer_cost').removeAttr('required'); 
            $('#costDiv').css('display','none');
          }
          
          if(percenttype == "UPTO"){
            $('#offer_percent').val('');
            $('#offer_percent').attr('required','');
            $('#percentDiv').css('display','block');
            
            $('#offer_cost').val('');
            $('#offer_cost').attr('required','');
            $('#costDiv').css('display','block');
          }          

        });
      });

    </script>


@endsection