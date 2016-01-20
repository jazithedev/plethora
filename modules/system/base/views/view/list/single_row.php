<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $oEntity \Plethora\View\ViewEntity */ ?>

<tr class="single_row">
    <?php echo $oEntity->getView()->render() ?>
</tr>