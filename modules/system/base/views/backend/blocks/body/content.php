<?php
/**
 * View with the main content of a page.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $oBreadcrumbs array */ ?>
<?php /* @var $oSystemMessages \Plethora\View */ ?>
<?php /* @var $oContent \Plethora\View */ ?>
<?php /* @var $oController \Plethora\Controller */ ?>

<div class='container'>
    <?php echo $oBreadcrumbs->render() ?>
    <?php echo \Plethora\Router\LocalActions::generateActions()->render() ?>
    <?php echo $oSystemMessages->render() ?>
    <?php echo \Plethora\View::factory('base/flash')->render() ?>
    <?php if($oController->getTitle() != ''): ?>
        <h1><?php echo $oController->getTitleForH1() ?></h1>
    <?php endif ?>
    <?php echo $oContent->render() ?>
</div>