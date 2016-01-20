<?php

namespace Model\Menu;

use Plethora\Form;
use Plethora\Validator;
use Plethora\ModelCore;

/**
 * @Entity
 * @Table(name="menu_locales")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          menu
 * @subpackage       classes\Model\Menu
 * @since            1.1.1-dev
 * @version          1.3.0-dev
 */
class Locales extends ModelCore\Locales
{
    /**
     * Parent.
     *
     * @ManyToOne(targetEntity="\Model\Menu", inversedBy="locales")
     * @JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE", nullable=FALSE)
     *
     * @access  protected
     * @var     \Model\Menu
     * @since   1.1.1-dev
     */
    protected $parent;

    /**
     * Menu title.
     *
     * @Column(type="string", length=100, nullable=FALSE)
     *
     * @access  protected
     * @var     string
     * @since   1.1.1-dev
     */
    protected $title;

    /**
     * Constructor.
     *
     * @access   public
     * @since    1.1.1-dev
     * @version  1.1.1-dev
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
     * @since    1.1.1-dev
     * @version  1.3.0-dev
     */
    public static function generateConfig()
    {
        $config = parent::generateConfig();

        // BACKEND
        $config->addField(Form\Field\Hidden::singleton('id')
            ->setDisabled()
        );

        $config->addField(Form\Field\Text::singleton('title')
            ->setLabel(__('Menu title'))
        );

        // return config
        return $config;
    }

    /**
     * Get menu title.
     *
     * @access   public
     * @return   string
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function getTitle()
    {
        return $this->title;
    }
}
