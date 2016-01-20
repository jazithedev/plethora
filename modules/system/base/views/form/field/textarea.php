<?php
/**
 * Main View field of `textarea` form field.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views\form\field
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>
<?php
/* @var $sLang string */
/* @var $iValueNumber string */
/* @var $oField \Plethora\Form\Field\Textarea */
/* @var $mValue mixed */
?>

    <textarea rows="<?= $oField->getRows() ?>" cols="<?= $oField->getCols() ?>" <?= $oField->getAttributes()->renderAttributes() ?>><?= $mValue ?></textarea>

<?php if(!$oField->isCountingHidden()): ?>
    <p class='text_counting'></p><input type="hidden" value="<?= $oField->getMaxLength() ?>"/>
<?php endif; ?>