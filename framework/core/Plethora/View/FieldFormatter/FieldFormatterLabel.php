<?php

namespace Plethora\View\FieldFormatter;

use Plethora\View;
use Plethora\Exception;

/**
 * @package        Plethora
 * @subpackage     View\FieldFormatter
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FieldFormatterLabel extends View\FieldFormatter implements View\ViewFieldFormatterInterface
{
    /**
     * This variable specifies which heading tag will be used, if label is set
     * to be created as heading.
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $labelAsHeading = 0;

    /**
     * Factory config.
     *
     * @access   public
     * @return   FieldFormatterLabel
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        $oFormatter = new FieldFormatterLabel();
        $oFormatter->useOnlyOnSingletons();

        return $oFormatter;
    }

    /**
     * Create label as heading tag.
     *
     * @access   public
     * @param    integer $headingType
     * @return   $this
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setLabelAsHeading($headingType = 2)
    {
        if(!is_int($headingType) || $headingType < 0 || $headingType > 6) {
            throw new Exception\Fatal('Inappropriate value. Must be integer with values from 1 to 6.');
        }

        $this->labelAsHeading = $headingType;

        return $this;
    }

    /**
     * Format value.
     *
     * @access   public
     * @param    string $value
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function format($value)
    {
        $this->getField()->showLabel();

        if($this->labelAsHeading !== 0) {
            $this->getField()->setLabelAsHeading($this->labelAsHeading);
        }

        return $this->sPrefix.$value.$this->sSuffix;
    }
}