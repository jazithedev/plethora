<?php

namespace Plethora;

use Plethora\Exception;
use Plethora\Exception\Fatal as FatalException;
use Plethora\Helper\Arrays as ArraysHelper;
use Plethora\Helper\String as StringHelper;
use Plethora\Router;
use Plethora\View;

/**
 * Main controller used generally as a parent for the rest of project's
 * controllers.
 *
 * @package          Plethora
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class Controller
{

    /**
     * Page "title" tag content.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sTitle = '';

    /**
     * List of CSS files included to project.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $css = [];

    /**
     * List of JavaScript files included to project.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $js = [];

    /**
     * List of metatags.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aMeta = [];

    /**
     * Path to main View of this controller.
     *
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sView = 'base/blocks/body/content/content';

    /**
     * @access    protected
     * @var        View
     * @since     1.0.0-alpha
     */
    protected $oView;

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sViewMain = 'base/index';

    /**
     * @access    protected
     * @var        View
     * @since     1.0.0-alpha
     */
    protected $oViewMain;

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sViewHead = 'base/blocks/head';

    /**
     * @access    protected
     * @var        View
     * @since     1.0.0-alpha
     */
    protected $oViewHead;

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sViewBody = 'base/blocks/body';

    /**
     * @access    protected
     * @var        View
     * @since     1.0.0-alpha
     */
    protected $oViewBody;

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sViewBodyHeader = 'base/blocks/body/header';

    /**
     * @access    protected
     * @var        View
     * @since     1.0.0-alpha
     */
    protected $oViewBodyHeader;

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sViewBodyContent = 'base/blocks/body/content';

    /**
     * @access    protected
     * @var        View
     * @since     1.0.0-alpha
     */
    protected $oViewBodyContent;

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sViewBodyFooter = 'base/blocks/body/footer';

    /**
     * @access    protected
     * @var        View
     * @since     1.0.0-alpha
     */
    protected $oViewBodyFooter;

    /**
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aBreadcrumbs = [];

    /**
     * List of various types of system messages.
     *
     * @access    protected
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aSystemMessages = [];

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sViewBreadcrumbs = 'base/blocks/body/content/breadcrumbs';

    /**
     * @access    protected
     * @var        View
     * @since     1.0.0-alpha
     */
    protected $oViewBreadcrumbs;

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sViewSystemMessages = 'base/blocks/body/content/system_messages';

    /**
     * View instance of system messages.
     *
     * @access    protected
     * @var        View
     * @since     1.0.0-alpha
     */
    protected $oViewSystemMessages;

    /**
     * Body classes.
     *
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sBodyClasses = '';

    /**
     * Constructor.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct()
    {
        // renew logged user permissions (only for those users, which already have some permissions)
        if(!is_null(Session::get('uid')) && !is_null(Session::get('perm'))) {
            \UserPermissions::reset();
        }

        // set default page title and description (based on app config "base")
        $this->setTitle(Config::get('base.app_name'));
        $this->setDescription(Config::get('base.app_description'));
        $this->setBodyBasicClasses();

        // initalize basic views
        $this->oViewMain           = View::factory($this->sViewMain);
        $this->oViewBody           = View::factory($this->sViewBody);
        $this->oViewBodyContent    = View::factory($this->sViewBodyContent);
        $this->oViewBodyFooter     = View::factory($this->sViewBodyFooter);
        $this->oViewBodyHeader     = View::factory($this->sViewBodyHeader);
        $this->oViewHead           = View::factory($this->sViewHead);
        $this->oView               = View::factory($this->sView);
        $this->oViewBreadcrumbs    = View::factory($this->sViewBreadcrumbs);
        $this->oViewSystemMessages = View::factory($this->sViewSystemMessages);

        // relate views with each other
        $this->oViewMain->bind('oHead', $this->oViewHead);
        $this->oViewMain->bind('oBody', $this->oViewBody);
        $this->oViewMain->bind('sBodyClasses', $this->sBodyClasses);

        $this->oViewBody->bind('sTitle', $this->sTitle);
        $this->oViewBody->bind('oHeader', $this->oViewBodyHeader);
        $this->oViewBody->bind('oContent', $this->oViewBodyContent);
        $this->oViewBody->bind('oFooter', $this->oViewBodyFooter);

        $this->oViewBodyContent->bind('oContent', $this->oView);
        $this->oViewBodyContent->bind('oController', $this);
        $this->oViewBodyContent->bind('oBreadcrumbs', $this->oViewBreadcrumbs);
        $this->oViewBodyContent->bind('oSystemMessages', $this->oViewSystemMessages);

        $this->oViewBreadcrumbs->bind('aBreadcrumbs', $this->aBreadcrumbs);
        $this->oViewSystemMessages->bind('aSystemMessages', $this->aSystemMessages);

        $this->oViewHead->bind('sTitle', $this->sTitle);
        $this->oViewHead->bind('aCss', $this->css);
        $this->oViewHead->bind('aJs', $this->js);
        $this->oViewHead->bind('aMeta', $this->aMeta);

        // set default meta
        $this->findDefaultMeta();

        // Create log about Controller initalization
        Log::insert('Main controller class initialized!');
    }

    /**
     * Find default metatags content for current path.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function findDefaultMeta()
    {
        $aModules = Router::getModules();
        $sUri     = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $aConfig  = [];

        // get metatags from modules
        foreach(array_keys($aModules) as $sModule) {
            try {
                $aConfig = Config::get($sModule.'.meta.'.$sUri);
            } catch(Exception $e) {

            }
        }

        // get metatags from app
        try {
            $aConfig = Config::get('meta.'.$sUri);
        } catch(Exception $e) {

        }

        // set keywords
        if(!empty($aConfig)) {
            $this->setDefaultMeta($aConfig);
        }
    }

    /**
     * Set default metatags content for current path.
     *
     * @access   private
     * @param    array $aConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function setDefaultMeta(array $aConfig)
    {
        $sCurrentLang = Router::getLang();

        foreach($aConfig as $sName => $aAllLangs) {
            foreach($aAllLangs as $sLang => $sContent) {
                if($sCurrentLang === $sLang) {
                    $this->addMetaTagRegular($sName, $sContent);
                }
            }
        }
    }

    /**
     * Create response for particular Controller.
     *
     * @access   public
     * @param    View $oContent
     * @return   Response
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function createResponse(View $oContent = NULL)
    {
        if(is_null($oContent)) {
            $oContent = $this->{Router::getActionName()}();
            $this->afterAction();
        }

        $sContent = '';

        if($oContent !== NULL) {
            $this->oView->bind('oContent', $oContent);

            // developers toolbar - CSS
            if(Router::hasModule('dev_toolbar') && \UserPermissions::hasPerm('dev_toolbar')) {
                $this->addJs('/themes/_common/js/dev_toolbar.js');
                $this->addCss('/themes/backend/css/dev_toolbar.css');
                $this->addBodyClass('dev_toolbar');
            }

            // render page View
            $sContent = $this->oViewMain->render();

            // add last benchmark
            Benchmark::mark('end');

            // developers toolbar
            if(Router::hasModule('dev_toolbar') && \UserPermissions::hasPerm('dev_toolbar')) {
                $sToolbar = \DevToolbar\Toolbar::factory()->render();
                $sContent = str_replace('</body>', $sToolbar.'</body>', $sContent);
            }
        }

        // create response
        $oResponse = new Response();
        $oResponse->setContent($sContent);

        // clear temp data after response creation
        Session::clearTempData();

        // return response
        return $oResponse;
    }

    /**
     * Create independent response.
     *
     * @static
     * @author   Krzysztof Trzos
     * @access   public
     * @param    View $oContent
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function independentResponse(View $oContent)
    {
        $sContent = $oContent->render();

        $oResponse = new Response();
        $oResponse->setContent($sContent);

        return $oResponse;
    }

    /**
     * @access     public
     * @param      string $sString
     * @param      string $sType
     * @return     Controller
     * @throws     Exception\Fatal
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addSystemMessage($sString, $sType)
    {
        if(!in_array($sType, ['success', 'info', 'warning', 'danger'])) {
            throw new FatalException(__('Type of system message must be one of these: success, info, warning, danger.'));
        }

        $this->aSystemMessages[] = [$sString, $sType];

        return $this;
    }

    /**
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getSystemMessages()
    {
        return $this->aSystemMessages;
    }

    /**
     * @access     protected
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function afterAction()
    {
        // to override
    }

    /**
     * @access     public
     * @param    string $sName
     * @param    string $sUrl
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addBreadCrumb($sName, $sUrl = NULL)
    {
        $this->aBreadcrumbs[] = [
            'name' => $sName,
            'url'  => $sUrl,
        ];
    }

    /**
     * Remove all of breadcrumbs.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function removeBreadcrumbs()
    {
        $this->aBreadcrumbs = [];
    }

    /**
     * Remove a fixed amount of breadcrumbs.
     *
     * @access     public
     * @param    integer $iBackwardsAmount
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function removeBreadcrumb($iBackwardsAmount = 1)
    {
        while($iBackwardsAmount > 0) {
            if(count($this->aBreadcrumbs) == 0) {
                break;
            }

            array_pop($this->aBreadcrumbs);

            $iBackwardsAmount--;
        }
    }

    /**
     * Get page title.
     *
     * @access     public
     * @return    string
     * @version    1.0.0-alpha
     */
    public function getTitle()
    {
        return $this->sTitle;
    }

    /**
     * Get page <i>h1</i> tag content (title).
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getTitleForH1()
    {
        $sTitle = $this->getTitle();

        if(!empty($sTitle)) {
            $sTitle = ArraysHelper::last(explode(' - ', $sTitle));
        }

        return $sTitle;
    }

    /**
     * Set page title.
     *
     * @access   public
     * @param    string $sValue
     * @return   Controller
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setTitle($sValue)
    {
        $this->sTitle = $sValue;

        return $this;
    }

    /**
     * Add new part of title.
     *
     * @access     public
     * @param    string $sValue
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addToTitle($sValue)
    {
        $this->sTitle .= $sValue;
    }

    /**
     * Get page description meta-tag.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getDescription()
    {
        return $this->getMetaTagContent('description');
    }

    /**
     * @access     public
     * @param    string $sValue
     * @return    Controller
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setDescription($sValue)
    {
        $this->addMetaTagRegular('description', StringHelper::substrWords($sValue, 155));

        return $this;
    }

    /**
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getKeywords()
    {
        return $this->getMetaTagContent('keywords');
    }

    /**
     * @access   public
     * @param    string $sValue
     * @return   Controller
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setKeywords($sValue)
    {
        $this->addMetaTagRegular('keywords', $sValue);

        return $this;
    }

    /**
     * Add new CSS file to the current project.
     *
     * @access   public
     * @param    string $value
     * @return   Controller
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addCss($value)
    {
        if(!in_array($value, $this->css)) {
            $this->css[] = $value;
        }

        return $this;
    }

    /**
     * Add new CSS file to the current project.
     *
     * @access   public
     * @param    string $value
     * @param    string $theme
     * @return   Controller
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addCssByTheme($value, $theme = NULL)
    {
        if($theme === NULL) {
            $theme = Theme::getTheme();
        }

        $sToAdd = '/themes/'.$theme.$value;

        if(!in_array($sToAdd, $this->css)) {
            $this->css[] = $sToAdd;
        }

        return $this;
    }

    /**
     * Get list of CSS files.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Reset (remove) all stylesheets.
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function resetCss()
    {
        $this->css = [];

        return $this;
    }

    /**
     * Add new JavaScript file.
     *
     * @access   public
     * @param    string $value
     * @return   Controller
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addJs($value)
    {
        if(!in_array($value, $this->js)) {
            $this->js[] = $value;
        }

        return $this;
    }

    /**
     * Add new JavaScript file.
     *
     * @access   public
     * @param    string $value
     * @param    string $theme
     * @return   Controller
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addJsByTheme($value, $theme = NULL)
    {
        if($theme === NULL) {
            $theme = Theme::getTheme();
        }

        $toAdd = '/themes/'.$theme.$value;

        if(!in_array($toAdd, $this->js)) {
            $this->js[] = $toAdd;
        }

        return $this;
    }

    /**
     * Get list of JavaScript files which will be included to particular
     * project.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * Reset (remove) all javascripts.
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function resetJs()
    {
        $this->js = [];

        return $this;
    }

    /**
     * Add new metatag.
     *
     * @access   public
     * @param    array $value array('name' => $sName, 'content' => $sContent
     * @return   Controller
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addMetaTag(array $value)
    {
        foreach($value as &$sValue) {
            $sValue = htmlspecialchars($sValue);
        }

        $this->aMeta[] = $value;

        return $this;
    }

    /**
     * Get single metatag content.
     *
     * @access   public
     * @param    string $key
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMetaTagContent($key)
    {
        return ArraysHelper::path($this->aMeta, $key.'.content');
    }

    /**
     * Add regular metatag.
     *
     * @access     public
     * @param      string $name
     * @param      string $content
     * @return     Controller
     * @throws     FatalException
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addMetaTagRegular($name, $content)
    {
        if(!preg_match('/^[a-z]+$/', $name)) {
            throw new FatalException('Wrong name for metatag ("'.$name.'").');
        }

        $this->aMeta[$name] = [
            'name'    => $name,
            'content' => htmlspecialchars($content),
        ];

        return $this;
    }

    /**
     * Get all meta-tags.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getMetaTags()
    {
        return $this->aMeta;
    }

    /**
     * Add new body class.
     *
     * @access     public
     * @param    string $sClass
     * @return    Controller
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addBodyClass($sClass)
    {
        $this->sBodyClasses .= ' '.$sClass;

        return $this;
    }

    /**
     * Set basic classes to "body" tag.
     *
     * @access     protected
     * @return    Controller
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function setBodyBasicClasses()
    {
        // add classes whether current page is front or not
        $this->addBodyClass(Router::isFrontPage() ? 'front' : 'not_front');

        // get current route
        $this->addBodyClass('route_'.Router::getCurrentRouteName());

        return $this;
    }

}
