<?php

namespace Model\User;

use Model\User;
use Plethora\ModelCore;

/**
 * @Entity
 * @Table(name="users_activation_codes")
 *
 * @author         Krzysztof Trzos
 * @package        Model
 * @subpackage     User
 * @since          1.0.0
 * @version        1.0.1, 2013-12-14
 */
class ActivationCode extends ModelCore
{
    /**
     * @Id
     * @OneToOne(targetEntity="\Model\User")
     * @JoinColumn(onDelete="cascade")
     *
     * @since    1.0.1, 2013-12-14
     */
    protected $user;

    /**
     * @Column(type="string", length=70)
     * @var string
     *
     * @since    1.0.1, 2013-12-14
     */
    protected $code;

    /**
     * @access  public
     * @return  string
     */
    public function getCode() { return $this->code; }

    /**
     * @access  public
     * @param   string $sValue
     */
    public function setCode($sValue) { $this->code = $sValue; }

    /**
     * @access  public
     * @return  User
     */
    public function getUser() { return $this->user; }

    /**
     * @access  public
     * @param   User $oUser
     */
    public function setUser(User $oUser) { $this->user = $oUser; }
}