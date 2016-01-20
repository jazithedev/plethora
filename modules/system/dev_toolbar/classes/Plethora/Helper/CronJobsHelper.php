<?php
/**
 * Created by PhpStorm.
 * User: KrzysztofTrzos
 * Date: 2015-09-22
 * Time: 22:43
 */

namespace Plethora\Helper;

use Plethora\Cache;
use Plethora\Config;

/**
 * Class CronJobs
 *
 * @package    base
 * @subpackage Plethora\Helper
 * @author     Krzysztof Trzos
 * @since      1.1.0-dev
 * @version    1.1.0-dev
 */
class CronJobsHelper {
	/**
	 * Get Cron jobs from all modules of particular web application.
	 *
	 * @static
	 * @access  public
	 * @return  array
	 * @since   1.1.0-dev
	 * @version 1.1.0-dev
	 */
	public static function getCronJobs() {
		$aAllJobs = [];

		foreach(Config::get('modules') as $sGroupName => $aGroup) {
			foreach(array_keys($aGroup) as $module) {
				$sDir      = PATH_MODULES.$sGroupName.DS.$module.DS.'config';
				$sFilePath = $sDir.DS.'cron.php';

				if(file_exists($sDir) && file_exists($sFilePath)) {
					$aCronData = Config::get($module.'.cron', NULL);

					foreach($aCronData as &$data) {
						$jobKey = base64_encode($data[1].'.'.$data[2]);
						$aCache = Cache::get($module.'.'.$jobKey, 'cron');

						$data['job_key']  = $jobKey;
						$data['module']   = $module;
						$data['time']     = isset($aCache['time']) ? $aCache['time'] : NULL;
						$data['last_exe'] = isset($aCache['last_execution_time']) ? $aCache['last_execution_time'] : NULL;
					}

					$aAllJobs = array_merge($aAllJobs, $aCronData);
				}
			}
		}

		return $aAllJobs;
	}
}