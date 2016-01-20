<?php

namespace Plethora\Exception;

use Plethora\Exception;

/**
 * @package        Plethora
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Code403 extends Exception {

    /**
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sHeaderContent = '403 Forbidden';

    /**
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $iHttpCode = 403;

}
