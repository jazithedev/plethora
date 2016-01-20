<?php
/**
 * View used for showing default subpage of the password recovery functionality.
 * 
 * @author		Krzysztof Trzos
 * @package		user
 * @subpackage	views/recovery
 * @since		1.0.3-dev, 2015-03-04
 * @version		2.1.0-dev
 */
?>

<?php /* @var $oForm \Plethora\Form */ ?>

<p><?= __('Enter Your e-mail address to recover access to Your account.') ?></p>
<div>
	<?php
	echo \Plethora\View::factory('base/form')
		->bind('oForm', $oForm)
		->render()
	?>
</div>