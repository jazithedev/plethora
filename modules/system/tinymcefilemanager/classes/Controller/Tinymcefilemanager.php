<?php

namespace Controller;
use Plethora\Exception\Code401 as Code401Exception;
use Plethora\Exception\Code404 as Code404Exception;
use Plethora\Router;

/**
 * Main controller used to get access to TinyMCE filemanager.
 * 
 * @author		Krzysztof Trzos
 * @package		tinymcefilemanager
 * @subpackage	classes\controller
 * @since		1.0.0-dev
 * @version		1.0.1
 */
class Tinymcefilemanager extends \Plethora\Controller {
	/**
	 * Default action for TinyMCE Responsive File Manager. Config file available
	 * via <code>\ResponsiveFileManager::$aConfig</code> variable. For 
	 * non-commercial usage only.
	 * 
	 * @access	public
	 * @since	1.0.0-dev
	 * @version	1.0.1
	 */
	public function actionDefault() {
		$sFileManagerAction = Router::getParam('fmaction');

		if(!in_array($sFileManagerAction, ['dialog', 'ajax_calls', 'execute', 'force_download', 'upload'])) {
			throw new Code404Exception();
		}

		if(!\UserPermissions::hasPerm('wysiwyg_filemanager')) {
			throw new Code401Exception();
		}

		$sLang = Router::getLang();

		\ResponsiveFileManager::$aConfig['default_language'] = $sLang;
	}
}