<?php

namespace Plethora\View\FieldFormatter;

use Plethora\Exception;
use Plethora\View;

/**
 * @package        Plethora
 * @subpackage     View\FieldFormatter
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FieldFormatterDate extends View\FieldFormatter implements View\ViewFieldFormatterInterface {
    /**
     * Date format.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sFormat = 'Y-m-d H:i';

    /**
     * Factory config.
     *
     * @access     public
     * @return    FieldFormatterDate
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory() {
        return new FieldFormatterDate();
    }

    /**
     * Set new value for format.
     *
     * @access     public
     * @param    string $sValue
     * @return    FieldFormatterDate
     * @throws    Exception\Fatal
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setFormat($sValue) {
        if(!is_string($sValue)) {
            throw new Exception\Fatal('Wrong argument type.');
        } else {
            $this->sFormat = $sValue;
        }

        return $this;
    }

    /**
     * Get format for date in \DateTime object.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFormat() {
        return $this->sFormat;
    }

    /**
     * Format value.
     *
     * @access     public
     * @param    \DateTime $value
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function format($value) {
        return $this->sPrefix.$value->format($this->getFormat()).$this->sSuffix;
    }
}