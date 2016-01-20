<?php

namespace Plethora\Form\Field;

use Plethora\Form;

/**
 * Form field using textarea with TinyMCE library.
 *
 * @package        Plethora\Form\Field
 * @subpackage     Editor
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Tinymce extends Textarea {
    /**
     * Path to the field main view.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/tinymce';

    /**
     * Constructor
     *
     * @access   public
     * @param    string $name field name
     * @param    Form   $form form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, Form &$form) {
        parent::__construct($name, $form);

        $attributes = $this->getAttributes();
        $attributes->addToAttribute('class', 'tinymce_editor');
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access   public
     * @param    string $name
     * @return   Tinymce
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($name) {
        return static::singletonByType($name, 'Tinymce');
    }
}