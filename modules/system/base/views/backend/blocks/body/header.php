<?php
/**
 * Header of backend theme.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     view
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */

use Plethora\Config;
use Plethora\Exception;
use Plethora\Route;
use Plethora\Router;
use Plethora\Theme;

?>

<?php
# load main menus
$menus = Config::get('backend.menu');

# load submenus
$subMenus = [];

foreach(array_keys(Router::getModules()) as $sModule) {
    try {
        $aModuleMenus = Config::get($sModule.'.backend.menu', [], TRUE);

        foreach($aModuleMenus as $sName => $aModuleMenu) {
            $subMenus[$aModuleMenu['parent']][$sModule][$sName] = $aModuleMenu;
        }
    } catch(Exception $e) {

    }
}

# get logged user
$oUser = Model\User::getLoggedUser();

# user anchor
$userAnchor = \Plethora\Helper\Link::factory()
    ->setTitle(__('User profile'))
    ->code($oUser->getFullName(), $oUser->getProfileURL());
?>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Menu</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand navbar-brand-logo"
               href="<?= Route::factory('home')->url() ?>"
               title="<?= __('Main page') ?>">
                <img
                    src="<?= Router::getBase().'/'.Theme::getThemePath() ?>/images/navbar_logo.png"
                    alt="<?= \Plethora\Core::getAppName() ?>" />
            </a>
            <a class="navbar-brand"
               href="<?= Route::factory('home')->url() ?>"
               title="<?= __('Main page') ?>"><?= \Plethora\Core::getAppName() ?></a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <?php foreach($menus as $sMenu): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle"
                           data-toggle="dropdown"><?= __('backend.mainmenu.'.$sMenu) ?>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if(isset($subMenus[$sMenu])): ?>
                                <?php foreach($subMenus[$sMenu] as $sModule => $aOptionsGroup): ?>
                                    <?php $aFirstOption = reset($aOptionsGroup); ?>
                                    <li class="dropdown-submenu">
                                        <a href="<?= $aFirstOption['url'] ?>"><?= __('module.'.$sModule) ?></a>
                                        <?php if(count($aOptionsGroup) > 1): ?>
                                            <ul class="dropdown-menu">
                                                <?php foreach($aOptionsGroup as $sName => $aOption): ?>
                                                    <?php $submenuName = __('backend.submenu.'.$sMenu.'.'.$sName) ?>
                                                    <li>
                                                        <a
                                                            href="<?= $aOption['url'] ?>"
                                                            title="<?= $submenuName ?>"><?= $submenuName ?></a>
                                                    </li>
                                                <?php endforeach ?>
                                            </ul>
                                        <?php endif ?>
                                    </li>
                                <?php endforeach ?>
                            <?php endif ?>
                        </ul>
                    </li>
                <?php endforeach ?>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li>
                    <p class="navbar-text"><?= __('Hello :user', ['user' => $userAnchor]) ?></p>
                </li>

                <?php if($oUser instanceof Model\User): ?>
                    <li>
                        <a
                            href="<?= Route::factory('logout')->url() ?>"
                            title="<?= __('Logout') ?>"><?= __('Logout') ?></a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</nav>