<?php

namespace Plethora\Form;

use Plethora\Form;

/**
 * One of the main classes from Form API for Singletons.
 *
 * @package        Plethora/Form
 * @subpackage     FormField
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FieldSingleton {
    /**
     * @static
     * @access  public
     * @var     Form
     * @since   1.0.0-alpha
     */
    private static $singleTonForm = NULL;

    /**
     * @access   public
     * @return   Form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getForm() {
        if(static::$singleTonForm === NULL) {
            static::$singleTonForm = Form::factory('singletons');
        }

        return static::$singleTonForm;
    }
}