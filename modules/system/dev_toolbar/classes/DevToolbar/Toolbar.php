<?php

namespace DevToolbar;

use Plethora\Benchmark;
use Plethora\DB;
use Plethora\Helper\CronJobsHelper;
use Plethora\Router;
use Plethora\View;

/**
 * Main class used in developers toolbar.
 *
 * @author         Krzysztof Trzos
 * @package        dev_toolbar
 * @subpackage     classes
 * @since          1.0.0-dev, 2015-06-08
 * @version        1.1.0-dev
 */
class Toolbar {

	/**
	 * Array of customs.
	 *
	 * @static
	 * @access    private
	 * @var        array
	 * @since     1.0.3-dev, 2015-06-11
	 */
	private static $aCustoms = [];

	/**
	 * Array of PHP errors / notices.
	 *
	 * @access    private
	 * @var        array
	 * @since     1.0.4-dev, 2015-06-15
	 */
	private static $aErrors = [];

	/**
	 * Factory method.
	 *
	 * @static
	 * @access     public
	 * @return    Toolbar
	 * @since      1.0.0-dev, 2015-06-08
	 * @version    1.0.0-dev, 2015-06-08
	 */
	public static function factory() {
		return new Toolbar();
	}

	/**
	 * Add new custom to the list.
	 *
	 * @static
	 * @param    string $sName
	 * @param    mixed  $mValue
	 * @since      1.0.3-dev, 2015-06-11
	 * @version    1.0.4-dev, 2015-06-15
	 */
	public static function custom($sName, $mValue) {
		$aBacktrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

		static::$aCustoms[$sName] = [
			'data' => $mValue,
			'line' => $aBacktrace[0]['file'].' on line '.$aBacktrace[0]['line'],
		];
	}

	/**
	 * Get the whole list of customs.
	 *
	 * @access     protected
	 * @return    array
	 * @since      1.0.3-dev, 2015-06-11
	 * @version    1.0.3-dev, 2015-06-11
	 */
	protected static function getCustoms() {
		return static::$aCustoms;
	}

	/**
	 * Add new error to the list.
	 *
	 * @static
	 * @access     public
	 * @since      1.0.4-dev, 2015-06-15
	 * @version    1.0.4-dev, 2015-06-15
	 */
	public static function addError($aError) {
		// TODO
	}

	/**
	 * Render toolbar.
	 *
	 * @access     public
	 * @return    string
	 * @since      1.0.0-dev, 2015-06-08
	 * @version    1.1.0-dev
	 */
	public function render() {
		$oSqlLogger = DB::getEntityManager()
			->getConnection()
			->getConfiguration()
			->getSQLLogger(); //* @var $oSqlLogger \Doctrine\DBAL\Logging\DebugStack */

		$aRoutesList     = Router::getRoutes();
		$aModules        = Router::getModules();
		$aBenchmarkMarks = Benchmark::getAllMarks();
		$aCustoms        = static::getCustoms();
		$cronJobs        = CronJobsHelper::getCronJobs();

		return View::factory('dev_toolbar/toolbar')
			->bind('oSqlLogger', $oSqlLogger)
			->bind('aRoutesList', $aRoutesList)
			->bind('aModules', $aModules)
			->bind('aCustoms', $aCustoms)
			->bind('aBenchmarkMarks', $aBenchmarkMarks)
			->bind('cronJobs', $cronJobs)
			->render();
	}

}
