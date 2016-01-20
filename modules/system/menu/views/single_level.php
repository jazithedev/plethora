<?php
/**
 * This view is used to generate one level of the menu.
 *
 * @author      Krzysztof Trzos
 * @package     menu
 * @subpackage  views
 * @since       1.0.0-dev
 * @version     1.3.0-dev
 */

use Plethora\Helper\Html;
use Plethora\View;

?>

<?php /* @var $route array */ ?>
<?php /* @var $routeTitle string */ ?>
<?php /* @var $routeName string */ ?>
<?php /* @var $path string */ ?>
<?php /* @var $routeParams array */ ?>
<?php /* @var $children View */ ?>
<?php /* @var $classes View */ ?>

<?php
$prefix      = \Plethora\Helper\Arrays::get($route, 'prefix', '');
$suffix      = \Plethora\Helper\Arrays::get($route, 'suffix', '');
$innerPrefix = \Plethora\Helper\Arrays::get($route, 'inner_prefix', '');
$innerSuffix = \Plethora\Helper\Arrays::get($route, 'inner_suffix', '');
?>

<?= $prefix ?>
    <li class="<?= implode(' ', $classes) ?>">
        <?php if($path !== NULL): ?>
            <?= Html::a($path, $innerPrefix.'<span>'.$routeTitle.'</span>'.$innerSuffix) ?>
        <?php else: ?>
            <?= $innerPrefix.'<span>'.$routeTitle.'</span>'.$innerSuffix ?>
        <?php endif ?>
        <?php if($children instanceof View): ?>
            <?= $children->render() ?>
        <?php endif ?>
    </li>
<?= $suffix ?>