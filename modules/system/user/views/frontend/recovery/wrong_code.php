<?php
/**
 * @author		Krzysztof Trzos
 * @package		user
 * @subpackage	views\frontend
 * @since		2.25.0, 2015-02-18
 * @version		1.0.0, 2015-02-18
 */
?>

<?php $sAnchor = \Plethora\Helper\Html::a(\Plethora\Route::factory('contact')->url(), __('contact')) ?>

<p class="act_txt"><?php echo __('An error occurred while recovering access to your account. The reason may be one of the following points') ?></p>
<ul class="func_list">
	<li><?php echo __('recovery operation has already been made under this link;') ?></li>
	<li><?php echo __('link from the e-mail was not fully copied into your browser\'s address field.') ?></li>
</ul>
<p class="act_txt"><?php echo __('If none of the above reasons does not solve the problem, please contact us via the :contact section.', array('contact' => $sAnchor)) ?></p>