@extends('admin.layouts.app')
@section('title', 'Setting')
@section('breadcrumb')
      <h1>
        Add Allowed Cities
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Setting</li>
      </ol>
@endsection
@section('content')
  <div id="page-wrapper">
  

 

  <div class="row">
    <div class="col-md-12">
  <!-- Default box -->
      <div class="box">
        <div class="box-body">

          <div id="msg" style="display: none;" class="alert alert-success"></div>
           
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <div class="col-md-6">
                  <div class="form-group">
                    <label>Add City</label>
                    <input name='city' id="city"  class="form-control" value="" placeholder="City" onkeyup="return addCity(event);">
                     @if ($errors->has('setting_base_fare_km'))
                        <span class="star help-block">
                            <strong>{{ $errors->first('setting_base_fare_km') }}</strong>
                        </span>
                     @endif
                     <div class="help-block with-errors"></div>
                  </div>
                  <div>

 
                  <div class="form-group"> 
                     <button type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset Form">Reset</button>
                      <button type="button" class="btn  btn-flat btn-success" onclick="return saveCity();">Save</button>
                  </div>

                  <div class="form-group" id="citytags">
                   <?php foreach ($appcity as $key => $value) {
                     ?>
                     
                  <div class="box"  id='div_<?php echo $value->id; ?>' >    <a href="#" title="Map view "><?php  echo $value->name ; ?></a>
                  <button type='button' class='close' aria-label='Close' title="Delete" ><span aria-hidden='true' onclick='remove( <?php echo $value->id;  ?>);'>&times;</span>
                  </button>
                 
                  </div>  
                  <?php 
                  } ?>


                   </div> 

            </div>
      
       </div>
      </div>
      <!-- /.box -->
    </div>
  </div> 
  </div>
@endsection
@section('css-script')
 <!--  bootstrap  validatoin css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/bootstrapValidator.min.css') }}">
    <!--  bootstrap  slider css file -->
 <link rel="stylesheet" type="text/css" href="{{ asset('Admin/css/slider.css') }}">
<style type="text/css">
  .box{
    border: 1px ;
  }
</style>
@endsection
@section('js-script')
   <!-- bootstrap validation script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrapValidator.min.js') }}"></script>
      <!-- bootstrap slider js script -->
   <script type="text/javascript" src="{{ asset('Admin/js/bootstrap-slider.js') }}"></script>
   <script type="text/javascript">
     function saveCity(){

         var city=$("#city").val();
         var url="<?php echo url('/') ?>/updateCity";

         $.post(url,{city:city},function(obj){
            if(obj.success=='1'){
              var id = obj.id;
              var close = "<button type='button' class='close' aria-label='Close' ><span aria-hidden='true' onclick='remove("+id+");'>&times;</span></button>";
              var tag = "<div id='div_"+id+"' class='box'>"+city+close+"</div>";
              //alert(tag);
              $("#citytags").append(tag);
              $("#city").val("");
              
              $("#msg").html('City Saved Successfully');
              $("#msg").show();
              window.setTimeout(function () { 
              $("#msg").fadeOut('slow') }, 2000); 
              
            }

         },"json");
     }
     function addCity(event){
       if (event.keyCode =='13') {
          saveCity();
       } 
    }  
    function remove(id){
      $("#div_"+id).remove();
        var url="<?php echo url('/') ?>/deletecity";
        $.post(url,{id:id},function(obj){
          $("#msg").html('City Deleted Successfully');
              $("#msg").show();
              window.setTimeout(function () { 
              $("#msg").fadeOut('slow') }, 2000); 
        });
    }
  

 

      /*@if (Session::has('msg'))
      window.setTimeout(function () { 
       $(".alert-row").fadeOut('slow') }, 2000); 
      @endif
*/
 
   </script>
@endsection