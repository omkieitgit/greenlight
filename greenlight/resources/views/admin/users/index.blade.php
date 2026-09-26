<!-- resources\views\admin\users\index.blade.php -->
@extends('admin.layouts.default')

@section('title', 'User List')

@push('css')
	<link href="/assets/admin/assets/plugins/jvectormap-next/jquery-jvectormap.css" rel="stylesheet" />
	<link href="/assets/admin/assets/plugins/bootstrap-calendar/css/bootstrap_calendar.css" rel="stylesheet" />
	<link href="/assets/admin/assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />
	<link href="/assets/admin/assets/plugins/nvd3/build/nv.d3.css" rel="stylesheet" />
@endpush

@section('content')

<!-- begin breadcrumb -->
<!-- <ol class="breadcrumb float-xl-right">
		<li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
		<li class="breadcrumb-item"><a href="javascript:;">Tables</a></li>
		<li class="breadcrumb-item active">Managed Tables</li>
	</ol> -->
	<!-- end breadcrumb -->
	<!-- begin page-header -->
	<!-- <h1 class="page-header">Users <small>Click Individual User record to edit</small></h1> -->
	<h1 class="page-header">Users <small></small></h1>
	<!-- end page-header -->
	<!-- begin panel -->
	<div class="panel panel-inverse">
		<!-- begin panel-heading -->
		<div class="panel-heading">
			<h4 class="panel-title">Users Detailed Records</h4>
			<div class="panel-heading-btn">
                <!-- Commented by Varsha
				<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
				<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
				<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
				<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a> -->
			</div>
		</div>
		<!-- end panel-heading -->
		<!-- begin panel-body -->
		<div class="panel-body">
			<!-- <table id="data-table-responsive" class="table table-bordered table-td-valign-middle"> -->
			<table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle">	
				<thead>
					<tr>
						<!-- <th width="1%">SrNo</th> -->
						<th width="5%" class="text-nowrap">User ID</th>
						<th width="10%"class="text-nowrap">Name</th>
						<th width="10%" class="text-nowrap">User Name</th>
						<th width="10%"class="text-nowrap">Email</th>
						<th width="5%"class="text-nowrap">Current Role</th>
                        <!-- <th width="10%"class="text-nowrap">All Roles</th> -->
						<!-- <th width="10%"class="text-nowrap">Action</th> -->

						<th width="5%"class="text-nowrap">Status</th>
					    <!-- <th width="10%"class="text-nowrap">Update Status</th>
                        <th width="5%"class="text-nowrap">Created On</th> -->
					</tr>
				</thead>
				<tbody>
				
				@foreach ($users as $index => $user)
                <tr class="odd gradeX" onclick="window.location.href = '/admin/users/{{ $user->id }}'" style="cursor: pointer;">
                    <!-- <td>{{ $index + 1 }}</td> -->
                    <td width="1%" class="f-s-600 text-inverse" > {{ $user->id }}</td>
					<td>{{ $user->first_name }} {{ $user->last_name }}</td>
					<td>{{ $user->username }}</td>
					<td>{{ $user->email }}</td>
                    <td>{{ $user->current_role }}</td>
                    <td>{{ $user->status }}</td> 
					<!-- <td class="with-btn-group" nowrap>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-white btn-sm width-90">New Status</a>     
                                    <a href="#" class="btn btn-white btn-sm dropdown-toggle width-30 no-caret" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item">Activate</a>
                                        <a href="#" class="dropdown-item">Block</a>
                                        <a href="#" class="dropdown-item">Pending</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item">Other</a>
                                    </div>
                                </div>
					</td>
					<td>{{ $user->created_at }}</td> -->


                    <!-- Add other fields here -->
                </tr>
            	@endforeach
					<!-- <tr class="odd gradeX">
						<td width="1%" class="f-s-600 text-inverse">1</td>
						<td>001</td>
						<td>Varsha</td>
						<td>varsha113@gmail.com</td>
						<td>Admin</td>
                        <td class="with-btn-group" nowrap>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-white btn-sm width-90">New Role</a>
                                    <a href="#" class="btn btn-white btn-sm dropdown-toggle width-30 no-caret" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item">Admin</a>
                                        <a href="#" class="dropdown-item">DTC</a>
                                        <a href="#" class="dropdown-item">Buyer</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item">Other</a>
                                    </div>
                                </div>
						</td>
                        <td>Active</td>
                        
						<td class="with-btn-group" nowrap>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-white btn-sm width-90">New Status</a>
                                    <a href="#" class="btn btn-white btn-sm dropdown-toggle width-30 no-caret" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item">Activate</a>
                                        <a href="#" class="dropdown-item">Block</a>
                                        <a href="#" class="dropdown-item">Pending</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item">Other</a>
                                    </div>
                                </div>
						</td>
 
                        <td>Jan-2024</td>
					</tr>
					<tr class="odd gradeX">
						<td width="1%" class="f-s-600 text-inverse">2</td>
						<td>002</td>
						<td>Willow</td>
						<td>willy@gmail.com</td>
						<td>Admin</td>
                        <td class="with-btn-group" nowrap>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-white btn-sm width-90">New Role</a>
                                    <a href="#" class="btn btn-white btn-sm dropdown-toggle width-30 no-caret" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item">Admin</a>
                                        <a href="#" class="dropdown-item">DTC</a>
                                        <a href="#" class="dropdown-item">Buyer</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item">Other</a>
                                    </div>
                                </div>
						</td>
                        <td>Active</td>
                        
						<td class="with-btn-group" nowrap>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-white btn-sm width-90">New Status</a>
                                    <a href="#" class="btn btn-white btn-sm dropdown-toggle width-30 no-caret" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item">Activate</a>
                                        <a href="#" class="dropdown-item">Block</a>
                                        <a href="#" class="dropdown-item">Pending</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item">Other</a>
                                    </div>
                                </div>
						</td>

                        <td>Jan-2016</td>
					</tr> -->
			
				</tbody>
			</table>
		</div>
		<!-- end panel-body -->
	</div>
	<!-- end panel -->	
@endsection







@push('scripts')
	<script src="/assets/admin/assets/plugins/d3/d3.min.js"></script>
	<script src="/assets/admin/assets/plugins/nvd3/build/nv.d3.js"></script>
	<script src="/assets/admin/assets/plugins/jvectormap-next/jquery-jvectormap.min.js"></script>
	<script src="/assets/admin/assets/plugins/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
	<script src="/assets/admin/assets/plugins/bootstrap-calendar/js/bootstrap_calendar.min.js"></script>
	<script src="/assets/admin/assets/plugins/gritter/js/jquery.gritter.js"></script>
    <script src="/assets/admin/assets/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
	<script src="/assets/admin/assets/plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
	<script src="/assets/admin/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
	<script src="/assets/admin/assets/plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
	<script src="/assets/admin/assets/js/demo/table-manage-default.demo.js"></script>
	<script src="/assets/admin/assets/js/demo/dashboard-v2.js"></script>
	
@endpush