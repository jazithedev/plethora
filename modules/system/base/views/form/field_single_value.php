<?php
/**
 * @author		Krzysztof Trzos
 * @package		base
 * @subpackage	views
 * @since		1.0.0-alpha
 * @version		1.0.0-alpha
 */
?>

<?php /* @var $sLang string */ ?>
<?php /* @var $sOneValueNumber integer */ ?>
<?php /* @var $sOneValueContent string */ ?>
<?php /* @var $oField \Plethora\Form\Field */ ?>

<div class="form-field-value form-field-value-no-<?= $sOneValueNumber ?><?php if($oField->hasErrorsParticular($sLang, $sOneValueNumber)): ?> has-error<?php endif ?>">
	<?php if($oField->getQuantity() !== 1): ?>
		<div class="form-field-delete">
			<button class="btn btn-danger btn-sm" type="button"><?php echo __('delete') ?></button>
		</div>
	<?php endif ?>
	<div class="form-field-content"><?= $sOneValueContent ?></div>
</div>