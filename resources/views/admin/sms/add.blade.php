@extends('admin.layouts.app')
@section('title', 'New Sms')
@section('breadcrumb')
      <h1>
        Send SMS To Drivers
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('plan/plans') }}">Send Smss</a></li>
        <li class="active">Send Sms</li>
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
           <form data-toggle="validator" role="form" action="{{ route('notification/send_sms') }}" method="post" enctype="multipart/form-data">

            {{ csrf_field() }}
            <div class="row">
              
                <div class="col-md-12">

                    <div class="col-md-3" id="city-container">
                       <div class="form-group">
                        <label>Select Driver</label><br/>
                        <select id="city" class="form-control" name="driver[]"  multiple="multiple" >
                          @foreach ($drivers as $val)
                               <option value="{{ $val->id }}">{{ $val->name }}</option>
                          @endforeach
                        </select>
                        <input type="hidden"   name="cities" id="cities" value="" data-required-error="please enter cities"  required>
                          <div id="div_cities" class="help-block with-errors" style="color: #dd4b39;" ></div>
                      </div>
                    </div>


                  
               
                <div class="col-md-12">
                  <div class="col-md-9">
                    <div class="form-group">
                      <label>Subject</label><span class="star">&nbsp;*</span>
                      <input type="text" name="subject" id="subject" readonly="readonly" class="form-control"  placeholder="Please enter subject"  value="Via Snap Rides" >
                      @if ($errors->has('subject'))
                      <span class="star help-block">
                        {{ old('subject') }}
                      </span>
                      @endif
                      <div id="div_sub" style="color: #dd4b39;"  class="help-block with-errors"></div>
                    </div> 
                    <div class="col-md-3"></div>

                  </div>
                </div>
         
                <div class="col-md-12">
                  <div class="col-md-9">
                    <div class="form-group">
                      <label>Message</label><span class="star">&nbsp;*</span>
                      <textarea class="form-control textarea" name='message' id="message" class="form-control"  placeholder="Please enter message"   >
                        {{ old('message') }}
                      </textarea>
                      @if ($errors->has('message'))
                      <span class="star help-block">
                        {{ old('message') }}
                      </span>
                      @endif
                      <div id="div_msg" style="color: #dd4b39;"  class="help-block with-errors"></div>
                    </div>
                  </div>
                </div>
      
              <div class="col-md-12">
                <div class="col-md-9">
                <div class="form-group"> 
                    <button onclick="return validate();"  type="submit" class="btn btn-md btn-flat btn-primary">Send</button>
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
  <!-- edit css file -->
 
@endsection
@section('js-script')
   <!-- bootstrap toggle script -->
  <link rel="stylesheet" href="Admin/css/bootstrap-multiselect.css" type="text/css">
  <link rel="stylesheet" type="text/css" href="http://localhost:8000/Admin/css/bootstrapValidator.min.css">
  <script type="text/javascript" src="http://localhost:8000/Admin/js/bootstrapValidator.min.js"></script>

  <script type="text/javascript" src="Admin/js/bootstrap-multiselect.js"></script>  
  <script type="text/javascript">
 
     function validate(){
      var ret = 1;
      if($("#toElement").val()=='driver'){
        if($("#cities").val()==''){
          $("#div_cities").html('Please select cities');
          ret=0;
        }else{
          $("#div_cities").html('');
        }
      }
      if($("#user").val()==''){
        $("#div_user").html('Please select users');
        ret=0;
      }else{
          $("#div_user").html('');
      }
      if($("#subject").val()==''){
        $("#div_sub").html('Please enter subject');
        ret=0;
      }else{
          $("#div_sub").html('');
      }
      if($("#message").val()==''){
        $("#div_msg").html('Please enter Meassge');
        ret=0;
      }else{
          $("#div_msg").html('');
      }
    
    
      if(ret==1){
        return true;
      }else{
        return false;
      }
      
    }
    $(document).ready(function() {
        $('#city').multiselect({
            includeSelectAllOption: true,
             enableFiltering: true,
              enableCaseInsensitiveFiltering: true,
                maxHeight: 400

        });
    });
    $(document).ready(function(){
       $("#users").multiselect();
    });

    //$('#toElement').change(function(){

    $('#city').change(function(){
        $("#div_users").show();

       let cities =  $('#city').val();

        $("#cities").val(cities);
      
        $("#users").multiselect('destroy');
 
          // get riders or drives as (users)
           
            $.ajax({
               type:'get',
               url:"{{  route('sms/getUsers') }}",
               data: {
                    cities     : cities,
                    '_token': '{{ csrf_token() }}'
                },
               success : function(data){
                    console.log(data);
                    $('#users').html(data);    
                    $('#users').multiselect({
                      includeSelectAllOption: true,
                      enableFiltering: true,
                      enableCaseInsensitiveFiltering: true,
                       maxHeight: 400
                    });                    
               }
            });
    });

    $('#users').change(function(){
         var user =  $('#users').val();
            $("#user").val(user);
          });

      @if (Session::has('msg'))
        window.setTimeout(function () { 
         $(".alert-row").fadeOut('slow') }, 1500); 
      @endif

  </script>
@endsection