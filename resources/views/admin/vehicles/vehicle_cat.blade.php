@extends('admin.layouts.app')
@section('title', 'Add Vehicle')
@section('breadcrumb')
      <h1>
        Add Vehicle Category
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="{{route('driver/drivers') }}">Vehicles</a></li>
        <li class="active">Add Vehicle</li>
      </ol>
@endsection
@section('content')   
  <!-- Default box -->
      <div class="box">
        <div class="box-body">
          <form role="form" action="{{ route('vehicle/create') }}" method="post" enctype="multipart/form-data">
            <div class="col-md-6">
         
               <div class="form-group">
                <label>Vehicl Type</label><span class="star">&nbsp;*</span>
                <input name='type' id="type" class="form-control" value="" placeholder="Vehicle Number">
                 @if ($errors->has('vehicle_number'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_number') }}</strong>
                    </span>
                 @endif
              </div>

               <div class="form-group">
                <label>Person Capacity</label><span class="star">&nbsp;*</span>
                <input name='capacity' id="capacity" class="form-control" value="" placeholder="Vehicle Number" onkeypress="return isNumberKey(event);" >
                 @if ($errors->has('vehicle_number'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_number') }}</strong>
                    </span>
                 @endif
              </div>

               <div class="form-group">
                <label>Base Fare</label><span class="star">&nbsp;*</span>
                <input  onkeypress="return isNumberKey(event);"  name='basefare' id="basefare" class="form-control" value="" placeholder="Vehicle Number">
                 @if ($errors->has('vehicle_number'))
                    <span class="star help-block">
                        <strong>{{ $errors->first('vehicle_number') }}</strong>
                    </span>
                 @endif
              </div>
        
                <div> 
                  <button style="width: 100px;" type="button" class="btn btn-block btn-primary btn-sm">Add</button>
                </div>


            </div>
            <div class="col-md-6"> </div>
         
       

             
           
          </form>
       </div>
      </div>
      <!-- /.box -->
      <script type="text/javascript">
        function preview_image() 
          {
             var total_file=document.getElementById("upload_file").files.length;
             for(var i=0;i<total_file;i++)
             {
              $('#image_preview').append("<img src='"+URL.createObjectURL(event.target.files[i])+"'>&nbsp;&nbsp;");
             }
          }
       </script>
       <style type="text/css">
          #image_preview img{
                 width: 100px;
                 height: 100px;
          } 
       </style>
@endsection

<script type="text/javascript">
  function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}
 


</script>