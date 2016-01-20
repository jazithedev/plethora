<?php

namespace Plethora\Helper;

use Plethora\Helper;
use Plethora\Exception;

/**
 * Helper used to generate HTML anchors.
 *
 * @package        Plethora
 * @subpackage     Helper
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Link extends Helper
{

    /**
     * Attributes list.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $attributes = NULL;

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @return   Link
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        return new Link;
    }

    /**
     * Constructor.
     *
     * @param    [ mixed $args [, $... ]]
     * @link     http://php.net/manual/en/language.oop5.decon.php
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct() {
        $this->attributes = new Attributes();
    }

    /**
     * Get attributes of this object.
     *
     * @access   public
     * @return   Attributes
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get link title text.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getTitle()
    {
        return $this->getAttributes()->getAttribute('title');
    }

    /**
     * Set link title text.
     *
     * @access   public
     * @param    string $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setTitle($value)
    {
        $this->getAttributes()->setAttribute('title', $value);

        return $this;
    }

    /**
     * Generate HTML anchor.
     *
     * @access   public
     * @param    string $text
     * @param    string $url
     * @return   string
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function code($text, $url)
    {
        if($this->getAttributes()->getAttribute('title') === NULL) {
            $this->getAttributes()->setAttribute('title', $text);
        }

        $attrs = $this->getAttributes()->renderAttributes();

        return '<a href="'.$url.'" '.$attrs.'>'.$text.'</a>';
    }
}