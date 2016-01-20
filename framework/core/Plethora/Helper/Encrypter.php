<?php

namespace Plethora\Helper;

use Plethora\Helper;

/**
 * Encrypter helper for string encrypting
 *
 * @package        Plethora
 * @subpackage     Helper
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Encrypter extends Helper {
    /**
     * Factory method.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Encrypter();
    }

    /**
     * Encrypt some text.
     *
     * @access   public
     * @param    string $sKey
     * @param    string $sText
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function encrypt($sKey, $sText) {
        if(empty($sText)) {
            return NULL;
        }

        $sCipherId        = mcrypt_module_open('cast-256', '', 'ecb', '');
        $sCipherDirection = mcrypt_create_iv(mcrypt_enc_get_iv_size($sCipherId), MCRYPT_RAND);

        mcrypt_generic_init($sCipherId, "super".$sKey, $sCipherDirection);

        $sEncrypted = mcrypt_generic($sCipherId, $sText);

        mcrypt_generic_deinit($sCipherId);
        mcrypt_module_close($sCipherId);

        return base64_encode($sEncrypted);
    }
}