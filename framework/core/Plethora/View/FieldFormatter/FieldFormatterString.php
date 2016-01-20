<?php

namespace Plethora\View\FieldFormatter;

use Plethora\View;
use Plethora\Helper;

/**
 * @package        Plethora
 * @subpackage     View\FieldFormatter
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FieldFormatterString extends View\FieldFormatter implements View\ViewFieldFormatterInterface
{
    /**
     * Integer which indicates how long particular string should be.
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $substringLength = 0;

    /**
     * Flag used to check whether string should be substringed with saving
     * words or not.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $bSaveWords = NULL;

    /**
     * How to end substr.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $sEndSubstrWith = NULL;

    /**
     * Flag which tells to do or not to do strip_tags() function on the value.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $bStripTags = FALSE;

    /**
     * Factory config.
     *
     * @access   public
     * @return   FieldFormatterString
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        return new FieldFormatterString();
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
        if($this->bStripTags) {
            $value = strip_tags($value);
        }

        if($this->substringLength > 0) {
            if($this->bSaveWords === FALSE) {
                $value = substr($value, 0, $this->substringLength);
            } else {
                $value = Helper\String::substrWords($value, $this->substringLength, $this->sEndSubstrWith);
            }
        }

        return $this->sPrefix.$value.$this->sSuffix;
    }

    /**
     * Set length of substr usage.
     *
     * @access   public
     * @param    integer $length
     * @param    boolean $saveWords
     * @param    string  $endWith
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setSubstrLength($length, $saveWords = TRUE, $endWith = '...')
    {
        $this->substringLength = (int)$length;
        $this->bSaveWords      = $saveWords;
        $this->sEndSubstrWith  = $endWith;

        return $this;
    }

    /**
     * Strip all tags from the value.
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function stripTags()
    {
        $this->bStripTags = TRUE;

        return $this;
    }

}
