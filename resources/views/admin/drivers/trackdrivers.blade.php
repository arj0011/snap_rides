@extends('admin.layouts.app')
@section('title', 'Drivers')
@section('breadcrumb')
      <h1>Realtime Tracking</h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
        <li class="active">Realtime Tracking </li>
      </ol>
@endsection
@section('content')   
  <div id="page-wrapper">
    <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
              <div class="box-body">
            <form class="form-inline" action="{{  route('driver/tracking') }}" method="get" autocomplete="off">
              {{ csrf_field() }}
                <div class="input-group input-group-xs">
                  <div class="input-group-btn">
                    <select name="p" class="form-control" id="selectInput">
                      <option value="">All</option>
                      <option  @isset ($param) @if($param == 'distance') {{ "selected" }} @endif @endisset value="distance">Distance</option>
                      <option  @isset ($param) @if($param == 'driver') {{ "selected" }} @endif @endisset value="driver">Driver Name</option>
                      <option  @isset ($param) @if($param == 'mobile') {{ "selected" }} @endif @endisset value="mobile">Driver Mobile</option>
                    </select>
                  </div>
                <!-- /btn-group -->
                   <input name="q" type="text" class="form-control" value="@if(isset($paramVal)){{ $paramVal }} @endif" id="textInput" style="@if(isset($paramVal)) {{ 'display: block;'}} @else  {{ 'display: none;'}}@endif">
                  </div>
                  <div class="form-group">
                    <select name="veh_cat" class="form-control">
                      <option value="">All</option>
                      <option value="1" @isset ($vcategory) @if($vcategory == '1') {{ "selected" }} @endif @endisset>Sedan</option>
                      <option value="2" @isset ($vcategory) @if($vcategory == '2') {{ "selected" }} @endif @endisset>7 Seater</option>
                      <option value="3" @isset ($vcategory) @if($vcategory == '3') {{ "selected" }} @endif @endisset>Delux</option>
                    </select>
                  </div>
                <div class="form-group">
                     <button type="submit" class="btn btn-flat margin" data-toggle="tooltip" title="Search">Search</button>
                    <a href="{{ route('driver/tracking') }}" type="reset" class="btn btn-default btn-flat" data-toggle="tooltip" title="Reset">Reset</a>
                </div>
            </form>
         
              </div>
              <!-- /.box-body -->
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>  
     </div>

    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Driver Tracking</h3>
          </div>
          <div class="box-body table-responsive no-padding">
              <div id="map"></div>  
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
    </div>
  </div>
  @php 
  $base_url = URL::to('/');
  @endphp
@endsection
@section('css-script')
<style>
/* Always set the map height explicitly to define the size of the div
 * element that contains the map. */
#map {
  height: 56%;
  width: 100%;
}
/* Optional: Makes the sample page fill the window. */
</style>
@endsection     
  
  <script>

    function initMap() {
      
      var map = new google.maps.Map(document.getElementById('map'), {
        center: new google.maps.LatLng(-33.927987, 18.421647),
        zoom: 12
      });
      var infoWindow = new google.maps.InfoWindow;

      var data = '<?php echo json_encode($drivers);?>';
      driver = JSON.parse(data);
        
      for (var i = 0; i < driver.length; i++){ 

        var name = driver[i].name;
        var mobile = driver[i].mobile;
        var type = driver[i].type;
        
        var point = new google.maps.LatLng(
            parseFloat(driver[i].latitude),
            parseFloat(driver[i].longitude)
        );

        var infowincontent = document.createElement('div');
        var strong = document.createElement('strong');
        strong.textContent = name;
        infowincontent.appendChild(strong);
        infowincontent.appendChild(document.createElement('br'));

        var text = document.createElement('text');
        text.textContent = mobile;
        infowincontent.appendChild(text);

        icon_path = '<?php echo $base_url;?>'  + '/Admin/categoryImage/';
        
        var icon = {
          url: icon_path + driver[i].type_img,
          // This marker is 20 pixels wide by 32 pixels high.
          size: new google.maps.Size(100, 60),
          // The origin for this image is (0, 0).
          origin: new google.maps.Point(0, 0),
          // The anchor for this image is the base of the flagpole at (0, 32).
          anchor: new google.maps.Point(0, 40),
          scaledSize: new google.maps.Size(40, 40)
        };

        var marker = new google.maps.Marker({
          map: map,
          position: point,
          icon:icon
        });

        //Attach click event to the marker.
        dataa = driver[i];
        (function (marker, dataa) {
          google.maps.event.addListener(marker, "click", function (e) {
          infoWindow.setContent("<div style = 'width:200px;min-height:40px'>Driver : <b>" + dataa.name + "</b><br />Mobile : <b>"+dataa.mobile+"</b><img scr='"+dataa.vehicle_img+"' height='25' width='25' /><br />Vehicle type : <b>"+dataa.vehicle_type+"</b></div>");
          
          infoWindow.open(map, marker);
          });
        })(marker, dataa);
      }
    }

</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBnTKkK26b0bwrCOU8XMoqzpUMVrHnf554&callback=initMap">
</script>


@section('js-script')
    <script type="text/javascript">
      $(document).ready(function(){
        $(document).on('change','#selectInput',function(){
          if($(this).val() != '') 
            $('#textInput').css('display','block');
          else{
            $('#textInput').val('');
            $('#textInput').css('display','none');  
          }
        })
      })
    </script>
@endsection