<?php
/**
 * Main View used to render inputs of Checkbox type.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views/form/field
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $sLang string */ ?>
<?php /* @var $iValueNumber string */ ?>
<?php /* @var $oField \Plethora\Form\Field\Checkbox */ ?>
<?php /* @var $mValue mixed */ ?>

<?php
$iWidthPerCol = round(100 / $oField->getColumnsAmount());
$iNoOfCol     = 1;
$iNoInRow     = 1;
?>

<?php if($oField->getOptions() > 1): ?>
    <div id="check_uncheck_all_<?= $oField->getName() ?>" class="check_uncheck_all">
        <p>
            <span class="check"><?= __('Check') ?></span> /
            <span class="uncheck"><?= __('Uncheck') ?></span> <?= __('all options') ?>.
        </p>
    </div>
<?php endif ?>

<div class="options_list">
    <?php foreach($oField->getOptions() as $sKey => $aOption): ?>
        <?php
        $sSel   = (is_array($mValue) && in_array($aOption['value'], $mValue) || $mValue == $aOption['value']) ? 'checked' : '';
        $sAttrs = $oField->getAttributes()->renderAttributes(['name' => '['.$sKey.']', 'id' => '_'.$sKey], TRUE, FALSE);
        ?>

        <div<?php if($oField->getColumnsAmount() > 1): ?> style="width: <?= $iWidthPerCol ?>%; float: left;"<?php endif ?>>
            <input <?= $sAttrs ?> value="<?= $aOption['value'] ?>" <?= $sSel ?> />
            <label for="<?= $oField->getAttributes()->getAttribute('id').'_'.$sKey ?>"><?= $aOption['label'] ?></label>
        </div>

        <?php
        if($iNoOfCol == $oField->getColumnsAmount()) {
            $iNoOfCol = 1;
            $iNoInRow++;
        } else {
            $iNoOfCol++;
        }
        ?>
    <?php endforeach ?>
</div>

<script type="text/javascript">
    $(function() {
        $('div#check_uncheck_all_<?= $oField->getName() ?> span.check').click(function() {
            $(this).closest('div.form-group').find('input[type=checkbox]').prop('checked', true);
        });
        $('div#check_uncheck_all_<?= $oField->getName() ?> span.uncheck').click(function() {
            $(this).closest('div.form-group').find('input[type=checkbox]').prop('checked', false);
        });
    });
</script>