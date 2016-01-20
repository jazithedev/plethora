<?php

namespace Model\User;

use Model;
use Plethora\ModelCore;

/**
 * Model used to password recovery.
 *
 * @Entity
 * @Table(name="users_recovery_codes")
 *
 * @author         Krzysztof Trzos
 * @package        user
 * @subpackage     model\user
 * @since          2.23.0, 2015-02-17
 * @version        1.0.0, 2015-02-17
 */
class RecoveryCode extends ModelCore
{
    /**
     * Model of an user.
     *
     * @Id
     * @OneToOne(targetEntity="\Model\User")
     * @JoinColumn(onDelete="cascade")
     *
     * @var        Model\User
     * @since    1.0.0, 2015-02-17
     */
    protected $user;

    /**
     * Code used to recovery user password.
     *
     * @Column(type="string", length=70)
     *
     * @var        string
     * @since    1.0.0, 2015-02-17
     */
    protected $code;

    /**
     * Get recovery code.
     *
     * @access     public
     * @return    string
     * @since      1.0.0, 2015-02-17
     * @version    1.0.0, 2015-02-17
     */
    public function getCode()
    {
        return $this->code;

    }

    /**
     * Set recovery code.
     *
     * @access     public
     * @param    string $sValue
     * @since      1.0.0, 2015-02-17
     * @version    1.0.0, 2015-02-17
     */
    public function setCode($sValue)
    {
        $this->code = $sValue;

    }

    /**
     * Get user.
     *
     * @access   public
     * @return   Model\User
     * @since    1.0.0, 2015-02-17
     * @version  1.0.0, 2015-02-17
     */
    public function getUser()
    {
        return $this->user;

    }

    /**
     * Set user.
     *
     * @access   public
     * @param    Model\User $oUser
     * @since    1.0.0, 2015-02-17
     * @version  1.0.0, 2015-02-17
     */
    public function setUser(Model\User $oUser)
    {
        $this->user = $oUser;

    }
}