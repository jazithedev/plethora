<?php
/**
 * Main widget view (used as container).
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $sClasses string */ ?>
<?php /* @var $oContent \Plethora\View */ ?>

<?php ?>

<div class="widget <?php echo $sClasses ?>">
    <?php echo $oContent->render() ?>
</div>