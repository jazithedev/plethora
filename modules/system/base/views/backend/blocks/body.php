<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $oHeader \Plethora\View */ ?>
<?php /* @var $oContent \Plethora\View */ ?>
<?php /* @var $oFooter \Plethora\View */ ?>

<div id="wrap">
    <?php echo $oHeader->render() ?>
    <?php echo $oContent->render() ?>
</div>
<div id="footer">
    <?php echo $oFooter->render() ?>
</div>