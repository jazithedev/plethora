<?php
/**
 * This view generates a one level of a sorting list.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */

use Plethora\Route;
use Plethora\Router;

?>

<?php /* @var $aList array */ ?>
<?php /* @var $oModel \Plethora\ModelCore */ ?>
<?php /* @var $sColumn string */ ?>

<?php
$controller = Router::getParam('controller');
?>

<?php if(!empty($aList)): ?>
    <ol>
        <?php foreach($aList as $data): ?>
            <li id="object_<?= $data['object']->id ?>">
                <div class="content">
                    <span class="move glyphicon glyphicon-move"></span>
                    <a class="glyphicon glyphicon-pencil" href="<?= Route::backendUrl($controller, 'edit', $data['object']->id) ?>" title="<?= __('edit') ?>"></a>
                    <a class="glyphicon glyphicon-trash" href="<?= Route::backendUrl($controller, 'delete', $data['object']->id) ?>" title="<?= __('delete') ?>"></a>
                    <span><?= $data['object']->{$sColumn} ?></span>
                </div>
                <?php
                echo \Plethora\View::factory('base/backend/list_sort_group')
                    ->bind('aList', $data['siblings'])
                    ->bind('oModel', $oModel)
                    ->bind('sColumn', $sColumn)
                    ->render()
                ?>
            </li>
        <?php endforeach ?>
    </ol>
<?php endif; ?>