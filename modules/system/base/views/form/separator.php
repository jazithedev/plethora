<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $oSeparator \Plethora\Form\Separator */ ?>
<?php /* @var $sContent string */ ?>

<tr>
    <?php if($oSeparator->getLabel() !== NULL): ?>
        <th><?php echo $oSeparator->renderLabel() ?></th>
    <?php endif ?>
    <td>
        <?php echo $sContent ?>
    </td>
</tr>