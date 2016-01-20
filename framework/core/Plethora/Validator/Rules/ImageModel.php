<?php

namespace Plethora\Validator\Rules;

/**
 * Strings validation methods
 *
 * @package        Plethora
 * @subpackage     Validator\Rules
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ImageModel extends FileModel {
    /**
     * Factory static method.
     *
     * @static
     * @access   public
     * @return   ImageModel
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new ImageModel();
    }
}