<?php

namespace Controller\Frontend\User;

use Controller;
use Plethora\Core;
use Plethora\DB;
use Plethora\Form;
use Plethora\ModelCore;
use Plethora\Route;
use Plethora\Router;
use Plethora\Session;
use Plethora\View;
use Model\User;

/**
 * Controller used to contain actions concerning user password recovery.
 *
 * @author          Krzysztof Trzos
 * @copyright   (c) 2015, Krzysztof Trzos
 * @package         user
 * @subpackage      controller/frontend
 * @since           2.23.0, 2015-02-17
 * @version         2.1.0-dev
 */
class PasswordRecovery extends Controller\Frontend
{

    /**
     * Controller constructor.
     *
     * @access   public
     * @since    1.0.0, 2015-02-17
     * @version  2.1.0-dev
     */
    public function __construct()
    {
        parent::__construct();

        // fill up breadcrumbs title and other
        $this->addBreadCrumb(__('Password recovery'), Route::factory('password_recovery')->url());
        $this->addToTitle(' - '.__('Password recovery'));

        // check if particular user is logged
        if(User::isLogged()) {
            $sRedirectURL = Route::factory('home')->url();
            $sRedirectMsg = __('Cannot use password recovery system while being logged in!');

            Session::flash($sRedirectURL, $sRedirectMsg, 'warning');
        }
    }

    /**
     * Default action to password recovery.
     *
     * @access   public
     * @return   View
     * @since    1.0.0, 2015-02-17
     * @version  2.1.0-dev
     */
    /** @noinspection PhpMissingParentCallCommonInspection */
    public function actionDefault()
    {
        // create pass recovery form
        $oForm = Form::factory('password_recovery');

        $emailField = Form\Field\Text::factory('email', $oForm)
            ->setRequired()
            ->setLabel('E-mail');

        $oForm->addField($emailField);
        $oForm->addField(Form\Field\Captcha::factory('captcha', $oForm));

        // if form is valid
        if($oForm->isSubmittedAndValid()) {
            $sEmail = $oForm->getField('email')->getValueFirst();

            // find user with particular e-mail
            $query = DB::query('SELECT u FROM \Model\User u WHERE u.email = :email');
            $query->param('email', $sEmail);

            $oResult = $query->single();
            /* @var $oResult User */

            if($oResult instanceof User) {
                $this->sendRecoveryCode($oResult);

                $sMessage = __("Message has been sent on your e-mail address. Click on the link from it's content to recover password to your account.");
                Session::flash(Router::getCurrentUrl(), $sMessage);
            }
        }

        // return view
        return View::factory('user/frontend/recovery/default')
            ->bind('oForm', $oForm);
    }

    /**
     * Action to set new password after e-mail validation.
     *
     * @access   public
     * @return   View
     * @since    1.0.0, 2015-02-17
     * @version  2.1.0-dev
     */
    public function actionNewPassword()
    {
        // fill up breadcrumbs title and other
        $this->addBreadCrumb(__('New password'));

        // get code from $_GET
        $sCode = Router::getParam('code');

        // get recovery code from DB
        $oRecoveryCode = DB::query("SELECT c FROM \Model\User\RecoveryCode c WHERE c.code = :code")->param('code', $sCode)->single();
        /* @var $oResult User\RecoveryCode */

        // check if code exists
        if($oRecoveryCode instanceof User\RecoveryCode) {
            $this->addToTitle(' - '.__('New password'));

            // get user
            $oUser = $oRecoveryCode->getUser();

            // generate form for account access recovery
            $oConfig = ModelCore\ModelFormConfig::factory()
                ->noReload()
                ->setFieldsRestriction(['password'])
                ->setMessage(__('Your password has been successfully changed to the new one.'))
                ->setAction(Route::factory('password_recovery')->url());

            // get form
            $oModelForm = $oUser->form('new_password', $oConfig);
            $oForm      = $oModelForm->generate();

            // check if form is valid
            if($oForm->isSubmittedAndValid()) {
                $oRecoveryCode->remove();

                Session::flash(Route::factory('password_recovery')->url(), __('Password has been changed successfully.'));
            }

            $oForm->addToPrefix(View::factory('user/frontend/recovery/new_pass_prefix')->render());

            // return view
            return View::factory('base/form')
                ->bind('oForm', $oForm);
        } // if code do not exist
        else {
            $this->addToTitle(' - '.__('Error occured'));

            return View::factory('user/frontend/recovery/wrong_code');
        }
    }

    /**
     * Send user account recovery code.
     *
     * @access   public
     * @param    User $oUser
     * @since    1.0.0, 2015-02-17
     * @version  2.1.0-dev
     * @return   bool
     */
    private function sendRecoveryCode(User $oUser)
    {
        $sUserAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');

        $sCodeToEncode = (mb_strlen(uniqid()) * time()).$sUserAgent.$oUser->getLogin();
        $sCode2        = sha1($sCodeToEncode);
        $sRecoveryCode = base64_encode($sCode2);

        DB::query('DELETE FROM \Model\User\RecoveryCode r WHERE r.user = :user')
            ->param('user', $oUser->getId())
            ->execute(TRUE);

        $oRecoveryCode = new User\RecoveryCode();
        $oRecoveryCode->setUser($oUser);
        $oRecoveryCode->setCode($sRecoveryCode);

        DB::persist($oRecoveryCode);
        DB::flush();

        $sSubject    = __('Account activation on :app', ['app' => Core::getAppName()]);
        $mailContent = View::factory("user/frontend/recovery/message")->render(
            [
                'sLogin'        => $oUser->getLogin(),
                'sRecoveryCode' => $sRecoveryCode,
            ]
        );

        $mailView = View::factory('base/email');
        $mailView->bind('sContent', $mailContent);
        $mailView->set('sTitle', $sSubject);

        return $oUser->sendEmail($sSubject, $mailView->render());
    }

}