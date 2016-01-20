<?php

namespace Plethora\ModelCore\MConfig;

use Plethora\ModelCore;

/**
 * Main interface used for implementing classes which define entity field
 * formatters.
 *
 * @package        Plethora
 * @subpackage     ModelCore\MConfig
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
interface FieldFormattersInterface {
    /**
     * @static
     * @access   public
     * @param    ModelCore\MConfig $oConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function createFormatters(ModelCore\MConfig &$oConfig);
}