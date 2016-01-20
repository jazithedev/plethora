<?php
/**
 * View used in developers toolbar main View to list out all routes.
 * 
 * @author		Krzysztof Trzos
 * @package		dev_toolbar
 * @subpackage	views
 * @since		1.0.1-dev, 2015-06-09
 * @version		1.0.2-dev, 2015-06-10
 */
?>

<?php /* @var $aRoutesList array */ ?>

<?php
ksort($aRoutesList);
?>

<?php foreach($aRoutesList as $sRouteName => $oRoute): /* @var $oRoute \Plethora\Route */ ?>
	<?php
	$sClass			 = 'single_route';
	$sParameterTypes = implode(', ', $oRoute->getParameterTypes());
	$sDefaults		 = implode(', ', $oRoute->getDefaults());
	$sPermissions	 = implode(', ', $oRoute->getPermissions());

	if(\Plethora\Router::getCurrentRouteName() === $sRouteName) {
		$sClass.= ' current';
	}
	?>
	<div class="<?= $sClass ?>">
		<span class="route_name"><?= $sRouteName ?></span>
		<div class="additional_info">
			<div>
				<span class="name">Action: </span>
				<span class="value"><?= $oRoute->getAction() ?></span>
			</div>
			<div>
				<span class="name">Controller: </span>
				<span class="value"><?= $oRoute->getController() ?></span>
			</div>
			<div>
				<span class="name">Raw URL: </span>
				<span class="value"><?= $oRoute->getRawURL() ?></span>
			</div>
			<div>
				<span class="name">URL: </span>
				<span class="value"><?= $oRoute->getURL() ?></span>
			</div>
			<div>
				<span class="name">Parameter types: </span>
				<span class="value"><?= empty($sParameterTypes) ? '-' : $sParameterTypes ?></span>
			</div>
			<div>
				<span class="name">Defaults: </span>
				<span class="value"><?= empty($sDefaults) ? '-' : $sDefaults ?></span>
			</div>
			<div>
				<span class="name">Permissions: </span>
				<span class="value"><?= empty($sPermissions) ? '-' : $sPermissions ?></span>
			</div>
		</div>
	</div>
<?php endforeach; ?>