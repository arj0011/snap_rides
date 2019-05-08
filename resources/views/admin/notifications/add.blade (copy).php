@extends('admin.layouts.app')
@section('title', 'New Notification')
@section('breadcrumb')
      <h1>
        Send New Notification
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('plan/plans') }}">Send Notifications</a></li>
        <li class="active">Send Notification</li>
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
          <form data-toggle="validator" role="form" action="{{ route('notification/store') }}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
              
              <div class="col-md-12">
              <div class="col-md-3">
                 <div class="form-group">
                  <label>To</label>
                  <select id="toElement" class="form-control" name="to">
                    <option value="driver" selected>Driver</option>
                    <option value="rider">Rider</option>
                  </select>
                </div>
              </div>

              <div class="col-md-3" id="city-container">
                 <div class="form-group">
                  <label>Select City</label>
                  <select id="city" class="form-control select2" name="city" multiple>
                    <option value="all">--Select all--</option>
                    @foreach ($cities as $city)
                         <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>


              <div class="col-md-3">
                 <div class="form-group">
                  <label id="ToElement" style=" text-transform: capitalize;"></label>
                  <select id="to" class="form-control select2" name="to" multiple>
                    <option value="all">-- select --</option>
                  </select>
                </div>
              </div>

              </div>
             
             <div class="col-md-12">
              <div class="col-md-9">
                 <div class="form-group">
                  <label>Subject</label><span class="star">&nbsp;*</span>
                  <input type="text" name="subject" class="form-control"  placeholder="Please enter subject"  data-required-error="please enter subject"  required>
                   @if ($errors->has('subject'))
                      <span class="star help-block">
                          {{ old('subject') }}
                      </span>
                   @endif
                   <div class="help-block with-errors"></div>
                </div>
              </div>
              </div>
             <div class="col-md-12">
              <div class="col-md-9">
                 <div class="form-group">
                  <label>Message</label><span class="star">&nbsp;*</span>
                  <textarea class="form-control textarea" name='message' id="PlanTextarea" class="form-control"  placeholder="Please enter message"  data-required-error="please enter message"  required>
                        {{ old('message') }}
                  </textarea>
                   @if ($errors->has('message'))
                      <span class="star help-block">
                          {{ old('message') }}
                      </span>
                   @endif
                   <div class="help-block with-errors"></div>
                </div>
              </div>
              </div>
              
              <div class="col-md-12">
              <div class="col-md-12">
                <div class="form-group"> 
                    <button type="submit" class="btn btn-md btn-flat btn-primary">Send</button>
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
 <!-- validatoin css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
 <!-- toggle css file -->
 <link rel="stylesheet" type="text/css" href="{{  asset('Admin/css/bootstrap-toggle.css') }}">
  <!-- select2 css file -->
 <link rel="stylesheet" type="text/css" href="{{  asset('Admin/css/select2.min.css') }}">
 <!-- edit css file -->
 <link rel="stylesheet" type="text/css" href="{{  asset('Admin/css/bootstrap-multiselect.css') }}">
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
   <!-- bootstrap toggle script -->
   <script type="text/javascript"  src="{{ asset('Admin/js/bootstrap-toggle.js') }}"></script>
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
   <!-- texarea auto sizer -->
   <script type="text/javascript" src="{{ asset('Admin/js/texarea-autosizer.js') }}"></script>
      <!-- select2 to auto sizer -->
   <script type="text/javascript" src="{{ asset('Admin/js/select2.full.min.js') }}"></script>
      <!-- multiselect -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrap-multiselect.js') }}"></script>
   
 
   <script type="text/javascript">
    $(function() {
      $('#toggle').bootstrapToggle();
    });
     @if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
   @endif
     
    $('#ToElement').text($('#toElement').val());

    $('#toElement').change(function(){
        $('#ToElement').text($('#toElement').val());
        if($('#toElement').val() == 'driver')
           $('#city-container').css('display', 'inline');
        else
          $('#city-container').css('display', 'none');
    });

   // enable select2

    $('#toElement').change(function(){
        let userType =  $('#toElement').val();
        if(userType == 'rider'){
           $.ajax({
               type:'get',
               url:"{{  route('notification/getUsers') }}",
               data: {
                    userType : userType,
                    '_token': '{{ csrf_token() }}'
                },
               success : function(data){
                        $('#to').html(data);                     
               }
            });
        }else{
           $('#to').html('');
        }
    });

   $(".select2").select2();

   // get Drives or Riders By Jquery

   $('#city').change(function(){

       let userType =  $('#toElement').val();
       let cities =  $('#city').val();
       console.log(city);
       // get riders or drives as (users)
            $.ajax({
               type:'get',
               url:"{{  route('notification/getUsers') }}",
               data: {
                    userType : userType,
                    cities     : cities,
                    '_token': '{{ csrf_token() }}'
                },
               success : function(data){
                        $('#to').html(data);                     
               }
            });
    });

 $(function() {
  var filter = $('#city');
  filter.on('change', function() {
    if (this.selectedIndex) return; //not `Select All`
    filter.find('option:gt(0)').prop('selected', true);
    filter.find('option').eq(0).prop('selected', false);
  });
});

  $(function() {
  var filter = $('#to');
  filter.on('change', function() {
    if (this.selectedIndex) return; //not `Select All`
    filter.find('option:gt(0)').prop('selected', true);
    filter.find('option').eq(0).prop('selected', false);
  });
});

  $(document).ready(function() {
    $('.multiselect').multiselect();
  });


      autosize($("#PlanTextarea"));
   
      </script>
@endsection