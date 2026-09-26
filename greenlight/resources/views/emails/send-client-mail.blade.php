          <!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
Hello {{$to_name}},<br><br>
<p>
@if(app()->env != "production")
@foreach($contact_info as $info)
	{{$info}}
@endforeach
@endif
<p>
@foreach($main_message as $message) 
	{{$message}} 
<BR><BR>
@endforeach
</p>
<?php echo $signature?>
</body>
</html>