<?php
/**
 * Basic view file for a single row in a entity list.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views/view
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>
<?php /* @var $oEntity \Plethora\View\ViewEntity */ ?>

<div class="single_row">
    <?php echo $oEntity->getView()->render() ?>
</div>