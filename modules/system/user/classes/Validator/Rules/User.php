<?php

namespace Validator\Rules;

/**
 * Validator for an user data.
 * 
 * @package		user
 * @subpackage	Validator
 * @author		Krzysztof Trzos
 * @since		2.20.6, 2015-01-26
 * @version		2.1.0-dev
 */
class User {

	/**
	 * Check if the given password is correct (is the logged user password).
	 * 
	 * @static
	 * @author	Krzysztof Trzos
	 * @access	public
	 * @param	string $sPassword
	 * @return	boolean
	 * @since	1.0.0, 2015-01-26
	 * @version	1.0.0, 2015-01-26
	 */
	public static function passConfirm($sPassword) {
		$oLoggedUser		 = \Model\User::getLoggedUser();
		$sPasswordToCompare	 = $oLoggedUser->getPassword();
		$sEncrypted			 = \Model\User::encryptPassword($oLoggedUser->getLogin(), $sPassword);
		
		if($sEncrypted === $sPasswordToCompare) {
			return TRUE;
		} else {
			return __('Wrong password passed. Try again.');
		}
	}

}