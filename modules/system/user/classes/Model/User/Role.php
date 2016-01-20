<?php

namespace Model\User;

use Doctrine;
use Plethora\DB;
use Plethora\Form;
use Plethora\ModelCore;

/**
 * @Entity
 * @Table(name="users_roles")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2013, Krzysztof Trzos
 * @package          Model
 * @subpackage       User
 * @since            1.0.0
 * @version          1.1.1-dev, 2015-08-10
 */
class Role extends ModelCore
{

    /**
     * Role ID.
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @access    protected
     * @var        integer
     */
    protected $id;

    /**
     * Role name.
     *
     * @Column(type="string", length=20, unique=TRUE)
     *
     * @access  protected
     * @var     string
     */
    protected $name;

    /**
     * List of role permissions.
     *
     * @ManyToMany(targetEntity="\Model\User\Permission", inversedBy="roles")
     * @JoinTable(name="users_roles_permissions")
     *
     * @access  protected
     * @var     Doctrine\Common\Collections\ArrayCollection
     */
    protected $permissions;

    /**
     * List of users which have particular role.
     *
     * @ManyToMany(targetEntity="\Model\User", mappedBy="roles")
     *
     * @access  protected
     * @var     Doctrine\Common\Collections\ArrayCollection
     */
    protected $users;

    /**
     * Constructor.
     *
     * @access   public
     * @since    2013-12-23
     * @version  2015-01-08
     */
    public function __construct()
    {
        parent::__construct();

        $this->permissions = new Doctrine\Common\Collections\ArrayCollection();
        $this->users       = new Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Generate config object of particular Model.
     *
     * @overwritten
     * @static
     * @author     Krzysztof Trzos
     * @access     protected
     * @return    ModelCore\MConfig
     * @since      2015-01-10
     * @version    1.1.1-dev, 2015-08-10
     */
    public static function generateConfig()
    {
        // get all permissions list
        $aPermissions = [];
        $aResult      = DB::queryList('\Model\User\Permission')->execute();

        foreach($aResult as $oPermission) {
            /* @var $oPermission Permission */
            $aPermissions[$oPermission->getId()] = [
                'value' => $oPermission->getId(),
                'label' => $oPermission->getName(),
            ];
        }

        $config = parent::generateConfig();

        // return MConfig
        $config->addField(Form\Field\Hidden::singleton('id'));

        $config->addField(Form\Field\Text::singleton('name')
            ->setRequired()
            ->setLabel(__('Name')));

        $config->addField(Form\Field\CheckboxRelation::singleton('permissions')
            ->setRelatedModelName('\Model\User\Permission')
            ->setColumnsAmount(3)
            ->setOptions($aPermissions)
            ->setLabel(__('Permissions')));

        return $config;
    }

    /**
     * Get ID of the role.
     *
     * @access     public
     * @return    integer
     * @since      2013-12-23
     * @version    2015-01-08
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Get role name.
     *
     * @access     public
     * @return    string
     * @since      2013-12-23
     * @version    2015-01-08
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set role name.
     *
     * @access     public
     * @param    string $sName
     * @since      2013-12-23
     * @version    2015-01-08
     */
    public function setName($sName)
    {
        $this->name = $sName;
    }

    /**
     * @access     public
     * @return    Doctrine\Common\Collections\ArrayCollection
     * @since      2013-12-23
     * @version    2015-01-08
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @access     public
     * @return    Doctrine\Common\Collections\ArrayCollection
     * @since      2013-12-23
     * @version    2015-01-08
     */
    public function getUsers()
    {
        return $this->users;
    }

}