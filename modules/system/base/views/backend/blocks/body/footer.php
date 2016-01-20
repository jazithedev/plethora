<?php
/**
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views/backend/blocks/body
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php $sChangelogLink = \Plethora\Route::factory('framework_changelog')->url() ?>

<div class="container">
    <p class="text-muted text-right"><?= __('author') ?>: <b>Krzysztof Trzos</b> with
        <a href="<?= $sChangelogLink ?>" title="<?= __('changelog') ?>">Plethora v<?= \Plethora\Core::getVersion() ?></a></p>
</div>