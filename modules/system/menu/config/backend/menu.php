<?php

return array(
	'menu_list' => array(
		'parent'	=> 'contents',
		'url'		=> \Plethora\Route::backendUrl('menu', 'list'),
		'priority'	=> 1
	),
	'menu_add' => array(
		'parent'	=> 'contents',
		'url'		=> \Plethora\Route::backendUrl('menu', 'add'),
		'priority'	=> 1
	),
//	'menu_item_add' => array(
//		'parent'	=> 'contents',
//		'url'		=> \Plethora\Route::backendUrl('menu\item', 'add'),
//		'priority'	=> 1
//	),
//	'menu_item_list' => array(
//		'parent'	=> 'contents',
//		'url'		=> \Plethora\Route::backendUrl('menu\item', 'list'),
//		'priority'	=> 1
//	),
);