<?php

namespace Plethora\Form\Field;

use Plethora\Form;

/**
 * Password input form field
 *
 * @package        Plethora
 * @subpackage     Form\Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class PasswordConfirm extends Password {

    /**
     * Create new instance of PasswordConfirm class.
     *
     * @static
     * @access   public
     * @param    string $name
     * @param    Form   $form
     * @return   PasswordConfirm
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory($name, Form $form) {
        return new PasswordConfirm($name, $form);
    }

    /**
     * Class constructor.
     *
     * @access   public
     * @param    string $name
     * @param    Form   $form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, Form $form) {
        parent::__construct($name, $form);

        $this->setLabel(__('Password confirmation'));
        $this->setRequired();

        if(class_exists('\Model\User')) {
            $this->validator(
                [
                    ['\Validator\Rules\User::passConfirm', [':value']],
                ]
            );
        }
    }

}