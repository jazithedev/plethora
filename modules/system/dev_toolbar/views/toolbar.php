<?php
/**
 * Main developers toolbar View.
 * 
 * @author		Krzysztof Trzos
 * @package		dev_toolbar
 * @subpackage	views
 * @since		1.0.0-dev, 2015-06-08
 * @version		1.0.2-dev, 2015-06-10
 */
?>

<?php
/* @var $oSqlLogger \Doctrine\DBAL\Logging\DebugStack */
/* @var $aRoutesList array */
/* @var $aModules array */
/* @var $aCustoms array */
/* @var $aBenchmarkMarks array */
/* @var $cronJobs array */
?>

<?php
$sSqlQueriesTime = 0;

foreach($oSqlLogger->queries as $aQuery) {
	$sSqlQueriesTime+= $aQuery['executionMS'];
}

$arr = get_defined_vars();

// benchmark count
$sBenchmark = '';

if(count($aBenchmarkMarks) > 0) {
	$sBenchmark = ' ('.count($aBenchmarkMarks).')';
}
?>

<div id="dev_toolbar">
	<div class="dv-button"></div>
	<div class="dv-content">
		<div class="dv-section control_panel">
			<div class="dv-section-header">
				<a href="/a">
					<span class="image"></span>
				</a>
			</div>
		</div>
		<div class="dv-section framework_version">
			<div class="dv-section-header">
				<span class="text"><?= \Plethora\Core::FW_VERSION ?></span>
			</div>
		</div>
		<div class="dv-section php active">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text"><?= phpversion() ?></span>
			</div>
			<div class="dv-section-content">
				<?php
//				ob_start();
//				phpinfo();
//				$sInfo = ob_get_contents();
//				ob_end_clean();
//				echo preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $sInfo);
				?>
			</div>
		</div>
		<div class="dv-section benchmark <?php if($sBenchmark !== ''): ?>active<?php endif ?>">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text"><?= \Plethora\Benchmark::elapsedTime('start', 'end') ?> ms<?= $sBenchmark ?></span>
			</div>
			<?php if($sBenchmark !== ''): ?>
				<div class="dv-section-content"><?= \Plethora\View::factory('dev_toolbar/toolbar/benchmark')->bind('aBenchmarkMarks', $aBenchmarkMarks)->render() ?></div>
			<?php endif ?>
		</div>
		<div class="dv-section memory_usage">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text"><?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB</span>
			</div>
		</div>
		<div class="dv-section database_queries active">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text"><?= count($oSqlLogger->queries) ?> (<?= round($sSqlQueriesTime, 4) ?> ms)</span>
			</div>
			<div class="dv-section-content"><?= \Plethora\View::factory('dev_toolbar/toolbar/database')->bind('oSqlLogger', $oSqlLogger)->render() ?></div>
		</div>
		<div class="dv-section routing active">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text"><?= count($aRoutesList) ?></span>
			</div>
			<div class="dv-section-content"><?= \Plethora\View::factory('dev_toolbar/toolbar/routes')->bind('aRoutesList', $aRoutesList)->render() ?></div>
		</div>
		<div class="dv-section modules active">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text"><?= count($aModules) ?></span>
			</div>
			<div class="dv-section-content"><?= \Plethora\View::factory('dev_toolbar/toolbar/modules')->bind('aModules', $aModules)->render() ?></div>
		</div>
		<div class="dv-section variables active">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text">variables (<?= count(filter_input_array(INPUT_POST)) ?>/<?= count(filter_input_array(INPUT_GET)) ?>/<?= count($_FILES) ?>/<?= count(filter_input_array(INPUT_COOKIE)) ?>/<?= isset($_SESSION) ? count($_SESSION) : 0 ?>)</span>
			</div>
			<div class="dv-section-content"><?= \Plethora\View::factory('dev_toolbar/toolbar/variables')->render() ?></div>
		</div>
		<div class="dv-section customs active">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text">customs (<?= count($aCustoms) ?>)</span>
			</div>
			<div class="dv-section-content"><?= \Plethora\View::factory('dev_toolbar/toolbar/customs')->bind('aCustoms', $aCustoms)->render() ?></div>
		</div>
		<div class="dv-section files active">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text"><?= 'files (?)' ?></span>
			</div>
		</div>
		<div class="dv-section ajax active">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text"><?= 'ajax (?)' ?></span>
			</div>
		</div>
		<div class="dv-section cron active">
			<div class="dv-section-header">
				<span class="image"></span>
				<span class="text"><?= 'cron ('.count($cronJobs).')' ?></span>
			</div>
			<div class="dv-section-content"><?= \Plethora\View::factory('dev_toolbar/toolbar/cron')->bind('cronJobs', $cronJobs)->render() ?></div>
		</div>
		<div class="dv-section close_toolbar">
			<div class="dv-section-header">
				<a href="javascript: void(0);" title="<?= __('Close toolbar') ?>">
					<span class="image"></span>
				</a>
			</div>
		</div>
	</div>
</div>