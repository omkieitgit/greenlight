<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	@include('admin.includes.head')
</head>
@php
	$bodyClass = (!empty($boxedLayout)) ? 'boxed-layout ' : '';
	$bodyClass .= (!empty($paceTop)) ? 'pace-top ' : '';
	$bodyClass .= (!empty($bodyExtraClass)) ? $bodyExtraClass . ' ' : '';
@endphp
<body class="{{ $bodyClass }}">
	@include('admin.includes.component.page-loader')
	
	@yield('content')
			
	@include('admin.includes.page-js')
</body>
</html>
