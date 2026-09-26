@extends('admin.layouts.empty', ['paceTop' => true])

@section('title', 'Login Page')

@section('content')
	<!-- begin login-cover -->
	<div class="login-cover">
		<div class="login-cover-image" style="background-image: url(/assets/admin/assets/img/login-bg/login-bg-17.jpg)" data-id="login-cover-image"></div>
		<div class="login-cover-bg"></div>
	</div>
	<!-- end login-cover -->
	
	<!-- begin login -->
	<div class="login login-v2" data-pageload-addclass="animated fadeIn">
		<!-- begin brand -->
		<div class="login-header">
			<div class="brand">
			
				<!-- <span class="logo"></span> <b>Green Light</b>  -->
				<span> <img src="/assets/images/greenlight_logo.png" alt="Can't load Greenlight Logo"width="27" height="27"></span> 
					<b>Green Light</b>
				<small> Admin Login</small>
			</div>
			<div class="icon">
				<i class="fa fa-lock"></i>
			</div>
		</div>
		<!-- end brand -->
		<!-- begin login-content -->
		<div class="login-content">
			<form method="POST" action="{{route('admin.login')}}" class="margin-bottom-0">
				<div class="form-group m-b-20">
					<input type="text" name = "email" class="form-control form-control-lg" placeholder="Email Address" required />
				</div>
				<div class="form-group m-b-20">
					<input type="password"  name = "password" class="form-control form-control-lg" placeholder="Password" required />
				</div>
				<div class="checkbox checkbox-css m-b-20">
					<input type="checkbox" id="remember_checkbox" /> 
					<label for="remember_checkbox">
						Remember Me
					</label>
				</div>
				<div class="login-buttons">
					<button type="submit" class="btn btn-success btn-block btn-lg">Sign me in</button>
				</div>
                <!-- Removed following div from Theme Varsha -->
				<!-- <div class="m-t-20">
					Not a member yet? Click <a href="javascript:;">here</a> to register.
				</div> -->
			</form>
		</div>
		<!-- end login-content -->
	</div>
	<!-- end login -->
	
	<!-- begin login-bg -->

	<!-- Commented By Varsha Start
	<ul class="login-bg-list clearfix">
		<li class="active"><a href="javascript:;" data-click="change-bg" data-img="/assets/admin/assets/img/login-bg/login-bg-17.jpg" style="background-image: url(/assets/admin/assets/img/login-bg/login-bg-17.jpg)"></a></li>
		<li><a href="javascript:;" data-click="change-bg" data-img="/assets/admin/assets/img/login-bg/login-bg-16.jpg" style="background-image: url(/assets/admin/assets/img/login-bg/login-bg-16.jpg)"></a></li>
		<li><a href="javascript:;" data-click="change-bg" data-img="/assets/admin/assets/img/login-bg/login-bg-15.jpg" style="background-image: url(/assets/admin/assets/img/login-bg/login-bg-15.jpg)"></a></li>
		<li><a href="javascript:;" data-click="change-bg" data-img="/assets/admin/assets/img/login-bg/login-bg-14.jpg" style="background-image: url(/assets/admin/assets/img/login-bg/login-bg-14.jpg)"></a></li>
		<li><a href="javascript:;" data-click="change-bg" data-img="/assets/admin/assets/img/login-bg/login-bg-13.jpg" style="background-image: url(/assets/admin/assets/img/login-bg/login-bg-13.jpg)"></a></li>
		<li><a href="javascript:;" data-click="change-bg" data-img="/assets/admin/assets/img/login-bg/login-bg-12.jpg" style="background-image: url(/assets/admin/assets/img/login-bg/login-bg-12.jpg)"></a></li>
	</ul>
	Commented By Varsha End -->

	<!-- end login-bg -->
@endsection

@push('scripts')
	<script src="/assets/admin/assets/js/demo/login-v2.demo.js"></script>
@endpush



