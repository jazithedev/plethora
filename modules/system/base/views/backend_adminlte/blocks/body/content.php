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

<div class="content-wrapper">
    <section class="content-header">
        <?php if($oController->getTitle() != ''): ?>
            <h1>
                <span><?= $oController->getTitleForH1() ?></span>
                <?php /*<small>Control panel</small>*/ ?>
            </h1>
        <?php endif ?>
        <?php echo $oBreadcrumbs->render() ?>
    </section>

    <section class="content body">
        <?php echo \Plethora\Router\LocalActions::generateActions()->render() ?>
        <?php echo $oSystemMessages->render() ?>
        <?php echo \Plethora\View::factory('base/flash')->render() ?>

        <?php echo $oContent->render() ?>
    </section>
</div>