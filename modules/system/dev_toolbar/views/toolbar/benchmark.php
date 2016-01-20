<?php
/**
 * View used as content of benchmarks section in Plethora developer toolbar.
 * 
 * @author		Krzysztof Trzos
 * @package		dev_toolbar
 * @subpackage	views\toolbar
 * @since		1.0.4-dev, 2015-06-15
 * @version		1.0.4-dev, 2015-06-15
 */
?>

<?php /* @var $aBenchmarkMarks array */ ?>

<?php
$sPreviousName = '';
?>

<table>
	<thead>
		<tr>
			<th>name</th>
			<th>time</th>
			<th>difference</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($aBenchmarkMarks as $sName => $sData): ?>
		<?php list($p1m, $p1s) = explode(' ', $sData) ?>
			<tr>
				<td><?= $sName ?></td>
				<td><?= ((float)$p1m + (float)$p1s) ?></td>
				<td>
					<?php if($sPreviousName !== ''): ?>
						<span><?= \Plethora\Benchmark::elapsedTime($sPreviousName, $sName) ?></span>
					<?php else: ?>
						<span>0</span>
					<?php endif ?>
				</td>
			</tr>
			<?php $sPreviousName = $sName ?>
		<?php endforeach ?>
	</tbody>
</table>
