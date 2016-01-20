<?php defined('PATH_ROOT') OR die('No direct script access.');

return array(
	'pages_add' => array(
		'parent'	=> 'contents',
		'url'		=> \Plethora\Route::backendUrl('pages', 'add'),
		'priority'	=> 1
	),
	'pages_list' => array(
		'parent'	=> 'contents',
		'url'		=> \Plethora\Route::backendUrl('pages', 'list'),
		'priority'	=> 1
	),
);