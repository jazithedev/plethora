<?php

namespace Plethora\Form\Field;

use Plethora\Config;
use Plethora\Form;
use Plethora\Form\Field as FormField;
use Plethora\Router;
use ReCaptcha\ReCaptcha;

/**
 * Captcha form field.
 *
 * @package        Plethora\Form
 * @subpackage     Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Captcha extends FormField {

    /**
     * Public key for reCAPTCHA
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $sPublickey;

    /**
     * Private key for reCAPTCHA
     *
     * @access    private
     * @var       string
     * @since   1.0.0-alpha
     */
    private $sPrivatekey;

    /**
     * The response from reCAPTCHA
     *
     * @access    private
     * @var       string
     * @since   1.0.0-alpha
     */
    private $oResponse = NULL;

    /**
     * The error code from reCAPTCHA, if any
     *
     * @access    private
     * @var       string
     * @since   1.0.0-alpha
     */
    private $aCaptchaError = NULL;

    /**
     * Path to field View.
     *
     * @access    protected
     * @var       string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/captcha';

    /**
     * This variable tells whether particular user is validated by captcha.
     *
     * @access    protected
     * @var       bool
     * @since   1.0.0-alpha
     */
    private $isValidated = FALSE;

    /**
     * Constructor.
     *
     * @access   public
     * @param    string $name
     * @param    Form   $form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, Form &$form) {
        // add reCaptcha JavaScript API
        Router::getInstance()
            ->getController()
            ->addJs('https://www.google.com/recaptcha/api.js?hl=pl');

        // get private and public keys
        $this->sPublickey  = Config::get('recaptcha.publickey');
        $this->sPrivatekey = Config::get('recaptcha.privatekey');

        // parent construct
        parent::__construct($name, $form);
    }

    /**
     * Make some actions / operations for particular field just before form validation.
     *
     * @override
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function beforeValidation() {
        if(Config::get('recaptcha.active') === FALSE) {
            return FALSE;
        }

        $sResponseField = filter_input(INPUT_POST, 'g-recaptcha-response');
        $sRemoteAddr    = filter_input(INPUT_SERVER, 'REMOTE_ADDR');

        if($sResponseField !== NULL && $this->isValidated === FALSE) {
            $oReCaptcha = new ReCaptcha($this->getPrivateKey());
            /* @var $oReCaptcha ReCaptcha */
            $this->oResponse = $oReCaptcha->verify($sResponseField, $sRemoteAddr);
            /* @var $oResponse \ReCaptcha\Response */

            if(!$this->oResponse->isSuccess()) {
                $this->aCaptchaError = $this->oResponse->getErrorCodes();
                $this->getFormObject()->getValidator()->addError($this->getName().'____und____0', __('In the purpose of security, you must prove that you are not a bot.'));
            } else {
                $this->isValidated = TRUE;
            }
        }

        return TRUE;
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access   public
     * @param    string $name
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($name) {
        return static::singletonByType($name, 'Captcha');
    }

    /**
     * Cechk whether user is validated by captcha mechanizm.
     *
     * @return   bool
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isValidated() {
        return $this->isValidated;
    }

    /**
     * Get public key of reCAPTCHA.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getPublicKey() {
        return $this->sPublickey;
    }

    /**
     * Get private key of reCAPTCHA.
     *
     * @access   private
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function getPrivateKey() {
        return $this->sPrivatekey;
    }

    /**
     * Get reCAPTCHA generated errors.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getCaptchaError() {
        return $this->aCaptchaError;
    }

    /**
     * Render field and return its rendered value.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function render() {
        return Config::get('recaptcha.active') === FALSE ? '' : parent::render();
    }

}
