<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $oField \Plethora\Form\Field\Checkbox */ ?>

<?php $iNoOfCol = 1 ?>
<?php $iNoInRow = 1 ?>

<?php $oField->addToAttribute('class', 'form-control input-sm') ?>

<?php foreach($oField->getOptions() as $sKey => $aOption): ?>
    <?php $sSel = (is_array($oField->getValue()) && in_array($aOption['value'], $oField->getValue()) || $oField->getValue() == $aOption['value']) ? 'checked' : '' ?>
    <?php $sAttrs = $oField->parseAttributes(['name' => '['.$sKey.']']) ?>

    <span>
		<input <?php echo $sAttrs ?> value="<?php echo $aOption['value'] ?>" <?php echo $sSel ?> /> <?php echo $aOption['label'] ?>
	</span>

    <?php
    if($iNoOfCol == $oField->getColumnsAmount()) {
        $iNoOfCol = 1;
        $iNoInRow++;
    } else {
        $iNoOfCol++;
    }
    ?>
<?php endforeach ?>