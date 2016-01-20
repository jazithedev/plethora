<?php

namespace Controller\Frontend\User;

use Controller;
use Plethora;
use Plethora\Config;
use Plethora\DB;
use Plethora\Mail;
use Plethora\Mailer;
use Plethora\ModelCore\ModelFormConfig;
use Plethora\Route;
use Plethora\Router;
use Plethora\Session;
use Plethora\View;
use Model\User as UserModel;
use Model\User\ActivationCode as ActivationCodeModel;

/**
 * User registration controller.
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2013, Krzysztof Trzos
 * @package          Controller\Frontend
 * @since            1.0.0
 * @version          2.1.1-dev
 */
class Registration extends Controller\Frontend
{

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Default action with registration form.
     *
     * @access     public
     * @return     View
     * @since      1.0.0
     * @version    2.1.0-dev
     */
    public function actionDefault()
    {
        // if user is logged, redirect to main page
        if(UserModel::isLogged()) {
            Route::factory('home')->redirectTo();
        }

        $this->setTitle(__('Register Your account'));
        $this->setKeywords(__('register account,login,email,password'));

        /* @var $oConfig ModelFormConfig */
        $oConfig = ModelFormConfig::factory();
        $oConfig->noReload();
        $oConfig->setFieldsRestriction(['login', 'email', 'password']);

        $oUser      = new UserModel;
        $oModelForm = $oUser->form('register', $oConfig);

        /* @var $oModelForm \ModelForm\User */
        $oForm = $oModelForm->generate();

        // if form is submitted and is valid
        if($oForm->isSubmittedAndValid()) {
            $this->sendActivationCode($oForm->get('password_confirm'), $oUser);

            $sMessage = __('Your account has been registered successfully. Activation link has been sent to your mailbox. Click it to make the final activation.');
            Session::flash(Router::getCurrentUrl(), $sMessage);
        }

        // return registration View
        return View::factory('user/frontend/register')
            ->bind('oForm', $oForm);
    }

    /**
     * Activate user account.
     *
     * @access     public
     * @return     View
     * @since      1.0.0
     * @version    1.1.0, 2015-02-12
     */
    public function actionActivation()
    {
        // if user is logged, redirect to main page
        if(UserModel::isLogged()) {
            Route::factory('home')->redirectTo();
        }

        // set page title
        $this->setTitle(__('Activate your new account'));

        // get code from URL
        $sCode      = Router::getParam('code');
        $bActivated = FALSE;

        // get activation code from DB
        $oResult = DB::query("SELECT c FROM \Model\User\ActivationCode c WHERE c.code = :code")->param('code', $sCode)->single();
        /* @var $oResult ActivationCodeModel */

        // activate user
        if($oResult instanceof ActivationCodeModel) {
            // Set user as activated
            $bActivated = TRUE;

            $oUser = $oResult->getUser();
            $oUser->setActivation(TRUE);

            DB::flush();

            // Remove activation code from DB
            DB::remove($oResult);
            DB::flush();
        }

        // view
        return View::factory("user/frontend/register/activation")
            ->bind('bActivated', $bActivated);
    }

    /**
     * Send user account activation code.
     *
     * @access     public
     * @param      string    $sPassword
     * @param      UserModel $oUser
     * @return     bool
     * @throws     \Plethora\Exception
     * @throws     \Plethora\Exception\Fatal
     * @since      1.0.0
     * @version    2.1.0-dev
     */
    private function sendActivationCode($sPassword, UserModel $oUser)
    {
        $sUserAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');

        $sActivationCode1 = (mb_strlen($sPassword) * time()).$sUserAgent.$oUser->getLogin();
        $sActivationCode2 = sha1($sActivationCode1);
        $sActivationCode  = base64_encode($sActivationCode2);

        $oActivationCode = new ActivationCodeModel();
        $oActivationCode->setUser($oUser);
        $oActivationCode->setCode($sActivationCode);

        DB::persist($oActivationCode);
        DB::flush();

        $sSubject    = __(':appname - Activation link', ['appname' => Plethora\Core::getAppName()]);
        $mailContent = View::factory("user/frontend/register/message")->render(
            [
                'sLogin'          => $oUser->getLogin(),
                'sActivationCode' => $sActivationCode,
            ]
        );

        $mailView = View::factory('base/email');
        $mailView->bind('sContent', $mailContent);
        $mailView->set('sTitle', $sSubject);

        $mail = $mailView->render();

        $oMessage = new Mail;
        $oMessage->setSubject($sSubject);
        $oMessage->setFrom(Config::get('base.email'));
        $oMessage->setTo($oUser->getEmail());
        $oMessage->setBody($mail, 'text/html');

        return Mailer::factory()->send($oMessage);
    }

}