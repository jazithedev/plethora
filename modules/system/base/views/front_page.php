<?php
/**
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */

use Plethora\Helper;
use Plethora\Route;

$loginLink = Helper\Link::factory();
$loginLink->getAttributes()->addToAttribute('class', 'btn btn-primary btn-lg');
?>

<div class="jumbotron">
    <p style="font-size: 70px;"><?= __('Hello!') ?></p>
    <p><?= __('Welcome on your new website!') ?></p>
    <?php if(\Model\User::isLogged()): ?>
        <p><?= __('You are logged! Go and see whats in your management panel ;).') ?></p>
        <p>
            <?php
            $backend = Helper\Link::factory();
            $backend->getAttributes()->addToAttribute('class', 'btn btn-danger btn-lg');
            echo $backend->code(__('go to management panel'), Route::factory('backend')->url());
            ?>
            <?= $loginLink->code(__('logout'), Route::factory('logout')->url()) ?>
        </p>
    <?php else: ?>
        <p><?= __('Click the button below to login and start managing. Enjoy!'); ?></p>
        <p><?= $loginLink->code(__('login page'), Route::factory('login')->url()) ?></p>
    <?php endif ?>
</div>