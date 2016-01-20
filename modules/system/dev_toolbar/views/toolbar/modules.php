<?php
/**
 * View used as content of modules section in Plethora developer toolbar.
 * 
 * @author		Krzysztof Trzos
 * @package		dev_toolbar
 * @subpackage	views\toolbar
 * @since		1.0.3-dev, 2015-06-11
 * @version		1.0.3-dev, 2015-06-11
 */
?>

<?php /* @var $aModules array */ ?>

<?php
ksort($aModules);
?>

<ul>
	<?php foreach($aModules as $sModuleKey => $aModuleData): ?>
		<li>
			<?= $sModuleKey ?> (<?= $aModuleData['path'] ?>)
		</li>
	<?php endforeach ?>
</ul>