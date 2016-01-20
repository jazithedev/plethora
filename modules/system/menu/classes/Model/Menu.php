<?php

namespace Model;

use Doctrine;
use Plethora\Form;
use Plethora\ModelCore;
use Plethora\Validator;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          menu
 * @subpackage       classes\Model
 * @since            1.1.0-dev
 * @version          1.3.0-dev
 *
 * @Entity
 * @Table(name="menu")
 */
class Menu extends ModelCore implements ModelCore\ModelInterface
{

    /**
     * Project ID.
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     *
     * @access  protected
     * @var     integer
     * @since   1.1.0-dev
     */
    protected $id;

    /**
     * Locales.
     *
     * @OneToMany(targetEntity="\Model\Menu\Locales", mappedBy="parent")
     *
     * @access    protected
     * @var        Doctrine\Common\Collections\ArrayCollection
     * @since     1.1.0-dev
     */
    protected $locales;

    /**
     * Locales.
     *
     * @OneToMany(targetEntity="\Model\Menu\Item", mappedBy="menu")
     *
     * @access    protected
     * @var        Doctrine\Common\Collections\ArrayCollection
     * @since     1.1.2-dev
     */
    protected $items;

    /**
     * Project working name.
     *
     * @Column(type="string", length=70, nullable=FALSE)
     *
     * @access    protected
     * @var        string
     * @since     1.1.1-dev
     */
    protected $working_name;

    /**
     * Constructor
     *
     * @access     public
     * @since      1.1.2-dev
     * @version    1.1.2-dev
     */
    public function __construct()
    {
        parent::__construct();

        $this->locales = new Doctrine\Common\Collections\ArrayCollection();
        $this->items   = new Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Fields config for backend.
     *
     * @access   public
     * @return   ModelCore\MConfig
     * @since    1.1.0-dev
     * @version  1.3.0-dev
     */
    protected static function generateConfig()
    {
        $config = parent::generateConfig();

        // BACKEND
        $config->addField(Form\Field\Hidden::singleton('id')
            ->setDisabled()
        );

        $config->addField(Form\Field\Text::singleton('working_name')
            ->setLabel(__('Working name'))
            ->addRulesSet(Validator\RulesSetBuilder\String::factory()
                ->regex(':value', '^[a-z_]*$', __('The working name must contain only lowercase letters and underscores.'))
            )
        );

        // return config
        return $config;
    }

    /**
     * Search engine configuration.
     *
     * @static
     * @access   public
     * @return   ModelCore\ConfigSearchEngine
     * @since    1.1.2-dev
     * @version  1.3.0-dev
     */
    public static function getConfigSearchEngine()
    {
        $config = parent::getConfigSearchEngine();
        $config->addFromRel('locales', 'title');

        return $config;
    }

    /**
     * Get ID.
     *
     * @access   public
     * @return   string
     * @since    1.1.0-dev
     * @version  1.1.0-dev
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set a set of items for this menu.
     *
     * @access   public
     * @param    array $items
     * @return   Menu
     * @since    1.1.5-dev
     * @version  1.1.5-dev
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get menu items.
     *
     * @access   public
     * @return   Doctrine\Common\Collections\ArrayCollection
     * @since    1.1.5-dev
     * @version  1.1.5-dev
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set menu working name.
     *
     * @access   public
     * @param    string $value
     * @return   Menu
     * @since    1.1.5-dev
     * @version  1.1.5-dev
     */
    public function setWorkingName($value)
    {
        $this->working_name = $value;

        return $this;
    }

    /**
     * Get menu working name.
     *
     * @access   public
     * @return   string
     * @since    1.1.5-dev
     * @version  1.1.5-dev
     */
    public function getWorkingName()
    {
        return $this->working_name;
    }

    /**
     * Here you can generate items tree of particular menu.
     *
     * @access   public
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function getMenuTree()
    {
        d($this->getItems());
    }

    /**
     * Render menu.
     *
     * @access   public
     * @return   string
     * @since    1.1.5-dev
     * @version  1.1.5-dev
     */
    public function renderMenu()
    {

    }
}
