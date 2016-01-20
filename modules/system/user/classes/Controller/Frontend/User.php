<?php

namespace Controller\Frontend;

use Model;
use Controller\Frontend;
use Plethora\Core;
use Plethora\DB;
use Plethora\Form;
use Plethora\ModelCore\ModelFormConfig;
use Plethora\Route;
use Plethora\Router;
use Plethora\Session;
use Plethora\View;
use Plethora\Helper;
use Plethora\Exception;

/**
 * @author   Krzysztof Trzos
 * @package  Controller\Frontend
 * @since    0.0.1
 * @version  2.1.0-dev
 */
class User extends Frontend
{
    /**
     * ACTION - User logout.
     *
     * @access   public
     * @return   View
     * @since    1.0.2, 2013-12-07
     * @version  1.0.3, 2013-12-23
     */
    public function actionLogout()
    {
        $this->setTitle(__('Logout'));

        if(Session::get('uid') === NULL) {
            Route::factory('home')->redirectTo();
        }

        Session::destroy();

        return View::factory('user/frontend/logout');
    }

    /**
     * ACTION - User login.
     *
     * @access   public
     * @return   View
     * @since    1.0.2, 2013-12-07
     * @version  1.0.7-dev, 2015-05-04
     */
    public function actionLogin()
    {
        $this->setTitle(Core::getAppName().' - '.__('Login form'));
        $this->addBreadCrumb(__('Login form'));

        $oLoggedUser = Model\User::getLoggedUser();

        if($oLoggedUser instanceof Model\User) {
            Route::factory('user_profile')->redirectTo(['id' => $oLoggedUser->getId()]);
        }

        $failedLogins = \User\LoginFail::getCachedData();

        if($failedLogins > 4) {
            return View::factory('base/alert')
                ->set('sType', 'danger')
                ->set('sMsg', __('to.many.incorrect.logins'));
        }

        $oLoginForm = Form::factory('login');
        $oLoginForm->addField(Form\Field\Text::factory('login', $oLoginForm));
        $oLoginForm->addField(Form\Field\Password::factory('password', $oLoginForm));

        if($oLoginForm->isSubmittedAndValid()) {
            $sUsername = $oLoginForm->get('login');
            $sPassword = $oLoginForm->get('password');

            $sEncryptedPassword = Helper\Encrypter::factory()->encrypt($sUsername, $sPassword);

            $oUser = DB::query("SELECT u FROM \Model\User u WHERE u.login = :login AND u.password = :pass")
                ->param('login', $sUsername)
                ->param('pass', $sEncryptedPassword)
                ->single();

            if($oUser instanceof Model\User) {
                Session::set('username', $sUsername);
                Session::set('uid', (int)$oUser->getId());

                $oUser->setLoginDateNOW();
                DB::flush();

                # Get role permissions for particular user and set them in session
                \UserPermissions::reset();

                Route::factory(Router::getCurrentRouteName())->redirectTo();
            } else {
                $currentUrl = Router::currentUrl();
                $alert      = __('You have entered wrong username or password. Try again.');

                \User\LoginFail::addLoginFail();

                Session::flash($currentUrl, $alert, 'danger');
            }
        }

        $oLoginForm->addToSuffix(View::factory('user/frontend/login_links')->render());

        return View::factory('base/form')
            ->bind('oForm', $oLoginForm);
    }

    /**
     * ACTION - Users profile on frontend.
     *
     * @access   public
     * @return   View
     * @throws   Exception\Code404
     * @throws   Exception\Fatal
     * @since    1.1.0, 2014-11-22
     * @version  1.3.4, 2015-02-20
     */
    public function actionProfile()
    {
        $iUserID = (int)Router::getParam('id');
        $oUser   = Model\User::getUser($iUserID);

        if(empty($oUser)) {
            throw new Exception\Code404(__('This page does not exists.'));
        }

        return View::factory('user/frontend/profile')
            ->bind('oUser', $oUser);
    }

    /**
     * ACTION - Users profile modyfication form.
     *
     * @access   public
     * @return   View
     * @since    1.1.0, 2014-11-22
     * @version  1.3.4, 2015-02-20
     */
    public function actionEditProfile()
    {
        if(!Model\User::isLogged()) {
            Router::relocateToRoute('home');
        }

        // create Model form configuration
        $oFormConfig = ModelFormConfig::factory()
            ->setFieldsToRemove(['password'])
            ->setMessage(__('Your profile has been modified successfully.'));

        // get user
        $oUser      = Model\User::getLoggedUser();
        $oModelForm = $oUser->form('user_profile', $oFormConfig);
        $oForm      = $oModelForm->generate();

        // add local actions
        Router\LocalActions::addLocalAction(__('View profile'), 'user_profile_edit', 'user_profile')
            ->setParameters([
                'id' => $oUser->getId(),
            ]);
        Router\LocalActions::addLocalAction(__('Change password'), 'user_profile_edit', 'user_password_change');

        // return profile modification form
        return View::factory('base/form')
            ->bind('oForm', $oForm);
    }

    /**
     * ACTION - Change user password.
     *
     * @access   public
     * @return   View
     * @since    1.3.0, 2015-01-27
     * @version  1.0.2-dev, 2015-03-02
     */
    public function actionChangePassword()
    {
        if(!Model\User::isLogged()) {
            Router::relocateToRoute('home');
        }

        // get user
        $oUser = Model\User::getLoggedUser();

        /* create form instance */
        $oModelFormConfig = ModelFormConfig::factory()
            ->setFieldsRestriction(['password'])
            ->setMessage(__('Password changed successfully.'));

        $oModelForm = $oUser->form('user_profile', $oModelFormConfig);
        $oForm      = $oModelForm->generate();

        // add local actions
        Router\LocalActions::addLocalAction(__('View profile'), 'user_password_change', 'user_profile')
            ->setParameters(
                [
                    'id' => $oUser->getId(),
                ]
            );
        Router\LocalActions::addLocalAction(__('Edit profile'), 'user_password_change', 'user_profile_edit');

        // return profile modification form
        return View::factory('base/form')
            ->bind('oForm', $oForm);
    }

}
