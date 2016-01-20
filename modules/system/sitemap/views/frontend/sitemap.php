<?php
/**
 * Main view for site map.
 * 
 * @author		Krzysztof Trzos
 * @package		sitemap
 * @subpackage	views
 * @since		1.0.0-dev, 2015-04-19
 * @version		1.0.0-dev, 2015-04-19
 */
?>

<?php /* @var $aItems array */ ?>

<div id="sitemap">
	<ul>
		<?php foreach($aItems as $aItem): ?>
			<li><?php echo \Plethora\Helper\Html::a($aItem[0], $aItem[1]) ?></li>
		<?php endforeach ?>
	</ul>
</div>