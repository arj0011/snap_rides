@extends('admin.layouts.app')
@section('title', 'New Notification')
@section('breadcrumb')
      <h1>
        Send Notification
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('plan/plans') }}">Send Notifications</a></li>
        <li class="active">Send Notification</li>
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
          <form data-toggle="validator" role="form" action="{{ route('notification/store') }}" method="post" enctype="multipart/form-data">

            {{ csrf_field() }}
            <div class="row">
              
                <div class="col-md-12">
                     <div class="col-md-3">
                       <div class="form-group">
                        <label>To</label>
                        <select id="toElement" class="form-control" name="toElement" onchange="loaddata();">
                          <option value="driver" selected>Driver</option>
                          <option value="rider">Rider</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-3" id="city-container">
                       <div class="form-group">
                        <label>Select Driver</label><br/>
                        <select id="city" class="form-control" name="driver"  multiple="multiple" >
                          @foreach ($driver as $val)
                               <option value="{{ $val->id }}">{{ $val->name }}</option>
                          @endforeach
                        </select>
                        <input type="hidden"   name="driver" id="cities" value="" data-required-error="please enter cities"  required>
                          <div id="div_cities" class="help-block with-errors" style="color: #dd4b39;" ></div>
                      </div>
                    </div>


                    <div class="col-md-3">
                       <div class="form-group" id="div_users"  style="display: none;" >
                          <label >Select Users</label><br />
                          <select id="users" class="form-control" name="users"  multiple="multiple">
                             
                          </select>
                            <input type="hidden"   name="user" id="user" value="" data-required-error="please enter users"  required>
                              <div style="color: #dd4b39;" id="div_user" class="help-block with-errors"></div>
                      </div>
                    </div>
                      <div class="col-md-3"></div>

                </div>
               
                <div class="col-md-12">
                  <div class="col-md-9">
                    <div class="form-group">
                      <label>Subject</label><span class="star">&nbsp;*</span>
                      <input type="text" name="subject" id="subject" class="form-control"  placeholder="Please enter subject"   >
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
      else if($("#toElement").val()=='rider'){
            if($("#user").val()==''){
        $("#div_user").html('Please select users');
        ret=0;
      }else{
          $("#div_user").html('');
      }
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

    function loaddata(){ 
      let userType =  $('#toElement').val();
        
      if(userType == 'rider'){
          
        $("#div_users").show();
        $("#users").multiselect('destroy');
        $("#city-container").hide();
        $.ajax({
          type:'get',
          url:"{{  route('notification/getUsers') }}",
          data: {
            userType : userType,
            '_token': '{{ csrf_token() }}'
          },
          success : function(data){
            $('#users').html(data);
              $('#users').multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                  maxHeight: 400
              });                     
            }
        });
      }else{
         
        $("#city-container").show();
        $("#div_users").hide();
        //  $('#users').val('');
        $("#users").multiselect('destroy');
        //$('.multiselect').val().
      }
    }
    //});

    $('#city').change(function(){
       $("#div_users").hide();
       let userType =  $('#toElement').val();
       let cities =  $('#city').val();
        $("#cities").val(cities);
      
        $("#users").multiselect('destroy');
 
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
                    //$('#users').html('');
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