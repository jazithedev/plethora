<?php
/**
 * View used as content of database section in Plethora developer toolbar.
 * 
 * @author		Krzysztof Trzos
 * @package		dev_toolbar
 * @subpackage	views\toolbar
 * @since		1.0.2-dev, 2015-06-10
 * @version		1.0.7-dev, 2015-08-02
 */
?>

<?php /* @var $oSqlLogger \Doctrine\DBAL\Logging\DebugStack */ ?>

<ol>
	<?php foreach($oSqlLogger->queries as $aQuery): ?>
		<?php
		$sParams = '';
		
		if(is_array($aQuery['params'])) {
			foreach($aQuery['params'] as &$mParam) {
				if(is_object($mParam)) {
					$mParam = get_class($mParam).' class';
				} elseif(is_array($mParam)) {
					$mParam = '('.implode(', ', $mParam).')';
				}
			}
			
			$sParams = implode(', ', $aQuery['params']);
		} else {
			$sParams = $aQuery['params'];
		}
		
		$sTypes	 = is_array($aQuery['types']) ? implode(', ', $aQuery['types']) : '-';
		?>
		<li>
			<p class="sql"><?= $aQuery['sql'] ?></p>
			<p class="params"><span>Parameters:</span> <?= $sParams !== '' ? '['.$sParams.']' : '-' ?></p>
			<p class="types"><span>Types:</span> <?= $sTypes !== '' ? '['.$sTypes.']' : '-' ?></p>
			<p class="exetime"><span>Time:</span> <?= $aQuery['executionMS'] ?> ms</p>
		</li>
	<?php endforeach ?>
</ol>