<?php
/**
 * View used as content of customs section in Plethora developer toolbar.
 * 
 * @author		Krzysztof Trzos
 * @package		dev_toolbar
 * @subpackage	views\toolbar
 * @since		1.0.3-dev, 2015-06-11
 * @version		1.0.3-dev, 2015-06-11
 */
?>

<?php /* @var $aCustoms array */ ?>

<?php if(count($aCustoms) > 0): ?>
	<ul>
		<?php foreach($aCustoms as $sCustomName => $aCustomData): ?>
			<li>
				<p><span class="custom_var_name"><?= $sCustomName ?></span> (from <span class="custom_var_location"><?= $aCustomData['line'] ?></span>)</p>
				<div><?php \Kint::dump($aCustomData['data']) ?></div>
			</li>
		<?php endforeach ?>
	</ul>
<?php else: ?>
	<p><?= __('No customs were defined.') ?></p>
<?php endif ?>
