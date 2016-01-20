<?php
/**
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views\view
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $sLabel string */ ?>
<?php /* @var $oContent \Plethora\View */ ?>
<?php /* @var $sClass string */ ?>

<div class="entity_field entity_field_imitation entity_field_imitation_<?= $sClass ?>">
    <?php if($sLabel !== NULL): ?>
        <div class="entity_field_label"><?= $sLabel ?>:</div>
    <?php endif ?>
    <div class="entity_field_content"><?= $oContent->render() ?></div>
</div>