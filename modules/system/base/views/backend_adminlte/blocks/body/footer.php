<?php
/**
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views/backend/blocks/body
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */

use Plethora\Core;
use Plethora\Helper;
use Plethora\Route;

?>

<?php
$sChangelogLink  = Route::factory('framework_changelog')->url();
$changelogAnchor = Helper\Link::factory()
    ->setTitle(__('changelog'))
    ->code(Core::getVersion(), $sChangelogLink);
?>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b><?= __('Version') ?></b> <?= $changelogAnchor ?>
    </div>
    <strong>Copyright &copy; 2016 <a href="http://plethorafw.com">Plethora Framework</a>.</strong> <?= __('All rights reserved.') ?>
</footer>