<?php

namespace Model\User;

use Doctrine;
use Plethora\Form;
use Plethora\ModelCore;

/**
 * @Entity
 * @Table(name="users_permissions")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2013, Krzysztof Trzos
 * @package          Model
 * @subpackage       User
 * @since            1.0.0
 * @version          1.1.1-dev, 2015-08-10
 */
class Permission extends ModelCore
{
    /**
     * Permission ID.
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    protected $id;

    /**
     * Permission name.
     *
     * @Column(type="string", length=20, unique=TRUE)
     * @access    protected
     * @var        string
     */
    protected $name;

    /**
     * List of roles which are using this permission.
     *
     * @ManyToMany(targetEntity="\Model\User\Role", mappedBy="permissions")
     *
     * @var    Doctrine\Common\Collections\ArrayCollection
     * @since  2015-01-08
     */
    protected $roles;

    /**
     * @OneToMany(targetEntity="\Model\User\Permission\Locales", mappedBy="parent")
     *
     * @access  protected
     * @var     array
     * @since   2015-01-10
     */
    protected $locales;

    /**
     * Constructor.
     *
     * @access     public
     * @since      2013-12-23
     * @version    2015-01-08
     */
    public function __construct()
    {
        parent::__construct();

        $this->roles = new Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Generate config object of particular Model.
     *
     * @static
     * @author   Krzysztof Trzos
     * @access   protected
     * @return   ModelCore\MConfig
     * @since    2015-01-10
     * @version  1.1.1-dev, 2015-08-10
     */
    public static function generateConfig()
    {
        // get config
        $config = parent::generateConfig();

        // add fields
        $config->addField(Form\Field\Hidden::singleton('id'));

        $config->addField(Form\Field\Text::singleton('name')
            ->setLabel(__('Name'))
            ->setRequired());

        // return config
        return $config;
    }

    /**
     * Get ID of a permission.
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
     * Get name of a permission.
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
     * Set name of a permission.
     *
     * @access     public
     * @param    string $sValue
     * @since      2013-12-23
     * @version    2015-01-08
     */
    public function setName($sValue)
    {
        $this->name = $sValue;
    }

    /**
     * Get roles which are using this permission.
     *
     * @access     public
     * @return    array
     * @since      2013-12-23
     * @version    2015-01-08
     */
    public function getRoles()
    {
        return $this->roles;
    }

}