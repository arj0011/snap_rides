<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Complex Polylines</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div id="map" style="width: 80%;height: 80%;"></div>
 
   <!--  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap">
    </script> -->
        <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFwsRyc4HATOYM5ZjS3kFsKfj4EUoFRqs&callback=initMap">
    </script>
    <script type="text/javascript" src="http://localhost:8000/Admin/js/jquery.min.js"></script>
       <script>

      // This example creates an interactive map which constructs a polyline based on
      // user clicks. Note that the polyline only appears once its path property
      // contains two LatLng coordinates.

      var poly;
      var map;

      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 10,
          //center: {lat: 41.879, lng: -87.624}  // Center the map on Chicago, USA.
          center: {lat:22.7196, lng: 75.8577}
        });

        poly = new google.maps.Polyline({
          strokeColor: '#000000',
          strokeOpacity: 1.0,
          strokeWeight: 3
        });
        poly.setMap(map);
        // Add a listener for the click event
        //map.addListener('click', addLatLng);
        //setInterval (moveDriver(),2000);
         setInterval(moveDriver,2000);
         
      }

      function moveDriver(){
        var booking_id = "<?php echo $booking_id ?>";  
        var url= "<?php echo url('/') ?>/getRoutes";
        
        $.post(url,{id:booking_id},function(obj){
            //var obj= json.parse(data);
            var id=obj.id;
            //var latlongs="("+obj.lat+', '+obj.long+")";
            //var latlongs=obj.lat+', '+obj.long;
            //  var latlongs= '(41.80329076970831, -86.14908544921877)'; 
            var path = poly.getPath();
            var centerPoint = new google.maps.LatLng(obj.lat,obj.long);
            //alert(centerPoint);
            path.push(centerPoint);
            var marker = new google.maps.Marker({
              position: centerPoint,
              title: '#' + path.getLength(),
              map: map
            });

        })

      }


      // Handles click events on a map, and adds a new point to the Polyline.
      function addLatLng(event) {
        var path = poly.getPath();
        // Because path is an MVCArray, we can simply append a new coordinate
        // and it will automatically appear.
        //alert(event.latLng);
        
        path.push(event.latLng);

        // Add a new marker at the new plotted point on the polyline.
        var marker = new google.maps.Marker({
          position: event.latLng,
          title: '#' + path.getLength(),
          map: map
        });
      }
/*      function addLatLng(event) {
        var path = poly.getPath();
        // Because path is an MVCArray, we can simply append a new coordinate
        // and it will automatically appear.
        //alert(event.latLng);
        path.push(event.latLng);

        // Add a new marker at the new plotted point on the polyline.
        var marker = new google.maps.Marker({
          position: event.latLng,
          title: '#' + path.getLength(),
          map: map
        });
      }*/
    </script>
  </body>
</html>