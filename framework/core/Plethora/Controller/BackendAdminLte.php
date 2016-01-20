<?php

namespace Plethora\Controller;

use Model\User;
use Plethora\Controller\Backend as PlethoraBackendController;
use Plethora\Route;
use Plethora\Theme;

/**
 * Master controller of backend side of page.
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @package          Controller
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class BackendAdminLte extends PlethoraBackendController
{

    protected $sViewBody = 'base/backend_adminlte/blocks/body';
    protected $sViewBodyHeader = 'base/backend_adminlte/blocks/body/header';
    protected $sViewBodyFooter = 'base/backend_adminlte/blocks/body/footer';
    protected $sViewBodyContent = 'base/backend_adminlte/blocks/body/content';
    protected $sViewBreadcrumbs = 'base/backend_adminlte/blocks/body/content/breadcrumbs';

    /**
     * Constructor.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct()
    {
        # initialize theme
        Theme::initBackend();

        # call parent
        parent::__construct();

        if($this->sModel !== NULL) {
            $this->setModel(new $this->sModel);
        }

        if(!User::isLogged() || !\UserPermissions::hasPerm(static::PERM_ADMIN_ACCESS)) {
            Route::factory('home')->redirectTo();
        }

        // set body classes
        $this->addBodyClass('skin-red');

        // add main breadcrumbs and title
        $this->alterBreadcrumbsTitleMain();

        // reset JavaScripts and CSS
        $this->resetCss();
        $this->resetJs();

        // add CSS and JavaScript files
        $this->addCss('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700&subset=latin,latin-ext');
        $this->addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
        $this->addCss('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');

        $this->addCssByTheme('/bootstrap/css/bootstrap.min.css');
        $this->addCssByTheme('/css/backend.css');

        $this->addJsByTheme('/plugins/jQuery/jQuery-2.1.4.min.js');
        $this->addJsByTheme('/plugins/jQueryUI/jquery-ui.min.js');
        $this->addJsByTheme('/bootstrap/js/bootstrap.min.js');
        $this->addJsByTheme('/js/backend.js');
        $this->addJsByTheme('/js/jquery.mjs.nestedSortable.js');
        $this->addJsByTheme('/js/app.min.js');
        $this->addJsByTheme('/js/backend_after_theme_load.js');

        # add viewport
        $this->addMetaTagRegular(
            'viewport',
            'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'
        );

        // generate menu
        $menuView = $this->generateMenu();
        $this->oViewBody->bind('menu', $menuView);
    }
}
