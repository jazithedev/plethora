<?php
/**
 * @package    base
 * @author     Krzysztof Trzos
 * @version    1.0.0-alpha
 * @since      1.0.0-alpha
 */
?>

<?php /* @var $oField \Plethora\View\ViewField */ ?>

<td class="entity_field entity_field_<?php echo $oField->getTypeOfModel() ?> entity_field_name_<?php echo $oField->getName() ?>"><?php echo $oField->getValue() ?></td>