<?php

namespace Controller\Frontend;

use Plethora\View;
use Plethora\Route;

/**
 * Main class for frontend side of the "sitemap" module.
 * 
 * @author		Krzysztof Trzos
 * @package		sitemap
 * @subpackage	classes
 * @since		1.0.0-dev, 2015-04-19
 * @version		1.0.0-dev, 2015-04-19
 */
class Sitemap extends \Controller\Frontend {

	/**
	 * @access	public
	 * @since	1.0.0-dev, 2015-04-19
	 * @version	1.0.0-dev, 2015-04-19
	 */
	public function actionDefault() {
		$this->setTitle(__('Sitemap'));
		$this->addBreadCrumb(__('Sitemap'));

		$aItems		 = [];
		$aItems[]	 = ['/', __('Front page')];

		$aPages = \Plethora\DB::query("SELECT p FROM \Model\Page p WHERE p.published = 1")
			->execute();

		foreach($aPages as $oPage) { /* @var $oPage \Model\Page */
			$aItems[] = [Route::factory('page')->path(['rewrite' => $oPage->getRewrite()]), $oPage->getTitle()];
		}
		
		\Sitemap\SitemapGenerator::generate($aItems);

		return View::factory('sitemap/frontend/sitemap')
				->bind('aItems', $aItems);
	}

}
