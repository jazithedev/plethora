<?php

namespace Plethora\Controller;

use Doctrine;
use Plethora\Config;
use Plethora\Controller;
use Plethora\DB;
use Plethora\Form;
use Plethora\ModelCore;
use Plethora\Route;
use Plethora\Router;
use Plethora\Session;
use Plethora\Theme;
use Plethora\View;
use Plethora\Exception;
use Model\User;
use Plethora\Helper;

/**
 * Master controller of backend side of page.
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @package          Controller
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class Backend extends Controller
{

    const PERM_ADMIN_ACCESS       = 'administration';
    const PERM_CONTENT_ADD        = 'content_add';
    const PERM_CONTENT_SORT       = 'content_sort';
    const PERM_CONTENT_EDIT_OWN   = 'content_edit_own';
    const PERM_CONTENT_EDIT_ALL   = 'content_edit_all';
    const PERM_CONTENT_DELETE_OWN = 'content_delete_own';
    const PERM_CONTENT_DELETE_ALL = 'content_delete_all';

    /**
     * Particular controller model.
     *
     * @access  protected
     * @var     ModelCore
     * @since   1.0.0-alpha
     */
    protected $oModel;

    /**
     * Model class.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sModel;

    /**
     * Array of columns displayed in list action.
     *
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    protected $listColumns = ['id'];

    /**
     * Amount of results per page in backend entity list.
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $iListResultsPerPage = 15;

    /**
     * View path to "list" action.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $viewList = 'base/backend/list';

    /**
     * View path to "sort" action.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sViewSort = 'base/backend/list_sort';

    /**
     * View path to "add" action.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sViewForm = 'base/backend/add';

    /**
     * Prefix of all backend content permissions.
     *
     * @static
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected static $sPermissionsPrefix = 'content';

    /**
     * Array of backend list options.
     *
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    protected $listOptions = [];

    /**
     * Name of the column of the model which will be used on the items sorting
     * action.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sColumnForSort = 'name';

    protected $sViewBody = 'base/backend/blocks/body';
    protected $sViewBodyHeader = 'base/backend/blocks/body/header';
    protected $sViewBodyFooter = 'base/backend/blocks/body/footer';
    protected $sViewBodyContent = 'base/backend/blocks/body/content';

    /**
     * Constructor.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct()
    {
        // initialize theme
        Theme::initBackend();

        parent::__construct();

        if($this->sModel !== NULL) {
            $this->setModel(new $this->sModel);
        }

        if(!User::isLogged() || !\UserPermissions::hasPerm(static::PERM_ADMIN_ACCESS)) {
            Route::factory('home')->redirectTo();
        }
//
//        // add main breadcrumbs and title
//        $this->alterBreadcrumbsTitleMain();
//
//        // add CSS and JavaScript files
//        $this->addCss('/themes/_common/packages/bootstrap/css/bootstrap.min.css');
//        $this->addCss('/themes/_common/packages/bootstrap/css/bootstrap-sticky-footer-navbar.css');
//        $this->addJs('/themes/_common/js/jquery-2.1.3.min.js');
//        $this->addJs('/themes/_common/js/jquery-ui.min.js');
//        $this->addJs('/themes/_common/packages/bootstrap/js/bootstrap.min.js');
//        $this->addJs('/themes/_common/js/global/framework.js');
//
//        $this->addCssByTheme('/css/backend.css');
//        $this->addJsByTheme('/js/jquery.mjs.nestedSortable.js');
//        $this->addJsByTheme('/js/backend.js');
    }

    protected function generateMenu()
    {
        # load main menus
        $menus = Config::get('backend.menu');

        # load submenus
        $subMenus = [];

        foreach(array_keys(Router::getModules()) as $module) {
            try {
                $moduleMenus = Config::get($module.'.backend.menu', [], TRUE);

                foreach($moduleMenus as $name => $moduleMenu) {
                    $subMenus[$moduleMenu['parent']][$module][$name] = $moduleMenu;
                }
            } catch(Exception $e) {

            }
        }

        # create and return View
        $view = View::factory('base/backend_adminlte/blocks/body/menu');
        $view->bind('menus', $menus);
        $view->bind('subMenus', $subMenus);

        return $view;
    }

    /**
     * Set Model for backend controller.
     *
     * @access   protected
     * @param    ModelCore $oModel
     * @return   Backend
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function setModel(ModelCore $oModel)
    {
        $this->oModel = $oModel;

        return $this;
    }

    /**
     * Get bakcend controller.
     *
     * @access   protected
     * @return   ModelCore
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function getModel()
    {
        return $this->oModel;
    }

    /**
     * ACTION - Contents list.
     *
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionList()
    {
        $this->addToTitle(__('List'));

        // check access
        $this->alterListAccess();

        // add local action to particular content add
        $this->alterListLocalActions();

        // generate search engine and list query which is based on it
        $search    = $this->getModel()->generateSearchEngine();
        $queries   = $search->generateQuery();
        $listQuery = $queries->getQueryBuilder();

        /* @var $search Helper\SearchEngine */
        /* @var $queries Helper\SearchEngine\SearchEngineGeneratedQueries */
        /* @var $listQuery Doctrine\ORM\QueryBuilder */

        // make list restrictions in relation to logged user permissions
        if(!\UserPermissions::hasPerm($this->getPermissionPrefix().'edit_all') && $this->getModel()->hasField('author')) {
            $listQuery
                ->join('t.author', 'a', 'WITH', 'a.id = ?1')
                ->setParameter('1', User::getLoggedUser()->getId());
        }

        // pager
        $queryParam = filter_input(INPUT_GET, 'results', FILTER_VALIDATE_INT);

        if($queryParam === NULL) {
            $queryParam = 15;
        }

        $pager = Helper\Pager::factory($listQuery, 't', $queryParam);

        // alter query
        $this->alterListQuery($listQuery);

        // load list
        $list = $listQuery->getQuery()->execute();

        if(empty($list)) {
            $this->addSystemMessage(__('Entries list is empty.'), 'info');
        }

        // create list actions
        $this->alterListActions();

        // return View
        return View::factory($this->viewList)
            ->bind('list', $list)
            ->bind('pager', $pager)
            ->bind('listColumns', $this->listColumns)
            ->bind('search', $search)
            ->set('model', $this->getModel())
            ->bind('options', $this->listOptions);
    }

    /**
     * ACTION - Contents list.
     *
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionSort()
    {
        // check access
        if(!\UserPermissions::hasPerm($this->getPermissionPrefix().static::PERM_CONTENT_SORT)) {
            Route::factory('home')->redirectTo();
        }

        // add title ad breadcrumb
        $this->alterBreadcrumbsTitleSort();

        // add local actions
        $this->alterSortLocalActions();

        // generate search engine and list query which is based on it
        $aResult = $this->alterSortQueryResult();

        // load list
        $aList = [];

        if(empty($aResult)) {
            $this->addSystemMessage(__('Entries list is empty.'), 'info');
        } else {
            foreach($aResult as $oObject) {
                /* @var $oObject ModelCore\Traits\Sortable */
                if(!isset($aList[$oObject->getOrderParent()])) {
                    $aList[$oObject->getOrderParent()] = [];
                }

                $aList[$oObject->getOrderParent()][] = $oObject;
            }
        }

        // sort items
        $sortList = function ($aList, $iIndex) use (&$sortList) {
            $aOutput = [];

            if(isset($aList[$iIndex]) && is_array($aList[$iIndex])) {
                foreach($aList[$iIndex] as $oObject) {
                    $aOutput[] = [
                        'object'   => $oObject,
                        'siblings' => $sortList($aList, $oObject->id),
                    ];
                }
            }

            return $aOutput;
        };

        $aListSorted = $sortList($aList, 0);

        // return View
        return View::factory($this->sViewSort)
            ->set('oModel', $this->getModel())
            ->bind('aList', $aListSorted)
            ->bind('sColumn', $this->sColumnForSort);
    }

    /**
     * ACTION - Backend add action.
     *
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function actionAdd()
    {
        // check user access
        $this->checkPermissions('add');

        // add basic JavaScript files
        $this->addeditJs();

        // add title ad breadcrumb
        $this->alterBreadcrumbsTitleAdd();

        // generate form config
        $oConfig = new ModelCore\ModelFormConfig();
        $oConfig->setMessage(__('Add operation was successful.'));

        // generate form
        $oModelForm = $this->getModel()->form('add', $oConfig);
        $oForm      = $oModelForm->generate();

        // return View
        return View::factory($this->sViewForm)
            ->bind('oForm', $oForm)
            ->set('sTitle', $this->getTitle());
    }

    /**
     * ACTION - Backend edit action.
     *
     * @access     public
     * @return     View
     * @throws     Exception\Code403
     * @throws     Exception\Code404
     * @throws     Exception\Fatal
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function actionEdit()
    {
        $sIdParam = Router::getParam('id');

        if($sIdParam === FALSE) {
            throw new Exception\Code404();
        }

        $this->addeditJs();

        try {
            $this->loadModelForActions();
        } catch(Exception $oExc) {
            $this->addToTitle(__('Error'));
            $this->addSystemMessage('XXX', 'danger');
            die('TODO');
        }

        // check permissions
        $this->checkPermissions('edit');

        // add title ad breadcrumb
        $this->alterBreadcrumbsTitleEdit();

        $oConfig = new ModelCore\ModelFormConfig();
        $oConfig->setMessage(__('Edit operation was successful.'));

        $oModelForm = $this->getModel()->form('edit', $oConfig);
        /* @var $oModelForm \Plethora\ModelCore\ModelForm */
        $oForm = $oModelForm->generate();

        return View::factory($this->sViewForm)
            ->bind('oForm', $oForm)
            ->set('sTitle', $this->getTitle());
    }

    /**
     * ACTION - Backend delete action.
     *
     * @access     public
     * @return    View
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function actionDelete()
    {
        try {
            $this->loadModelForActions();
        } catch(Exception $oExc) {
            $this->addToTitle(__('Error'));
            $this->addSystemMessage('XXX', 'error');
            die('TODO');
        }

        // check permissions
        $this->checkPermissions('delete');

        // add title ad breadcrumb
        $this->alterBreadcrumbsTitleDelete();

        // confirm message
        $sMessage = __(
            'Are you sure that want to remove item <b>:name</b> with '.
            'id <b>:id</b> from <b>:section</b> section?',
            [
                'name'    => $this->getModel()->getEntityTitle(),
                'id'      => $this->getModel()->getId(),
                'section' => __(strtolower(Router::getControllerName())),
            ]
        );
        $this->addSystemMessage($sMessage, 'danger');

        // delete form
        $oForm = new Form('delete_form');
        $oForm->setSubmitValue(__('delete'));

        // if form submitted
        if($oForm->isSubmittedAndValid()) {
            $this->alterDeleteUpdateSort();
            $this->alterDelete();
        }

        // return View
        return View::factory($this->sViewForm)
            ->bind('oForm', $oForm)
            ->set('sTitle', $this->getTitle());
    }

    /**
     * This method updates all sort orders for entries which are siblings or
     * parent of currently entity.
     *
     * @access     protected
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function alterDeleteUpdateSort()
    {
        /* @var $model ModelCore|ModelCore\Traits\Sortable */
        $model = $this->getModel();

        if(property_exists($model, 'order_parent') && property_exists($model, 'order_number')) {
            /* @var $iParent integer */
            $iParent = $model->getOrderParent();

            // check siblings
            if($iParent > 0) {
                $aSiblings = DB::query('SELECT t FROM '.$model->getClass().' t WHERE t.order_parent = :parent')
                    ->param('parent', $model->getId())
                    ->execute();

                foreach($aSiblings as $oSibling) {
                    /* @var $oSibling ModelCore\Traits\Sortable|ModelCore */

                    $oSibling->setOrderParent($iParent);
                    $oSibling->save();
                }
            }
        }
    }

    /**
     * Remove particular entity after form submit and if form is valid.
     *
     * @access     protected
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function alterDelete()
    {
        $this->getModel()->remove();
        DB::flush();

        $sController = Router::getParam('controller');
        $sID         = Router::getParam('id', NULL);
        $sExtra      = Router::getParam('extra', NULL);
        $sURL        = Route::factoryBackendURL($sController, 'list', $sID, $sExtra);

        Session::flash($sURL, __('Entry has been deleted successfully.'));
    }

    /**
     * Load Model for controller actions usage.
     *
     * @access   protected
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function loadModelForActions()
    {
        $iId    = (int)Router::getParam('id');
        $sModel = $this->getModel()->getClass();
        $oModel = DB::find($sModel, $iId);

        if($oModel === NULL) {
            throw new Exception\Code404(__('Content with particular id does not exist.'));
        }

        $this->setModel($oModel);
    }

    /**
     * Add all JavaScript scripts for "add" and "edit" actions.
     *
     * @access     private
     * @since      1.0.0
     * @version    1.0.0-alpha
     */
    private function addeditJs()
    {
        $this->addJsByTheme('/packages/tinymce/js/tinymce/tinymce.min.js', '_common');
        $this->addJs('/themes/_common/js/form/form.js');
    }

    /**
     * Get this Controller permissions prefix.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getPermissionPrefix()
    {
        return static::$sPermissionsPrefix.'_';
    }

    /**
     * Check permissions for
     *
     * @static
     * @access   public
     * @param    string $sType
     * @throws   Exception\Code403
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function checkPermissions($sType)
    {
        switch($sType) {
            case 'add':
                if(!\UserPermissions::hasPerm(static::getPermissionPrefix().'add')) {
                    throw new Exception\Code403(__('Permission denied.'));
                }
                break;
            case 'edit':
            case 'delete':
                if(!\UserPermissions::hasPerm(static::getPermissionPrefix().'edit_all') && $this->getModel()->hasField('author')) {
                    $iAuthorID = $this->getModel()->getAuthor()->getId();
                    $oUser     = User::getLoggedUser();

                    if(!\UserPermissions::hasPerm(static::getPermissionPrefix().'edit_all') || $iAuthorID != $oUser->getId()) {
                        throw new Exception\Code403(__('Access denied.'));
                    }
                }
                break;
        }
    }

    /**
     * Add new option to the backend list.
     *
     * @access   protected
     * @param    string            $sURL
     * @param    string            $sTitle
     * @param    string            $sGlyphicon
     * @param    Helper\Attributes $oAttributes
     * @return   \Controller\Backend
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function addListOption($sURL, $sTitle, $sGlyphicon, Helper\Attributes $oAttributes = NULL)
    {
        if($oAttributes === NULL) {
            $oAttributes = new Helper\Attributes;
        }

        $this->listOptions[] = [
            'url'   => $sURL,
            'title' => $sTitle,
            'icon'  => $sGlyphicon,
            'attrs' => $oAttributes,
        ];

        return $this;
    }

    /**
     * Method to overwrite if a list query should be altered before sending
     * request to database.
     *
     * @access   protected
     * @param    Doctrine\ORM\QueryBuilder $oListQuery
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterListQuery(Doctrine\ORM\QueryBuilder $oListQuery)
    {

    }

    /**
     * Method which can be used to overwrite local actions of list subpage.
     *
     * @access   protected
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterListLocalActions()
    {
        $sControllerParam = Router::getParam('controller');

        Router\LocalActions::addLocalAction(__('Add '.$sControllerParam), 'backend', 'backend')
            ->setConditions([
                'controller' => $sControllerParam,
                'action'     => 'list',
            ])
            ->setParameters([
                'controller' => $sControllerParam,
                'action'     => 'add',
            ]);

        if(property_exists($this->getModel(), 'order')) {
            Router\LocalActions::addLocalAction(__('Sort '.$sControllerParam), 'backend', 'backend')
                ->setConditions([
                    'controller' => $sControllerParam,
                    'action'     => 'list',
                ])
                ->setParameters([
                    'controller' => $sControllerParam,
                    'action'     => 'sort',
                ]);
        }
    }

    /**
     * Method which can be used to overwrite local actions of list subpage.
     *
     * @access   protected
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterSortLocalActions()
    {
        $sControllerParam = Router::getParam('controller');

        Router\LocalActions::addLocalAction(__('Add '.$sControllerParam), 'backend', 'backend')
            ->setConditions([
                'controller' => $sControllerParam,
                'action'     => 'sort',
            ])
            ->setParameters([
                'controller' => $sControllerParam,
                'action'     => 'add',
            ]);

        Router\LocalActions::addLocalAction(__($sControllerParam.' list'), 'backend', 'backend')
            ->setConditions([
                'controller' => $sControllerParam,
                'action'     => 'sort',
            ])
            ->setParameters([
                'controller' => $sControllerParam,
                'action'     => 'list',
            ]);
    }

    /**
     * Generate result (containing entities list) for sort list.
     *
     * @access   protected
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterSortQueryResult()
    {
        return DB::query('SELECT t FROM '.$this->getModel()->getClass().' t ORDER BY t.order_number')
            ->execute();
    }

    /**
     * Method which can be used to overwrite of access checking operation..
     *
     * @access   protected
     * @throws   Exception\Fatal
     * @throws   Exception\Code403
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterListAccess()
    {
        if(!$this->getModel() instanceof ModelCore) {
            throw new Exception\Fatal('Model of this backend site is not defined. Set `$sModel` variable in your backend controller.');
        }

        if(!\UserPermissions::hasPerm($this->getPermissionPrefix().'edit_own') && !\UserPermissions::hasPerm($this->getPermissionPrefix().'delete_own')) {
            throw new Exception\Code403(__('Permission denied.'));
        }
    }

    /**
     * Change main (start or parent) breadcrumbs and/or title for this backend
     * constructor.
     *
     * @access   protected
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterBreadcrumbsTitleMain()
    {
        $sControllerOriginal = Router::getControllerName();
        $sURL                = Route::backendUrl($sControllerOriginal, 'list');
        $sControllerName     = strtolower($sControllerOriginal);
        $sectionTranslation  = __('section.'.$sControllerName);

        $this->setTitle(__('Management panel').' - ');
        $this->addToTitle($sectionTranslation.' - ');

        $this->addBreadCrumb(__('Home'), Route::backendUrl());
        $this->addBreadCrumb($sectionTranslation, $sURL);
    }

    /**
     * Change breadcrumbs and/or title of "edit entity" action.
     *
     * @access   protected
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterBreadcrumbsTitleEdit()
    {
        $this->addToTitle(__('Edit entry'));
        $this->addBreadCrumb(__('Edit entry'), Router::getCurrentUrl());
    }

    /**
     * Change breadcrumbs and/or title of "add entity" action.
     *
     * @access   protected
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterBreadcrumbsTitleAdd()
    {
        $this->addToTitle(__('Add entry'));
        $this->addBreadCrumb(__('Add entry'), Router::getCurrentUrl());
    }

    /**
     * Change breadcrumbs and/or title of "sort entities" action.
     *
     * @access   protected
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterBreadcrumbsTitleSort()
    {
        $this->addToTitle(__('Sort entries'));
        $this->addBreadCrumb(__('Sort entries'), Router::getCurrentUrl());
    }

    /**
     * Change breadcrumbs and/or title of "delete entity" action.
     *
     * @access   protected
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterBreadcrumbsTitleDelete()
    {
        $this->addToTitle(__('Delete item'));
        $this->addBreadCrumb(__('Delete item'), Router::getCurrentUrl());
    }

    /**
     * This method generates actions in each row of the entity list. By default,
     * there are two actions: "edit" and "delete".
     *
     * @access   protected
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function alterListActions()
    {
        $sControllerParam = Router::getParam('controller');

        $this->addListOption(Route::backendUrl($sControllerParam, 'edit', '{id}'), __('edit'), 'pencil');
        $this->addListOption(Route::backendUrl($sControllerParam, 'delete', '{id}'), __('delete'), 'trash');
    }

}
