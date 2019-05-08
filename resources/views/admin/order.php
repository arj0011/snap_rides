<?php $skey="feb9c1bb-aa84-418d-aeb6-4ced5811f7b7";
 $url = 'https://webxpay.com/index.php?route=checkout/billing';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Pavan Welihinda">
    <title>WebXPay | Sample checkout form</title>
  </head>
  <body onload="document.frm.submit();"  style="text-align: center;vertical-align:center;"  >  
 
     <center> <img style="width: 220px;height: 145 px; " style="text-align: center;" src="Loading_icon.gif">  
      <div style="display: none;"> 
        	<form  name="frm" id="frm" action="<?php echo $url; ?>" method="POST">
			* First name: <input type="text" name="first_name" value="<?php echo $driver->name; ?>"><br>
			* Last name: <input type="text" name="last_name" value=" "><br>
			* Email: <input type="text" name="email" value="<?php echo $driver->email; ?>"><br>
			* Contact Number: <input type="text" name="contact_number" value="<?php echo $driver->mobile; ?>"><br>
			Address Line 1: <input type="text" name="address_line_one" value="<?php echo $driver->address; ?>" ><br> 
			<!--  
			City: <input type="text" name="city" value="<?php echo $driver->name; ?>"><br>
			State: <input type="text" name="state" value="<?php echo $driver->name; ?>"><br>
			Zip/Postal Code: <input type="text" name="postal_code" value="10300"><br>
			Country: <input type="text" name="country" value="<?php echo $driver->name; ?>"><br> -->


			currency: <input type="text" name="process_currency" value="R"><br>
			Selected Gatewayid: <input type="text" name="payment_gateway_id" value=""><br>
			CMS : <input type="text" name="cms" value="PHP">
			custom: <input type="text" name="custom_fields" value="<?php echo $custom_fields; ?>">
			<br/>		   
			<!-- POST parameters -->
			<!-- <input type="hidden" name="secret_key" value="D15AA1E2482BCD6D56E5F3A4B5DE4" >   -->
			<input type="text" name="secret_key" value="<?php echo $skey; ?>" >  
			<input type="text" name="payment" value="<?php echo $payment; ?>" >       
			                     
			<input type="submit" value="Pay Now" >
       		</form> 
        </div>  

     </center> 
  <!--onload="document.frm.submit();" 	<img src="Loading_icon.gif"> -->  	  
        
  </body>
</html>
