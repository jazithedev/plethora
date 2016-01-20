<?php
/**
 * This View is used to generate the whole content of the page body.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views\blocks\body
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */

use Plethora\Controller;
use Plethora\Router;
use Plethora\View;

?>

<?php /* @var $oBreadcrumbs View */ ?>
<?php /* @var $oSystemMessages View */ ?>
<?php /* @var $oContent View */ ?>
<?php /* @var $oController Controller */ ?>

<div class="main_content container">
    <?php echo $oBreadcrumbs->render() ?>
    <?php echo Router\LocalActions::generateActions()->render() ?>
    <?php echo $oSystemMessages->render() ?>
    <?php echo View::factory('base/flash')->render() ?>

    <?php if($oController->getTitle() != ''): ?>
        <h1><?php echo $oController->getTitleForH1() ?></h1>
    <?php endif ?>

    <?php echo $oContent->render() ?>
</div>