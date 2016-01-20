<?php

namespace Plethora\Form\Separator;

use Plethora\Form\Separator;

/**
 * Text separator for form fields
 *
 * @package        Plethora
 * @subpackage     Form\Separator
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class TextFormSeparator extends Separator {
    /**
     * Separator class
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $class = "text_separator";

    /**
     * Returns rendered separator
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function render() {
        return '<td colspan="2"><p class="'.$this->class.'">'.$this->getValue().'</p></td>';
    }
}