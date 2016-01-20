<?php

namespace Model\Menu\Item;

use Plethora\ModelCore;
use Plethora\Form;
use Plethora\Validator;

/**
 * @Entity
 * @Table(name="menu_items_locales")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          menu
 * @subpackage       classes\Model\Menu\Item
 * @since            1.1.2-dev
 * @version          1.3.0-dev
 */
class Locales extends ModelCore\Locales
{
    /**
     * Parent.
     *
     * @ManyToOne(targetEntity="\Model\Menu\Item", inversedBy="locales")
     * @JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE", nullable=FALSE)
     *
     * @access  protected
     * @var     \Model\Menu\Item
     * @since   1.1.2-dev
     */
    protected $parent;

    /**
     * Menu item name.
     *
     * @Column(type="string", length=30, nullable=FALSE)
     *
     * @access  protected
     * @var     string
     * @since   1.1.2-dev
     */
    protected $name;

    /**
     * Constructor.
     *
     * @access   public
     * @since    1.1.2-dev
     * @version  1.1.2-dev
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate Model config.
     *
     * @static
     * @access   public
     * @return   ModelCore\MConfig
     * @since    1.1.2-dev
     * @version  1.3.0-dev
     */
    public static function generateConfig()
    {
        $config = parent::generateConfig();

        // BACKEND
        $config->addField(Form\Field\Hidden::singleton('id')
            ->setDisabled()
        );

        $config->addField(Form\Field\Text::singleton('name')
            ->setLabel(__('Name'))
        );

        // return config
        return $config;
    }

    /**
     * Get name of the particular menu item.
     *
     * @access   public
     * @return   string
     * @sicne    1.1.5-dev, 2015-08-22
     * @version  1.1.5-dev, 2015-08-22
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get name of the particular menu item.
     *
     * @access   public
     * @return   \Model\Menu\Item
     * @sicne    1.1.5-dev, 2015-08-22
     * @version  1.1.5-dev, 2015-08-22
     */
    public function getParent()
    {
        return $this->parent;
    }

}
