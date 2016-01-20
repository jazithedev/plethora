<?php
/**
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views\view\imitation
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $sURL string */ ?>
<?php /* @var $sTitle string */ ?>
<?php /* @var $sValue string */ ?>

<?php $oAttributes = \Plethora\Helper\Attributes::factory() ?>
<?php $oAttributes->setAttribute('title', $sTitle) ?>

<?php echo \Plethora\Helper\Html::a($sURL, empty($sValue) ? __('read more') : $sValue, $oAttributes) ?>