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
<?php /* @var $oField \Plethora\Form\Field */ ?>
<?php /* @var $mValue mixed */ ?>

<?php $oField->getAttributes()->setAttribute('value', $mValue) ?>

<input <?php echo $oField->getAttributes()->renderAttributes() ?>>