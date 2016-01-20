<?php

namespace Plethora\Form\Separator;

use Plethora\Form\Separator;

/**
 * Image separator for form fields
 *
 * @package        form
 * @subpackage     field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ImageFormSeparator extends Separator {
    /**
     * Separator class
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $class = "image_separator";

    /**
     * Separator's image alt
     *
     * @access    private
     * @var        string
     * @since   1.0.0-alpha
     */
    private $alt = "";

    /**
     * Separator's image label
     *
     * @access    private
     * @var        string
     * @since   1.0.0-alpha
     */
    private $label = "Image label";

    /**
     * Make two columns in this separator (one with label, second with image)
     *
     * @access    private
     * @var        boolean
     * @since   1.0.0-alpha
     */
    private $twoColumns = FALSE;

    /**
     * Set image alt
     *
     * @access   public
     * @param    string $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setAlt($value) {
        $this->alt = $value;

        return $this;
    }

    /**
     * Set label
     *
     * @access   public
     * @param    string $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function label($value) {
        $this->label = $value;

        return $this;
    }

    /**
     * Forces to use two columns way
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setTwoColumns() {
        $this->twoColumns = TRUE;

        return $this;
    }

    /**
     * Returns rendered separator
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function render() {
        if($this->twoColumns) {
            $return = '<th>'.$this->label.':</th><td>';
        } else {
            $return = '<td colspan="2">';
        }

        $return .= '<p class="'.$this->class.'">';
        if(strpos('x'.$this->getValue(), 'http://') > 0) {
            $aHeaderResponse = get_headers($this->getValue(), 1);
            if(strpos($aHeaderResponse[0], "404") !== FALSE) {
                $return .= 'no image';
            } else {
                $return .= '<img src="'.$this->getValue().'?'.time().'" alt="'.$this->alt.'" />';
            }
        } else {
            if(file_exists($this->getValue())) {
                $return .= '<img src="'.$this->getValue().'?'.time().'" alt="'.$this->alt.'" />';
            } else {
                $return .= 'no image';
            }
        }

        $return .= '</p></td>';

        return $return;
    }
}