<?php
/**
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views\view
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>
<?php /* @var $oModel \Plethora\ModelCore */ ?>
<?php /* @var $aFields array */ ?>
<?php /* @var $sHtmlClass string */ ?>
<?php /* @var $sPrefix string */ ?>
<?php /* @var $sSuffix string */ ?>
<?php /* @var $sProfile string */ ?>

<?= $sPrefix ?>

    <div class="entity entity_<?= $sHtmlClass ?> entity_profile_<?= $sProfile ?> entity_id_<?= $oModel->getId() ?>">
        <?php foreach($aFields as $oField): /* @var $oField \Plethora\View\ViewField */ ?>
            <?php if($oField->getValue() !== NULL): ?>
                <?= $oField->getView()->render() ?>
            <?php endif ?>
        <?php endforeach ?>
    </div>

<?= $sSuffix; ?>