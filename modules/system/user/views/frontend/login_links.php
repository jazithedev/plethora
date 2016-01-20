<?php
/**
 * @author     Krzysztof Trzos <k.trzos@jazi.pl>
 * @package    user
 * @subpackage views
 * @since      2.1.0-dev
 * @version    2.1.0-dev
 */
?>

<div class="login_links">
	<p><?= \Plethora\Helper\Html::a(\Plethora\Route::factory('password_recovery')->url(), __('I forgot password')) ?></p>
	<p><?= \Plethora\Helper\Html::a(\Plethora\Route::factory('register')->url(), __('I want to register an account')) ?></p>
</div>
