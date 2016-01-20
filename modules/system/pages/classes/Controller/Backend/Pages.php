<?php

namespace Controller\Backend;

defined('PATH_ROOT') OR die('No direct script access.');

/**
 * @author	Krzysztof Trzos
 * @package	Controller
 * @version 1.0.4, 2013-12-27
 */
class Pages extends \Controller\Backend {
	/**
	 * List of permissions related to particular Actions
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $aPermissions = array(
		'list'		=> 'pages',
		'add'		=> 'pages',
		'edit'		=> 'pages',
		'delete'	=> 'pages_del',
	);
	
	/**
	 * @access	protected
	 * @var		array
	 */
	protected $listColumns = array('title');
	
	/**
	 * @access	protected
	 * @var		string
	 */
	protected $sModel = '\Model\Page';
}

/**
 * CHANGELOG:
 * 1.0.4, 2013-12-27: Usunięcie niepotrzebneg kodu.
 */