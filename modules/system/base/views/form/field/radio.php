<?php
/**
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views\form\field
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $sLang string */ ?>
<?php /* @var $iValueNumber string */ ?>
<?php /* @var $oField \Plethora\Form\Field\Radio */ ?>
<?php /* @var $mValue mixed */ ?>

<table>
    <?php $i = 1 ?>
    <?php foreach($oField->getOptions() as $sValue => $sLabel): ?>
        <?php $sSel = $mValue == $sValue ? ' checked' : '' ?>
        <?php if($i == 1): ?>
            <tr>
        <?php endif ?>

        <td>
            <input type="radio" <?php echo $oField->getAttributes()->renderAttributes() ?> value="<?php echo $sValue ?>" <?php echo $sSel ?> />
        </td>
        <td class="label"><?php echo $sLabel ?></td>

        <?php if($i == $oField->getColumnsAmount()): ?>
            <?php $i = 1 ?>
            </tr>
        <?php else: ?>
            <?php $i++ ?>
        <?php endif ?>
    <?php endforeach ?>
    <?php if($i > 1): ?>
	</tr>
<?php endif ?>
</table>