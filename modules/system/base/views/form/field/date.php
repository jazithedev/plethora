<?php
/**
 * @author		Krzysztof Trzos
 * @package		base
 * @subpackage	views/form/field
 * @since		1.0.0-alpha
 * @version		1.0.0-alpha
 */
?>

<?php /* @var $sLang string */ ?>
<?php /* @var $iValueNumber string */ ?>
<?php /* @var $oField \Plethora\Form\Field */ ?>
<?php /* @var $mValue mixed */ ?>

<?php if($oField->isDayHidden()): ?>
	<select <?php echo $oField->getAttributes()->renderAttributes(array('name' => '[day]', 'id' => '_day', 'class' => ' day')) ?>>
		<option value="">-</option>

		<?php for($i = 1; $i <= 31; ++$i): ?>
			<?php $sSel = (is_array($mValue) && $mValue['day'] == $i) ? ' selected="selected"' : '' ?>
			<option value="<?php echo $i ?>" <?php echo $sSel ?>><?php echo $i ?></option>
		<?php endfor ?>
	</select> <?php echo $oField->getSeparator() ?>
<?php endif ?>

<?php if($oField->isMonthHidden()): ?>
	<select <?php echo $oField->getAttributes()->renderAttributes(array('name' => '[month]', 'id' => '_month', 'class' => ' month')) ?>>
		<option value="">-</option>

		<?php foreach($oField->getMonthNames() as $j => $sMonth): ?>
			<?php
			$i		 = $j + 1;
			$sSel	 = (is_array($mValue) && $mValue['month'] == $i) ? ' selected="selected"' : '';
			?>
			<option value="<?php echo $i ?>" <?php echo $sSel ?>><?php echo ($oField->isMonthNamesHidden() ? $sMonth : $i) ?></option>
		<?php endforeach ?>
	</select> <?php echo $oField->getSeparator() ?>
<?php endif ?>

<?php if($oField->isYearHidden()): ?>
	<?php $aYearInterval = $oField->getYearInterval() ?>

	<select <?php echo $oField->getAttributes()->renderAttributes(array('name' => '[year]', 'id' => '_year', 'class' => ' year')) ?>>
		<option value="">-</option>
		<?php for($i = $aYearInterval[0]; $i <= $aYearInterval[1]; ++$i): ?>
			<?php $sSel = (is_array($mValue) && $mValue['year'] == $i) ? ' selected="selected"' : '' ?>
			<option value="<?php echo $i ?>" <?php echo $sSel ?>><?php echo $i ?></option>
		<?php endfor ?>
	</select>
<?php endif; ?>