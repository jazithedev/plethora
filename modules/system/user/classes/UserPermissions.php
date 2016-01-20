<?php
use Plethora\Session;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          user
 * @since            2.20.1, 2015-01-10
 * @version          2.1.0-dev
 */
class UserPermissions
{

    /**
     * Loaded permissions list.
     *
     * @static
     * @access    private
     * @var        array
     * @since     1.0.0, 2015-01-10
     *
     */
    private static $aPermissions = NULL;

    /**
     * Set permissions to session.
     *
     * @static
     * @access     public
     * @param   array $aPermissions
     * @since      1.0.0, 2015-01-10
     * @version    1.0.0, 2015-01-10
     */
    public static function setPerms(array $aPermissions)
    {
        static::$aPermissions = $aPermissions;

        Session::set('permissions', array_values($aPermissions));
    }

    /**
     * Get logged user permissions.
     *
     * @static
     * @access     public
     * @return  array
     * @since      1.0.0, 2015-01-10
     * @version    1.0.0, 2015-01-10
     */
    public static function getPerms()
    {
        if(static::$aPermissions === NULL) {
            static::$aPermissions = Session::get('permissions');
        }

        return static::$aPermissions;
    }

    /**
     * Check if logged user has particular permission.
     *
     * @static
     * @param   string $sPermission
     * @return  boolean
     * @since      1.0.0, 2015-01-10
     * @version    1.0.1, 2015-01-10
     */
    public static function hasPerm($sPermission)
    {
        $oUser = \Model\User::getLoggedUser();

        if($oUser !== NULL && $oUser->getId() === 1) {
            return TRUE;
        }

        return (static::getPerms() !== NULL) ? in_array($sPermission, static::getPerms()) : FALSE;
    }

    /**
     * Reset logged user permissions.
     *
     * @static
     * @return    array
     * @since   1.0.0, 2015-01-10
     * @version 2.1.0-dev
     */
    public static function reset()
    {
        $oUser        = \Model\User::getLoggedUser();
        $aPermissions = [];

        foreach($oUser->getRoles() as $oRole) {
            /* @var $oRole \Model\User\Role */
            foreach($oRole->getPermissions() as $oPermission) {
                /* @var $oPermission \Model\User\Permission */
                $aPermissions[$oPermission->getName()] = $oPermission->getName();
            }
        }

        static::setPerms($aPermissions);

        return $aPermissions;
    }

}