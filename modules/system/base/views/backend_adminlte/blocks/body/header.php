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

?>

<?php
# get logged user
$user = Model\User::getLoggedUser();

# register date
$registerDate = $user->getRegisterDate()->format('Y-m-d');

# avatar
$userImage = $user->getImageStyled();

# logout anchor
$logoutHelper = \Plethora\Helper\Link::factory();
$logoutHelper->getAttributes()
    ->addToAttribute('class', 'btn btn-default btn-flat');
$logout = $logoutHelper->code(__('Logout'), Route::factory('logout')->url());
?>

<a href="<?= Route::backendUrl(); ?>" class="logo">
    <span class="logo-mini"><b>Pl</b></span>
    <span class="logo-lg"><b>Plethora</b></span>
</a>

<nav class="navbar navbar-static-top" role="navigation">
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only"><?= __('Toggle navigation') ?></span>
    </a>

    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<?= $userImage ?>" class="user-image" alt="<?= __('User Image') ?>">
                    <span class="hidden-xs"><?= $user->getFullName() ?></span>
                </a>
                <ul class="dropdown-menu">
                    <li class="user-header">
                        <img src="<?= $userImage ?>" class="img-circle" alt="<?= __('User Image') ?>">
                        <p>
                            <?= $user->getFullName() ?>
                            <small><?= __('Member since :date', ['date' => $registerDate]) ?></small>
                        </p>
                    </li>
                    <li class="user-footer">
                        <div class="pull-left">
                            <a
                                href="<?= $user->getProfileURL() ?>"
                                class="btn btn-default btn-flat"
                            ><?= __('Profile') ?></a>
                        </div>
                        <div class="pull-right"><?= $logout ?></div>
                    </li>
                </ul>
            </li><?php /*
            <li>
                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
            </li>*/ ?>
        </ul>
    </div>
</nav>