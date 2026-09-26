<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'menu' => [[
		// 'icon' => 'fa fa-th-large',
    // 'icon' => 'fas fa-tachometer-alt',
    'icon' => 'fas fa-columns',
		'title' => 'Dashboard',
		//'url' => 'javascript:;',
    'url' => '/admin/dashboard',
		//'caret' => true		
	],[
		'icon' => 'fas fa-users',
		'title' => 'Users',
		//'url' => 'javascript:;',
    'url' => '/admin/users ',
		// 'badge' => '10'	
	],]
	];
