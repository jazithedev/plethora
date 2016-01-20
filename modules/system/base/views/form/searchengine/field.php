<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $oField \Plethora\Form\Field */ ?>
<?php /* @var $sContent string */ ?>

<?php
$attrs = $oField->getAttributes();
$for   = '';

if($attrs->getAttribute('id') != '') {
    $for = ' for="'.$attrs->getAttribute('id').'"';
}
?>

<div class="single_field single_field_<?php echo $oField->getType() ?> form-group">
    <?php if($oField->isLabelVisible()): ?>
        <th>
            <label<?= $for ?>><?= $oField->getLabel() ?></label>
            <?php if($oField->getTip() != ''): ?>
                <a href="/" title="" class="form_tip">
                    <img src="/images/icons/info.png" alt="Informacja"/>
                    <span class="form_tip_content"><?= $oField->getTip() ?></span>
                </a>
            <?php endif ?>
        </th>
    <?php endif ?>

    <td class="<?= $oField->getFieldType() ?>"><?= $sContent ?></td>
</div>