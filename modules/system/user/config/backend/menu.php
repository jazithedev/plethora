<?php

defined('PATH_ROOT') OR die('No direct script access.');

return array(
	'users'		 => array(
		'parent'	 => 'system',
		'url'		 => \Plethora\Route::backendUrl('user', 'list'),
		'priority'	 => 1,
	),
	'roles_list' => array(
		'parent'	 => 'system',
		'url'		 => \Plethora\Route::backendUrl('user\role', 'list'),
		'priority'	 => 2,
	),
	'permission_list' => array(
		'parent'	 => 'system',
		'url'		 => \Plethora\Route::backendUrl('user\permission', 'list'),
		'priority'	 => 2,
	),
);

/**
 * CHANGELOG:
 * 2015-01-10: Dodanie listy ról i uprawnień.
 */