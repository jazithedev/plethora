<?php
/**
 * Main view for entities fields.
 *
 * @package        base
 * @subpackage     views/view
 * @author         Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>
<?php /* @var $oField \Plethora\View\ViewField */ ?>
<?php /* @var $sPrefix string */ ?>
<?php /* @var $sSuffix string */ ?>

<?= $sPrefix ?>

    <div class="entity_field entity_field_<?= $oField->getTypeOfModel() ?> entity_field_name_<?= $oField->getName() ?>">
        <?php if($oField->isLabelVisible()): ?>
            <div class="entity_field_label"><?= $oField->getLabel() ?></div>
        <?php endif ?>
        <div class="entity_field_content"><?= $oField->getValue() ?></div>
    </div>

<?= $sSuffix; ?>