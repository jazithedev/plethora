<?php

namespace Plethora\View;

/**
 * @package        Plethora
 * @subpackage     View
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
interface ViewFieldFormatterInterface {
    /**
     * Factory class.
     *
     * @static
     * @author     Krzysztof Trzos
     * @access     public
     * @return    \Plethora\View\FieldFormatter
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory();

    /**
     * Format value to view.
     *
     * @static
     * @author     Krzysztof Trzos
     * @access     public
     * @param    mixed $value
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function format($value);

    /**
     * Format value to view.
     *
     * @static
     * @author     Krzysztof Trzos
     * @access     public
     * @param    array $values
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function formatArray(array $values);
}