<?php

namespace CronJobs;

use Plethora\Mailer;

/**
 * Class Base
 *
 * @author     Krzysztof Trzos <k.trzos@jazi.pl>
 * @package    base
 * @subpackage CronJobs
 * @since      1.0.0-alpha
 * @version    1.0.0-alpha
 */
class Base
{
    /**
     * Send all queued e-mail by this method.
     *
     * @static
     * @access  public
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public static function sendQueuedEmails()
    {
        Mailer::factory()->sendQueue();
    }
}