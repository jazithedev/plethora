<?php

namespace ModuleMenu;
use Plethora\Helper\Arrays;
use Plethora\Route;
use Plethora\Router;
use Plethora\View;
use Model\Menu;

/**
 * Class which is used to generate menu HTML code.
 *
 * @author		Krzysztof Trzos
 * @package		menu
 * @subpackage	classes
 * @since		1.2.0-dev
 * @version		1.2.0-dev
 */
class MenuModel {

	/**
	 * Current menu level
	 *
	 * @access	private
	 * @var		integer
	 * @since	1.2.0-dev
	 */
	private $iLevel = 0;

	/**
	 * Generate menu as a View object.
	 *
	 * @static
	 * @access	public
	 * @param	Menu  $oMenu
	 * @return	View
	 * @since	1.2.0-dev
	 * @version	1.2.0-dev
	 */
	public static function generate(Menu $oMenu) {
		$oLocales	 = $oMenu->getLocales(); /* @var $oLocales \Model\Menu\Locales */
		$aItems		 = $oMenu->getItems();
		$aRoutes	 = $oMenu::tree($aItems->toArray());

		$oMenuTool = new MenuModel();
		$oMenuTool->findActiveRoute($aRoutes);

		$oMenuView = $oMenuTool->createNextLevel($aRoutes);

		return View::factory('menu/menu_container')
				->set('sMenuHeader', $oLocales->getTitle())
				->set('sMenuMachineName', $oMenu->getWorkingName())
				->bind('oContent', $oMenuView);
	}

	/**
	 * Create next level of menu.
	 *
	 * @access	private
	 * @param	array  $aRoutes
	 * @return	View
	 * @since	1.2.0-dev
	 * @version	1.2.0-dev
	 */
	private function createNextLevel(array $aRoutes) {
		$aEntries = [];
		$this->iLevel++;

		foreach($aRoutes as $aRoute) { /* @var $aRoute array */
			$oItem			 = $aRoute['object']; /* @var $oItem \Model\Menu\Item */
			$oItemLocales	 = $oItem->getLocales(); /* @var $oItemLocales \Model\Menu\Item\Locales */
			$sRouteTitle	 = $oItemLocales->getName(); /* @var $sRouteTitle string */
			$sRouteName		 = $oItem->getRoute(); /* @var $sRouteName string */
			$aRouteParams	 = $oItem->getRouteParams() !== NULL ? $oItem->getRouteParams() : []; /* @var $aRouteParams array */
			$aAttributes	 = Arrays::get($aRoute, 'parameters', []); /* @var $aSiblings array */
			$aSiblings		 = Arrays::get($aRoute, 'siblings', []); /* @var $aSiblings array */
			$sClasses		 = $oItem->getClasses(); /* @var $sClasses string */
			$oSiblings       = NULL;
			$oRoute          = Route::factory($sRouteName);
			$aParamsTypes    = array_keys($oRoute->getParameterTypes());
			$aParams         = array_combine($aParamsTypes, $aRouteParams);
			$sPath			 = $oRoute->path($aParams);

			if(!isset($aAttributes['class'])) {
				$aAttributes['class'] = '';
			}

			$aAttributes['class'] = trim($sClasses.' '.$aAttributes['class'].' '.Arrays::get($aRoute, 'classes', ''));

			$oSingleLevel = View::factory('menu/model/single_level')
				->set('sRouteTitle', $sRouteTitle)
				->set('sRouteName', $sRouteName)
				->set('sPath', $sPath)
				->set('aRouteParams', $aRouteParams)
				->set('aParameters', $aAttributes);

			if($aSiblings !== []) {
				$oSiblings = $this->createNextLevel($aSiblings); /* @var $oSiblings View */
			}

			$oSingleLevel->set('oSiblings', $oSiblings);

			$aEntries[] = $oSingleLevel;
		}

		return View::factory('menu/menu')
			->set('iLevel', $this->iLevel)
			->set('aEntries', $aEntries);
	}

	/**
	 * Find active route in particular menu.
	 *
	 * @access	private
	 * @param	array    $aRoutes
	 * @param	integer  $iParentKey
	 * @param	array    $aParent
	 * @since	1.2.0-dev
	 * @version	1.2.0-dev
	 */
	private function findActiveRoute(array &$aRoutes, $iParentKey = NULL, array $aParent = []) {
		foreach($aRoutes as $i => &$aRoute) { /* @var $aRoute array */
			$oItem			 = $aRoute['object']; /* @var $oItem \Model\Menu\Item */
			$sRouteName		 = $oItem->getRoute(); /* @var $sRouteName string */
			$aRouteParams	 = $oItem->getRouteParams() !== NULL ? $oItem->getRouteParams() : []; /* @var $aRouteParams array */
			$aActiveRoutes	 = $oItem->getActiveRoutes() !== NULL ? $oItem->getActiveRoutes() : []; /* @var $aRouteParams array */
			$sPath			 = Route::factory($sRouteName)->path($aRouteParams);
			$sCurrentPath	 = Router::getCurrentUrl();

			if(in_array(Router::getCurrentRouteName(), $aActiveRoutes)) {
				$aRoute['classes'] = 'current active_trail';
			} elseif(!isset($aRoute['classes'])) {
				$aRoute['classes'] = NULL;
			}

			if($iParentKey !== NULL) {
				$aRoute['parent_key']	 = $iParentKey;
				$aRoute['parent']		 = $aParent;
			}

			if($sPath === $sCurrentPath) {
				$this->goBackAndSetActive($aRoute);
			}

			if(isset($aRoute['siblings']) && !empty($aRoute['siblings'])) {
				$this->findActiveRoute($aRoute['siblings'], $i, $aRoutes); /* @var $oChildren View */
			}
		}
	}

	/**
	 * Go back to the first level, and set all items on the menu route as active.
	 *
	 * @access	private
	 * @param	array  $aRoute
	 * @since	1.2.0-dev
	 * @version	1.2.0-dev
	 */
	private function goBackAndSetActive(array &$aRoute) {
		$aRoute['classes'] = 'current active_trail';

		if(isset($aRoute['parent_key'])) {
			$iParentsKey	 = $aRoute['parent_key'];
			$aParentRoute	 = &$aRoute['parent'][$iParentsKey];

			$aParentRoute['classes'] = 'active_trail';

			if(isset($aParentRoute['parent'])) {
				$this->goBackAndSetActive($aParentRoute);
			}
		}
	}

}
