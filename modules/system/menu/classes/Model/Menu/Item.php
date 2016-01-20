<?php

namespace Model\Menu;

use Doctrine;
use Plethora\DB;
use Plethora\Form;
use Plethora\ModelCore;
use Plethora\Router;
use Plethora\Validator;
use Plethora\Exception;
use Plethora\Helper;

/**
 * @author           Krzysztof Trzos
 * @copyright    (c) 2015, Krzysztof Trzos
 * @package          menu
 * @subpackage       classes\Model
 * @since            1.1.1-dev
 * @version          1.2.0-dev
 *
 * @Entity
 * @Table(name="menu_items")
 */
class Item extends ModelCore implements ModelCore\ModelInterface
{
    use ModelCore\Traits\Sortable;

    /**
     * Project ID.
     *
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="id")
     *
     * @access    protected
     * @var        integer
     * @since     1.1.2-dev
     */
    protected $id;

    /**
     * Locales.
     *
     * @OneToMany(targetEntity="\Model\Menu\Item\Locales", mappedBy="parent", cascade={"all"}, fetch="EAGER")
     *
     * @access    protected
     * @var        \Doctrine\Common\Collections\ArrayCollection
     * @since     1.1.2-dev
     */
    protected $locales;

    /**
     * Parent.
     *
     * @ManyToOne(targetEntity="\Model\Menu", inversedBy="items", fetch="EAGER")
     * @JoinColumn(name="menu_id", referencedColumnName="id", nullable=FALSE)
     *
     * @access  protected
     * @var     \Model\Menu
     * @since   1.1.1-dev
     */
    protected $menu;

    /**
     * Route related to this menu item.
     *
     * @Column(type="string", length=50, nullable=FALSE, name="route")
     *
     * @access  protected
     * @var     string
     * @since   1.1.2-dev
     */
    protected $route;

    /**
     * This particular menu item classes.
     *
     * @Column(type="string", length=100, nullable=TRUE, name="classes")
     *
     * @access  protected
     * @var     string
     * @since   1.2.0-dev
     */
    protected $classes;

    /**
     * Parameters of the route related to this menu item.
     *
     * @Column(type="array", nullable=TRUE, name="route_parameters")
     *
     * @access  protected
     * @var     array
     * @since   1.1.2-dev
     */
    protected $route_parameters;

    /**
     * External URL.
     *
     * @Column(type="string", length=100, nullable=TRUE, name="url")
     *
     * @access  protected
     * @var     string
     * @since   1.1.2-dev
     */
    protected $url;

    /**
     * List of routes to which this menu item is active.
     *
     * @Column(type="simple_array", nullable=TRUE, name="active_routes")
     *
     * @access  protected
     * @var     array
     * @since   1.1.2-dev
     */
    protected $active_routes;

    /**
     * Constructor
     *
     * @access     public
     * @since      1.1.2-dev
     * @version    1.1.3-dev
     */
    public function __construct()
    {
        parent::__construct();

        $this->locales = new \Doctrine\Common\Collections\ArrayCollection();

        // get menu ID
        if(Router::getCurrentRouteName() === 'backend' && in_array(Router::getParam('action'), ['add', 'edit'])) {
            $menuID = (int)Router::getParam('id');

            $this->menu = DB::find('\Model\Menu', $menuID);
        }
    }

    /**
     * Fields config for backend.
     *
     * @static
     * @access   protected
     * @return   ModelCore\MConfig
     * @since    1.1.0-dev
     * @version  1.3.0-dev
     */
    protected static function generateConfig()
    {
        // get config from parent
        $config = parent::generateConfig();

        // get list of all routes
        $routesList    = array_keys(Router::getRoutes());
        $routesOptions = [];

        foreach($routesList as $value) {
            $routesOptions[$value] = [
                'value' => $value,
                'label' => $value,
            ];
        }

        // BACKEND
        $config->addField(Form\Field\Hidden::singleton('id')
            ->setDisabled()
        );

        $config->addField(Form\Field\Select::singleton('route')
            ->setOptions(array_combine($routesList, $routesList))
            ->setLabel(__('Route'))
            ->setRequired()
        );

        $config->addField(Form\Field\Text::singleton('route_parameters')
            ->setLabel(__('Route parameters'))
            ->setQuantity(0)
        );

        $config->addField(Form\Field\Text::singleton('url')
            ->setLabel('URL')
        );

        $config->addField(Form\Field\Checkbox::singleton('active_routes')
            ->setColumnsAmount(3)
            ->setOptions($routesOptions)
            ->setLabel(__('Active routes'))
            ->setTip(__('List of routes for which the actual route will be active'))
        );

        $config->addField(Form\Field\Text::singleton('classes')
            ->setLabel(__('HTML classes'))
            ->addRulesSet(Validator\RulesSetBuilder\String::factory()
                ->regex(':value', '[0-9a-z_-]*'))
        );

        // return config
        return $config;
    }

    /**
     * Search engine configuration.
     *
     * @static
     * @access	 public
     * @return	 array
     * @since	 1.3.0-dev
     * @version	 1.3.0-dev
     */
    public static function getConfigSearchEngine() {
        $oConfig = parent::getConfigSearchEngine();
        $oConfig->addFromRel('locales', 'name');

        return $oConfig;
    }

    /**
     * Get identifier of a menu item.
     *
     * @access   public
     * @return   integer
     * @since    1.1.1-dev
     * @version  1.1.2-dev
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get menu object to which this item belongs.
     *
     * @access   public
     * @return   \Model\Menu
     * @since    1.1.3-dev
     * @version  1.1.3-dev
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Get URL to which this item corresponds.
     *
     * @access   public
     * @return   string
     * @since    1.1.5-dev
     * @version  1.1.5-dev
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * Get route to which this item corresponds.
     *
     * @access   public
     * @return   string
     * @since    1.1.5-dev
     * @version  1.1.5-dev
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Get classes of this menu item.
     *
     * @access   public
     * @return   string
     * @since    1.2.0-dev
     * @version  1.2.0-dev
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Get list of parameters for a given route.
     *
     * @access   public
     * @return   array
     * @since    1.1.5-dev
     * @version  1.1.5-dev
     */
    public function getRouteParams()
    {
        return $this->route_parameters;
    }

    /**
     * Get list of routes to which this menu item will be rendered as active.
     *
     * @access   public
     * @return   array
     * @since    1.1.5-dev
     * @version  1.1.5-dev
     */
    public function getActiveRoutes()
    {
        return $this->active_routes;
    }
}
