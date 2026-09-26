@php
	$headerClass = (!empty($headerInverse)) ? 'navbar-inverse ' : 'navbar-default ';
	$headerMenu = (!empty($headerMenu)) ? $headerMenu : '';
	$headerMegaMenu = (!empty($headerMegaMenu)) ? $headerMegaMenu : ''; 
	$headerTopMenu = (!empty($headerTopMenu)) ? $headerTopMenu : '';
@endphp
<!-- begin #header -->
<div id="header" class="header {{ $headerClass }}">
	<!-- begin navbar-header -->
	<div class="navbar-header">
		<!-- @if ($sidebarTwo)
		<button type="button" class="navbar-toggle pull-left" data-click="right-sidebar-toggled">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		@endif -->
		<a class="navbar-brand">
			<!-- Commented by Varsha <span class="navbar-logo"></span>  -->
			<span > <img src="/assets/images/greenlight_logo.png" alt="Can't load Greenlight Logo" width="30" height="27"></span> 
			
			<b> GreenLight</b> &nbsp Admin</a>
		<!-- @if ($headerMegaMenu)
			<button type="button" class="navbar-toggle pt-0 pb-0 mr-0" data-toggle="collapse" data-target="#top-navbar">
				<span class="fa-stack fa-lg text-inverse">
					<i class="far fa-square fa-stack-2x"></i>
					<i class="fa fa-cog fa-stack-1x"></i>
				</span>
			</button>
		@endif -->
		<!-- @if (!$sidebarHide && $topMenu)
			<button type="button" class="navbar-toggle pt-0 pb-0 mr-0 collapsed" data-click="top-menu-toggled">
				<span class="fa-stack fa-lg text-inverse">
					<i class="far fa-square fa-stack-2x"></i>
					<i class="fa fa-cog fa-stack-1x"></i>
				</span>
			</button>
		@endif -->
		<!-- @if (!$sidebarHide && !$headerTopMenu)
		<button type="button" class="navbar-toggle" data-click="sidebar-toggled">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		@endif -->
		<!-- @if ($headerTopMenu)
			<button type="button" class="navbar-toggle" data-click="top-menu-toggled">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		@endif -->
	</div>
	<!-- end navbar-header -->
	
	@includeWhen($headerMegaMenu, 'includes.header-mega-menu')
	
	<!-- begin header-nav -->
	<ul class="navbar-nav navbar-right">
		<!-- <li class="navbar-form">
			<form action="" method="POST" name="search_form">
				<div class="form-group">
					<input type="text" class="form-control" placeholder="Enter keyword" />
					<button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
				</div>
			</form>
		</li> -->
		
		<!-- @isset($headerLanguageBar)
		<li class="dropdown navbar-language">
			<a href="#" class="dropdown-toggle pr-1 pl-1 pr-sm-3 pl-sm-3" data-toggle="dropdown">
				<span class="flag-icon flag-icon-us" title="us"></span>
				<span class="name d-none d-sm-inline">EN</span> <b class="caret"></b>
			</a>
			<div class="dropdown-menu">
				<a href="javascript:;" class="dropdown-item"><span class="flag-icon flag-icon-us" title="us"></span> English</a>
				<a href="javascript:;" class="dropdown-item"><span class="flag-icon flag-icon-cn" title="cn"></span> Chinese</a>
				<a href="javascript:;" class="dropdown-item"><span class="flag-icon flag-icon-jp" title="jp"></span> Japanese</a>
				<a href="javascript:;" class="dropdown-item"><span class="flag-icon flag-icon-be" title="be"></span> Belgium</a>
				<div class="dropdown-divider"></div>
				<a href="javascript:;" class="dropdown-item text-center">more options</a>
			</div>
		</li>
		@endisset -->
		<li class="dropdown navbar-user">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<!-- <img src="/assets/admin/assets/img/user/user-13.jpg" alt="" /> -->
						<!-- <i class="fas fa-user"></i> -->
						<i class="fa fa-user"></i>
				<span class="d-none d-md-inline">{{$adminUser->first_name}}</span>
				 <!-- <b class="caret"></b> -->
			</a>
			<div class="dropdown-menu dropdown-menu-right">
				<!-- <a href="javascript:;" class="dropdown-item">Edit Profile</a>
				<a href="javascript:;" class="dropdown-item"><span class="badge badge-danger pull-right">2</span> Inbox</a>
				<a href="javascript:;" class="dropdown-item">Calendar</a>
				<a href="javascript:;" class="dropdown-item">Setting</a> -->
				<div class="dropdown-divider"></div>
				<!-- <a href="javascript:;" class="dropdown-item">Log Out</a> -->
				<a href="javascript:;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">Log Out</a>
					<form id="logout-form" action="{{ route('admin.admin.logout') }}" method="POST" style="display: none;">
						@csrf
					</form>
				
			</div>
		</li>
		@if($sidebarTwo)
		<li class="divider d-none d-md-block"></li>
		<li class="d-none d-md-block">
			<a href="javascript:;" data-click="right-sidebar-toggled" class="f-s-14">
				<i class="fa fa-th"></i>
			</a>
		</li>
		@endif
	</ul>
	<!-- end header navigation right -->
</div>
<!-- end #header -->
