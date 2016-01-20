<?php

namespace ModelForm;

use Plethora\Form;
use Plethora\Form\Field;
use Plethora\ModelCore;
use Plethora\ModelCore\ModelForm;
use Plethora\ModelCore\ModelFormConfig;
use Plethora\Router;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          user
 * @subpackage       classes\ModelForm
 * @since            1.0.1-dev, 2015-02-20
 * @version          2.1.1-dev
 */
class User extends ModelForm
{

    /**
     * Factory method.
     *
     * @access   public
     * @param    ModelCore       $model
     * @param    string          $formName
     * @param    ModelFormConfig $config
     * @return   User
     * @since    1.0.2-dev, 2015-03-02
     * @version  1.0.2-dev, 2015-03-02
     */
    public static function factory(ModelCore $model, $formName, ModelFormConfig $config = NULL)
    {
        return new User($model, $formName, $config);
    }

    /**
     * Method which changes form object before all operations (validation and saving).
     *
     * @access   protected
     * @param    Form $form
     * @since    1.0.1-dev, 2015-02-20
     * @version  2.1.1-dev
     */
    protected function alterForm(Form &$form)
    {
        parent::alterForm($form);

        $sCurrentRoute = Router::getCurrentRouteName();

        # other pages
        switch($sCurrentRoute) {
            // USER PASSWORD CHANGE
            case 'user_password_change':
            case 'password_recovery_code':
                $form->getField('password')
                    ->setLabel(__('New password'));

                $form->addField(
                    Form\Field\Password::factory('new_password_confirm', $form)
                        ->setRequired()
                        ->setLabel(__('Confirm new password'))
                        ->addRule(['\Plethora\Validator\Rules::sameAs', [':value', ':valuefrom:password']])
                );
                break;
            //  USER REGISTRATION
            case 'register':
                $loginAttributes = $form->getField('login')->getAttributes();
                $loginAttributes->removeAttribute('disabled');

                $emailAttrs = $form->getField('email')->getAttributes();
                $emailAttrs->removeAttribute('disabled');

                $form->addField(
                    Form\Field\Text::factory('email_confirm', $form)
                        ->setRequired()
                        ->setLabel(__('Confirm e-mail'))
                        ->setWeightToBeAfter('email')
                        ->addRule(['\Plethora\Validator\Rules::sameAs', [':value', ':valuefrom:email']])
                );

                $form->getField('password')
                    ->setWeightToBeAfter('email_confirm');

                $form->addField(
                    Form\Field\Password::factory('password_confirm', $form)
                        ->setRequired()
                        ->setLabel(__('Confirm password'))
                        ->addRule(['\Plethora\Validator\Rules::sameAs', [':value', ':valuefrom:password']])
                        ->setWeightToBeAfter('password')
                );
                break;
        }

        if(in_array($sCurrentRoute, ['backend', 'user_profile_edit', 'user_password_change'])) {
            $confirmField = Form\Field\PasswordConfirm::factory('password_check', $form);
            $confirmField->setLabel(__('Enter your password'));
            $confirmField->setTip(__('This field is used for a security purposes.'));
            $confirmField->setWeight(999);
            $confirmField->setPrefix('<div class="field_prefix" style="margin-top: 50px;"></div>');

            $form->addField($confirmField);
        }

        if(in_array($sCurrentRoute, ['user_profile_edit'])) {
            $form->removeField('roles');
        }

        if(in_array($sCurrentRoute, ['backend'])) {
            /* @var $password Field\Password */
            $password = $form->getField('password');
            $password->setRequiredNot();
        }

        # captcha on needed pages
        if(in_array($sCurrentRoute, ['register'])) {
            $captchaField = Form\Field\Captcha::singleton('captcha');
            $captchaField->setLabel(__('Security field'));
            $captchaField->setWeight(999999999);
            $captchaField->setFormIfSingleton($form);
        }
    }

    /**
     * Method in which can do some operations before saving to database.
     *
     * @overwritten
     * @author   Krzysztof Trzos
     * @access   protected
     * @param    Form $form
     * @since    1.0.1-dev, 2015-02-20
     * @version  2.1.0-dev
     */
    protected function beforeSave(Form &$form)
    {
        parent::beforeSave($form);

        if($form->hasField('password') && $form->get('password') !== '') {
            $oUser = $this->getModel();
            /* @var $oUser \Model\User */

            $oUser->setPassword($form->get('password'));
        }
    }

    /**
     * Set value for database field from form.
     *
     * @author   Krzysztof Trzos
     * @access   protected
     * @param    string     $sName
     * @param    mixed      $mValue
     * @param    Form\Field $oFormField
     * @return   boolean
     * @since    1.0.1-dev, 2015-02-20
     * @version  2.1.1-dev
     */
    protected function makeDataTransfer($sName, $mValue, Form\Field &$oFormField)
    {
        $sCurrentRoute       = Router::getCurrentRouteName();
        $aCurrentRouteParams = Router::getParams();

        switch($sName) {
            case 'password':
                switch($sCurrentRoute) {
                    case 'backend':
                        if($aCurrentRouteParams['controller'] === 'user' && $aCurrentRouteParams['action'] === 'edit') {
                            if(empty($mValue['und'][0])) {
                                return FALSE;
                            }
                        }
                        break;
                }
                break;
        }

        return parent::makeDataTransfer($sName, $mValue, $oFormField);
    }

}