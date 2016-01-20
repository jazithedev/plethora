<?php
/**
 * View used as content of variables section in Plethora developer toolbar.
 * 
 * @author		Krzysztof Trzos
 * @package		dev_toolbar
 * @subpackage	views\toolbar
 * @since		1.0.3-dev, 2015-06-11
 * @version		1.0.3-dev, 2015-06-11
 */
?>

<?php /* @var $aModules array */ ?>

<?php foreach($GLOBALS as $sModuleKey => $aModuleData): ?>
	<?php
	if(!in_array($sModuleKey, ['_GET', '_POST', '_COOKIE', '_SESSION', '_FILES', '_ENV', '_REQUEST', '_SERVER'])) {
		continue;
	}
	?>

	<div>
		<p class="var_name"><?= $sModuleKey ?></p>
		<?php if(count($aModuleData) > 0): ?>
			<table>
				<thead>
					<tr>
						<th>key</th>
						<th>value</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($aModuleData as $sVarName => $mVarValue): ?>
						<tr>
							<td><div><?= $sVarName ?></div></td>
							<td>
								<pre><?php empty($mVarValue) ? '-' : var_dump($mVarValue) ?></pre>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		<?php else: ?>
			<p class="empty"><?= __('This variable is empty.') ?></p>
		<?php endif ?>
	</div>
<?php endforeach ?>