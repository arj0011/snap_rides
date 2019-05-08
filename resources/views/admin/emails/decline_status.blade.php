<!DOCTYPE html>
<html>
<head>
	<title>Snap Rides App</title>
</head>
<body>
    Hello {{$name}},   
       <p>Your account is declined.</p>
       @if($reason != '')
       <p>{{ $reason }}</p>
       @endif
       <p>For more information contact your Administrator</p>
    <p><b>Team Snap Rides</b></p>
</body>
</html>