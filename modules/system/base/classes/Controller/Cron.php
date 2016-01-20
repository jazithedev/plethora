<?php

namespace Controller;

use Cron\CronExpression;
use Plethora\Cache;
use Plethora\Config;
use Plethora\Controller;
use Plethora\Helper\CronJobsHelper;
use Plethora\Log;
use Plethora\Route;
use Plethora\Router;
use Plethora\View;
use Plethora\Exception;

/**
 * Main controller for Cron jobs.
 *
 * @package          base
 * @subpackage       Controller
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class Cron extends Controller
{
    /**
     * Main action to run cron jobs.
     *
     * @access   public
     * @since    2.33.0-dev, 2015-06-07
     * @version  2.39.0-dev
     */
    public function actionDefault()
    {
        if(!class_exists('\Cron\CronExpression')) {
            throw new Exception\Fatal('Cannot run Cron jobs without proper Cron library.');
        }

        $sTokenFromURL      = Router::getParam('token');
        $sTokenFromConfig   = Config::get('base.cron_token');
        $iCronJobsCompleted = 0;

        // check Cron token
        if($sTokenFromURL !== $sTokenFromConfig) {
            throw new Exception\Code404();
        }

        // get all Cron jobs
        $aAllJobs = CronJobsHelper::getCronJobs();

        // count amount of all CRON jobs
        $iCronJobs = count($aAllJobs);

        // run a single CRON job
        foreach($aAllJobs as $aJobData) {
            $sModule  = $aJobData['module'];
            $sJobKey  = base64_encode($aJobData[1].'.'.$aJobData[2]);
            $aCache   = Cache::get($sModule.'.'.$sJobKey, 'cron');
            $iRunDate = isset($aCache['time']) ? $aCache['time'] : NULL;
            $oCron    = CronExpression::factory($aJobData[0]);

            if($iRunDate === NULL || $iRunDate < time()) {
                switch($aJobData[1]) {
                    case 'route':
                        $sURL = Route::factory($aJobData[2])
                            ->url();
                        file_get_contents($sURL);
                        break;
                    case 'file':
                    case 'url':
                        file_get_contents($aJobData[2]);
                        break;
                    case 'function':
                        call_user_func($aJobData[2]);
                        break;
                }

                $iNextRun = $oCron->getNextRunDate()
                    ->format('U');

                $aCacheToSave = [
                    'time'                => $iNextRun,
                    'last_execution_time' => time(),
                    'type'                => $aJobData[1],
                    'param'               => $aJobData[2],
                ];

                Cache::set($aCacheToSave, $sModule.'.'.$sJobKey, 'cron', 0);
            }
        }

        // log that cron jobs turning on action was completed
        Log::insert('Cron jobs (in amount of '.$iCronJobs.') were checked and '.$iCronJobsCompleted.' of them were turned on.');

        // end of functionality
        exit;
    }

    /**
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionRunCron()
    {
        return View::factory('base/run_cron');
    }

    /**
     * Cron job - clear all not needed files rom TEMP directory.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionClearTemp()
    {
        \FileManager::clearTemp();
        exit;
    }

}
