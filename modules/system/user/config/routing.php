<?php

defined('PATH_ROOT') OR die('No direct script access.');

# login_check
\Plethora\Router::addRoute('login', '/login')
	->setController('Frontend\User')
	->setAction('login');

# logout
\Plethora\Router::addRoute('logout', '/logout')
	->setController('Frontend\User')
	->setAction('logout');

# user_profile
\Plethora\Router::addRoute('user_profile', '/user/{id}')
	->setAction('Profile')
	->setController('Frontend\User')
	->addParameterType('id', 'number')
	->addDefault('id', NULL);

# user_profile_edit
\Plethora\Router::addRoute('user_profile_edit', '/edit_profile')
	->setController('Frontend\User')
	->setAction('EditProfile');

# user_password_change
\Plethora\Router::addRoute('user_password_change', '/edit_profile/password')
	->setController('Frontend\User')
	->setAction('ChangePassword');

# registration
\Plethora\Router::addRoute('register', '/register')
	->setController('Frontend\User\Registration');

# account_activation
\Plethora\Router::addRoute('account_activation', '/account_activation/{code}')
	->setController('Frontend\User\Registration')
	->setAction('activation')
	->addParameterType('code', 'string');

# password recovery
\Plethora\Router::addRoute('password_recovery', '/password_recovery')
	->setController('Frontend\User\PasswordRecovery');

# account_activation
\Plethora\Router::addRoute('password_recovery_code', '/password_recovery/{code}')
	->setController('Frontend\User\PasswordRecovery')
	->setAction('NewPassword')
	->addParameterType('code', 'string');
