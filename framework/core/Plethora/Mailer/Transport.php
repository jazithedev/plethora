<?php

namespace Plethora\Mailer;

use Plethora\Mail;

/**
 * Parent of mailer transport
 *
 * @abstract
 * @package        Plethora
 * @subpackage     Mailer
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
abstract class Transport {
    /**
     * @abstract
     * @param    Mail $oMessage
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    abstract function send(Mail $oMessage);
}