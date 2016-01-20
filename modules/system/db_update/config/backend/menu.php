<?php defined('PATH_ROOT') OR die('No direct script access.');

return array(
	'dbupdate' => array(
		'parent'	=> 'system',
		'url'		=> \Plethora\Route::backendUrl('dbupdate'),
		'priority'	=> 2,
	),
);