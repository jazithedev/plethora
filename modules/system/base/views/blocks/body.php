<?php
/**
 * Main View which generates all content of the BODY tag.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $oHeader \Plethora\View */ ?>
<?php /* @var $oContent \Plethora\View */ ?>
<?php /* @var $oFooter \Plethora\View */ ?>
<?php /* @var $sTitle string */ ?>

<div class="wrapper">
    <?php echo $oHeader->render() ?>
    <?php echo $oContent->render() ?>
    <?php echo $oFooter->render() ?>
</div>