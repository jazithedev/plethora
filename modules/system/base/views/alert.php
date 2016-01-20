<?php
/**
 * Main view used to generate errors.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $sType string */ ?>
<?php /* @var $sMsg string */ ?>

<div class="alert alert-<?= $sType ?>">
    <p><b><?= __(ucfirst($sType)) ?>!</b> <?= $sMsg ?></p>
</div>