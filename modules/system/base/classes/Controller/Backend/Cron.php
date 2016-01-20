<?php

namespace Controller\Backend;

use Controller\Backend;
use Plethora\View;

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
class Cron extends Backend
{
    /**
     * Main action to run cron jobs manually.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function actionRunCron()
    {
        $this->addToTitle(__('Run Cron jobs manually'));

        return View::factory('base/backend/run_cron');
    }
}
