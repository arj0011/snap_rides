<!DOCTYPE html>
<html>
<head>
  <title>Snap Rides</title>
  <script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId={{$id}}"></script>
</head>
<body>	
<form action="http://snap_rides/paymentResponse" class="paymentWidgets" data-brands="VISA MASTER AMEX">
	{{-- <input type="hidden" name="authentication.userId" value="8ac7a4c86991344b0169966afbdc0662">
	<input type="hidden" name="authentication.password" value="qyyfHCN83e"> --}}
	<input type="hidden" name="authentication.entityId" value="8ac7a4c86991344b0169966b739d0666">
</form>
</body>
</html>