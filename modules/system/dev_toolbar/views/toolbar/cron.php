<?php
/**
 * View used as content of Cron jobs section in Plethora developer toolbar.
 *
 * @author         Krzysztof Trzos
 * @package        dev_toolbar
 * @subpackage     views\toolbar
 * @since          1.1.0-dev
 * @version        1.1.0-dev
 */
?>

<?php /* @var $cronJobs array */ ?>

<table>
	<thead>
	<tr>
		<th>ID</th>
		<th>time</th>
		<th>type</th>
		<th>data</th>
		<th>module</th>
		<th>next activation</th>
		<th>last execution time</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($cronJobs as $job): ?>
		<?php list($time, $type, $data, $jobKey, $module, $lastAction, $lastExec) = array_values($job) ?>
		<tr>
			<td><?= $jobKey ?></td>
			<td><?= $time ?></td>
			<td><?= $type ?></td>
			<td><?= $data ?></td>
			<td><?= $module ?></td>
			<td><?= $lastAction === NULL ? '- none -' : date('Y-m-d H:i:s', $lastAction) ?></td>
			<td><?= $lastExec === NULL ? '- none -' : date('Y-m-d H:i:s', $lastExec) ?></td>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>
