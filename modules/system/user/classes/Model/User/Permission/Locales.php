<?php

namespace Model\User\Permission;

use Model;
use Plethora\Form;
use Plethora\ModelCore;

/**
 * @Entity
 * @Table(name="users_permissions_locales")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          user
 * @version          1.1.0, 2015-01-10
 */
class Locales extends ModelCore\Locales
{

    /**
     * Reference to parent.
     *
     * @ManyToOne(targetEntity="\Model\User\Permission", inversedBy="locales")
     * @var    Model\User\Permission
     * @since  1.0.0, 2014-02-15
     */
    protected $parent;

    /**
     * Permissions description.
     *
     * @Column(type="string", length=255)
     *
     * @access  protected
     * @var     string
     * @since   1.0.0, 2015-01-08
     */
    protected $description;

    /**
     * Generate config object of particular Model.
     *
     * @static
     * @author   Krzysztof Trzos
     * @access   protected
     * @return   ModelCore\MConfig
     * @since    1.1.0, 2015-01-10
     * @version  1.1.0, 2015-01-10
     */
    public static function generateConfig()
    {
        $oConfig = parent::generateConfig();

        $oConfig->addField(
            Form\Field\Textarea::singleton('description')
                ->setLabel('Opis')
        );

        return $oConfig;
    }

    /**
     * Get parent.
     *
     * @access   public
     * @return   Model\User\Permission
     * @since    1.0.0, 2015-01-08
     * @version  1.0.0, 2015-01-08
     */
    public function getParent()
    {
        return $this->description;
    }

    /**
     * Get permissions description.
     *
     * @access   public
     * @return   string
     * @since    1.0.0, 2015-01-08
     * @version  1.0.0, 2015-01-08
     */
    public function getDescription()
    {
        return $this->description;
    }

}