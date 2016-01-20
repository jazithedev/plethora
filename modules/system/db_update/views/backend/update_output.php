<?php
/**
 * This view generates information output after database updating.
 * 
 * @author		Krzysztof Trzos
 * @package		db_update
 * @subpackage	views\backend
 * @since		1.2.0-dev
 * @version		1.2.0-dev
 */
?>

<?php /* @var $aSQL array */ ?>

<p><?= __('Queries done (in amount of :amount):', ['amount' => count($aSQL)]) ?></p>
<ol>
	<?php foreach($aSQL as $sSQL): /* @var $sSQL string */ ?>
		<li><?= $sSQL ?></li>
	<?php endforeach ?>
</ol>