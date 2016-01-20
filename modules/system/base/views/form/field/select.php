<?php
/**
 * Main View field of "select" form field.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views\form\field
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $sLang string */ ?>
<?php /* @var $iValueNumber string */ ?>
<?php /* @var $oField \Plethora\Form\Field\Select */ ?>
<?php /* @var $mValue mixed */ ?>
<?php

if(is_bool($mValue)) {
    $mValue = $mValue ? 1 : 0;
}

?>

<select <?php echo $oField->getAttributes()->renderAttributes() ?>>
    <option value=""><?= $oField->getFirstOption() ?></option>

    <?php foreach($oField->getOptions() as $sValue => $sLabel): ?>
        <?php $sSel = strcmp($mValue, $sValue) == 0 ? 'selected="selected"' : '' ?>

        <option value="<?= $sValue ?>" <?php echo $sSel ?>><?php echo $sLabel ?></option>
    <?php endforeach ?>
</select>