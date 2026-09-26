@extends('admin.layouts.default')

@section('title', 'Admin Dashboard')

@push('css')
	<link href="../assets/admin/assets/plugins/jvectormap-next/jquery-jvectormap.css" rel="stylesheet" />
	<link href="../assets/admin/assets/plugins/bootstrap-calendar/css/bootstrap_calendar.css" rel="stylesheet" />
	<link href="../assets/admin/assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />
	<link href="../assets/admin/assets/plugins/nvd3/build/nv.d3.css" rel="stylesheet" />
@endpush

@section('content')
	<!-- begin breadcrumb -->
	<ol class="breadcrumb float-xl-right">
		<li class="breadcrumb-item"><a href="javascript:;">Admin</a></li>
		<li class="breadcrumb-item"><a href="javascript:;">Dashboard</a></li>
		<li class="breadcrumb-item active">Admin Dashboard</li>
	</ol>
	<!-- end breadcrumb -->
	<!-- begin page-header -->
	<h1 class="page-header">Admin Dashboard<small></small></h1>
	<!-- end page-header -->
	<!-- begin row -->
	<div class="row">
		<!-- begin col-3 -->
		<div class="col-xl-3 col-md-6">
			<div class="widget widget-stats bg-teal">
				<div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
				<div class="stats-content">
					<div class="stats-title">NO. OF REGISTERED USERS</div>
					<div class="stats-number">{{$userStatistics['total_users']}}</div>
					<div class="stats-progress progress">
						<div class="progress-bar" style="width: 100.0%;"></div>
					</div>
					<div class="stats-desc">  
					@if ($userStatistics['total_users'] > 0)
    						<p>Percentage of Registered Users: {{ number_format(($userStatistics['total_users'] / $userStatistics['total_users']) * 100, 2) }}%</p>
					@else
   							 <p>Percentage of Registered Users: 0%</p>
					@endif	
					
					</div>
				</div>
			</div>
		</div>
		<!-- end col-3 -->
		<!-- begin col-3 -->
		<div class="col-xl-3 col-md-6">
			<div class="widget widget-stats bg-blue">
				<div class="stats-icon stats-icon-lg"><i class="fa fa-dollar-sign fa-fw"></i></div>
				<div class="stats-content">
					<div class="stats-title">NO. OF ACTIVE USERS</div>
					<div class="stats-number">{{$userStatistics['total_active_users']}}</div>
					<div class="stats-progress progress">
						<div class="progress-bar" style="width: 90.0%;"></div>
					</div>
					<div class="stats-desc">
					@if ($userStatistics['total_users'] > 0)
    						<p>Percentage of Active Users: {{ number_format(($userStatistics['total_active_users'] / $userStatistics['total_users']) * 100, 2) }}%</p>
					@else
   							 <p>Percentage of Active Users: 0%</p>
					@endif		
					
					</div>
				</div>
			</div>
		</div>
		<!-- end col-3 -->
		<!-- begin col-3 -->
		<div class="col-xl-3 col-md-6">
			<div class="widget widget-stats bg-indigo">
				<div class="stats-icon stats-icon-lg"><i class="fa fa-archive fa-fw"></i></div>
				<div class="stats-content">
					<div class="stats-title">NO. OF BLOCKED USERS</div>
					<div class="stats-number">{{$userStatistics['total_blocked_users']}}</div>
					<div class="stats-progress progress">
						<div class="progress-bar" style="width: 10.0%;"></div>
					</div>
					<div class="stats-desc">
					@if ($userStatistics['total_users'] > 0)
    						<p>Percentage of Blocked Users: {{ number_format(($userStatistics['total_blocked_users'] / $userStatistics['total_users']) * 100, 2) }}%</p>
					@else
   							 <p>Percentage of Blocked Users: 0%</p>
					@endif		
					
				   </div>
				</div>
			</div>
		</div>
		<!-- end col-3 -->
		<!-- begin col-3 -->
		<div class="col-xl-3 col-md-6">
			<div class="widget widget-stats bg-dark">
				<div class="stats-icon stats-icon-lg"><i class="fa fa-comment-alt fa-fw"></i></div>
				<div class="stats-content">
					<div class="stats-title">No. OF PENDING USERS</div>
					<div class="stats-number">{{$userStatistics['total_pending_users']}}</div>
					<div class="stats-progress progress">
						<div class="progress-bar" style="width: 0.5%;"></div>
					</div>
					<div class="stats-desc">
					@if ($userStatistics['total_users'] > 0)
    						<p>Percentage of Pending Users: {{ number_format(($userStatistics['total_pending_users'] / $userStatistics['total_users']) * 100, 2) }}%</p>
					@else
   							 <p>Percentage of Pending Users: 0%</p>
					@endif
					</div>
				</div>
			</div>
		</div>
		<!-- end col-3 -->
		<!-- begin col-3 -->
		<!-- <div class="col-xl-3 col-md-6">
		<div class="widget widget-stats bg-teal">
				<div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
				<div class="stats-content">
					<div class="stats-title">NO. OF DTC</div>
					<div class="stats-number">{{$userStatistics['total_first_dtc_users']}}</div>
					<div class="stats-progress progress">
						<div class="progress-bar" style="width: 5%;"></div>
					</div>
					<div class="stats-desc">
					@if ($userStatistics['total_users'] > 0)
    						<p>Percentage of DTCs: {{ number_format(($userStatistics['total_first_dtc_users'] / $userStatistics['total_users']) * 100, 2) }}%</p>
					@else
   							 <p>Percentage of DTCs: 0%</p>
					@endif
					</div>
				</div>
			</div>
		</div> -->
		<!-- end col-3 -->

		<!-- begin col-3 -->
		<!-- <div class="col-xl-3 col-md-6">
			<div class="widget widget-stats bg-blue">
				<div class="stats-icon stats-icon-lg"><i class="fa fa-dollar-sign fa-fw"></i></div>
				<div class="stats-content">
					<div class="stats-title">NO. OF WHOLESALE BUYER</div>
					<div class="stats-number">{{$userStatistics['total_wholesale_buyer_users']}}</div>
					<div class="stats-progress progress">
						<div class="progress-bar" style="width: 5%;"></div>
					</div>
					<div class="stats-desc">
					@if ($userStatistics['total_wholesale_buyer_users'] > 0)
    						<p>Percentage of Wholesale Buyers: {{ number_format(($userStatistics['total_wholesale_buyer_users'] / $userStatistics['total_users']) * 100, 2) }}%</p>
					@else
   							 <p>Percentage of Wholesale Buyers: 0%</p>
					@endif
				</div>
				</div>
			</div>
		</div> -->
		<!-- end col-3 -->
	</div>
	<!-- end row -->

	<!-- begin row -->
	<div class="row" style = "justify-content: center; align-items: center;">
		<!-- begin col-8 -->
		<div class="col-xl-20"  >
			<div class="widget-chart with-sidebar inverse-mode">
				<!-- <div class="widget-chart-content bg-dark">
					<h4 class="chart-title">
						Visitors Analytics
						<small>Where do our visitors come from</small>
					</h4>
					<div id="visitors-line-chart" class="widget-chart-full-width nvd3-inverse-mode" style="height: 260px;"></div>
				</div> -->
				<div class="widget-chart-sidebar bg-dark-darker" style="height: 330px; width:425px;">
					<div class="chart-number" style = "justify-content: center; align-items: center;">
					{{$userStatistics['total_users']}}
						<small>Total Users</small>
					</div>
						<!-- <div class="flex-grow-1 d-flex align-items-center" >
							<div id="visitors-donut-chart" class="nvd3-inverse-mode"style="height: 350px; width:550px; justify-content: right;" ></div>
						</div> -->
						<div style="display: flex; width: 400px;">
						<!-- <div> -->
							<div style="flex: 1; padding: 0 5px; margin-right: 0px;">
								<ul class="chart-legend f-s-11">
									<li><i class="fa fa-circle fa-fw text-blue f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_admin_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_admin_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Admin </span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i>
									{{$userStatistics['total_web_team_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_web_team_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Web Team </span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i>
									{{$userStatistics['total_chief_dca_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_chief_dca_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif <span>Chief DCA </span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_acquisition_manager_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_acquisition_manager_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Acqu. Mng.</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i>
									{{$userStatistics['total_first_dtc_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_first_dtc_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>First DTC</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i>
									{{$userStatistics['total_second_dca_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_second_dca_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Second DCA</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_third_dca_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_third_dca_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Third DCA</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_nos_by_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_nos_by_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Nos By</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_im_by_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_im_by_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>IM By</span></li>
									
								</ul>
							</div>
							<div style="flex: 1; padding: 0 5px; margin-left: 0px;">
								<ul class="chart-legend f-s-11">
							
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_trustee_caller_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_trustee_caller_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif 
									<span>Trustee Caller</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_accounting_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_accounting_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Accounting</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_home_buyer_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_home_buyer_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Home Buyer</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_wholesale_buyer_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_wholesale_buyer_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>WholeSale Buyer</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_fund_lander_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_fund_lander_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Fund Lander</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_auction_by_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_auction_by_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Auction By</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i>
									{{$userStatistics['total_im_checked_by_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_im_checked_by_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>IM Checked By</span></li>
									<li><i class="fa fa-circle fa-fw text-teal f-s-9 m-r-5 t-minus-1"></i> 
									{{$userStatistics['total_sub_to_users']}}
											@if ($userStatistics['total_users'] > 0)
													# {{ number_format(($userStatistics['total_sub_to_users'] / $userStatistics['total_users']) * 100, 2) }}%
											@else
													# 0 %
											@endif
									<span>Sub To</span></li>
								</ul>
							</div>
						</div>
				</div>
			</div>
		</div>
		<!-- end col-8 -->
	
@endsection



@push('scripts')
<!-- note : ../ is additionally required as dashboard.blade.php is placed inside admin folder in view -->
	<script src="../assets/admin/assets/plugins/d3/d3.min.js"></script>   
	<script src="../assets/admin/assets/plugins/nvd3/build/nv.d3.js"></script>
	<script src="../assets/admin/assets/plugins/jvectormap-next/jquery-jvectormap.min.js"></script>
	<script src="../assets/admin/assets/plugins/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
	<script src="../assets/admin/assets/plugins/bootstrap-calendar/js/bootstrap_calendar.min.js"></script>
	<script src="../assets/admin/assets/plugins/gritter/js/jquery.gritter.js"></script>
	<script src="../assets/admin/assets/js/demo/dashboard-v2.js"></script>
@endpush