<?php
/**
 * Main View for local actions.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $aActions array */ ?>

<div id="local_actions_container" class="clearfix">
    <p class="pull-right">
        <?php foreach($aActions as $aAction): ?>
            <a class="btn btn-primary" href="<?= $aAction['url'] ?>" title="<?= $aAction['title'] ?>">
                <span class="glyphicon glyphicon-<?= $aAction['icon'] ?>"></span> <?= $aAction['title'] ?>
            </a>
        <?php endforeach ?>
    </p>
</div>