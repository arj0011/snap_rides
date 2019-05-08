<!DOCTYPE html>
<html>
<head>
  <title>Faq</title>
  <link rel="stylesheet" type="text/css" href="http://dev.tekzee.in/snap_rides/Admin/fonts/font-awesome.min.css">
  <style type="text/css">
  
body {
  background: #fff;
  font-family: sans-serif;
}
h2 {
  color: #403A3A;
  font-weight: 300;
  text-align: center;
  margin: 50px 0 30px;
}
.dropdown {
  display: block;
  width: 90%;
  margin: 0 auto 10px auto;
}
.dropdown__top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  /*color: #242424;*/
  color: #FFFFFF;
  padding: 15px;
  box-sizing: border-box;
  font: 600 14px/22px sans-serif;
  /*background-color: #F9CF00;*/
  background-color: #2196F3;
  cursor: pointer;
  transition: background 0.3s ease;
  will-change: background;
  &:hover {
    background: #0d5871;
  }
  &::after {
    content: "\f13a";
    font: 20px "FontAwesome";
    transition: transform 0.5s ease;
    will-change: transform;
  }
}
.open .dropdown__top::after {
  -webkit-transform: rotate(180deg);
  transform: rotate(180deg);
}
.dropdown__btm {
  background: #f2f2f2;
  color: #555;
  font-size: 15px;
  line-height: 1.4;
  box-sizing: border-box;
  padding: 15px;
  display: none;
  word-break: break-all;
}

</style>
<script type="text/javascript" src="http://dev.tekzee.in/snap_rides/Admin/js/jquery.min.js"></script>
<script type="text/javascript">
  function changeIcon(id){
      var pre= $("#spn"+id).attr("src");
      if(pre=="Admin/img/close.png"){
         $(".spnall").attr("src", "Admin/img/close.png");
        $("#spn"+id).attr("src", "Admin/img/open.png");
      }else{
        $("#spn"+id).attr("src", "Admin/img/close.png");
      }
  }
  $('document').ready(function($){
  $('.dropdown__top').click(function(){
    if ($(this).parent(".dropdown").hasClass("open")) {
      $(this).parent(".dropdown").removeClass("open");
      $(this).siblings(".dropdown__btm").slideUp(500);
    } else {
      $(".dropdown").removeClass("open");
      $(".dropdown .dropdown__btm").slideUp(500);
      $(this).parent(".dropdown").addClass("open");
      $(this).siblings(".dropdown__btm").slideDown(500);
    }
  })
});
</script>
</head>
<body>

<center> <h4>Top Frequently Asked Question</h4></center>
<?php  //echo "<pre>"; print_r($data); ?>
<?php foreach ($data as $key => $value) {?>
  <div class="dropdown">
    <div class="dropdown__top" onclick="changeIcon(<?php echo $value->id; ?>);" >
      <?php echo $value->question; ?>   
       <span > 
        <img class="spnall" width="16px;" height="12px;" id="spn<?php echo $value->id; ?>"  src="Admin/img/close.png">
       </span>
      </div>
    <div class="dropdown__btm">
      
      <?php   echo $value->answer ;?>
    </div>
  </div>
<?php } ?>


</body>
</html>








 


