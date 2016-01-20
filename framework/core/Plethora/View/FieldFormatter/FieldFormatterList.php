<?php

namespace Plethora\View\FieldFormatter;

use Plethora\View;

/**
 * @package        Plethora
 * @subpackage     View\FieldFormatter
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FieldFormatterList extends View\FieldFormatter implements View\ViewFieldFormatterInterface
{
    /**
     * Factory config.
     *
     * @access   public
     * @return   FieldFormatterList
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        return new FieldFormatterList();
    }

    /**
     * Format value.
     *
     * @access   public
     * @param    array $value
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function format($value)
    {
        parent::format($value);
    }

    /**
     * Make formatting on an array value.
     *
     * @access   public
     * @param    array $values
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function formatArray(array $values)
    {
        $oView = View::factory('base/view/field_formatter/list')
            ->bind('aList', $values);

        return $this->sPrefix.$oView->render().$this->sSuffix;
    }
}
