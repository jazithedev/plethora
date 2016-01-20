<?php
/**
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php $sFlash = \Plethora\Session::get('flash') ?>

<?php if(!is_null($sFlash)): ?>
    <?php $aUnserializedFlash = unserialize($sFlash) ?>

    <div class="alert alert-<?= $aUnserializedFlash['type'] ?>">
        <p><?= $aUnserializedFlash['content'] ?></p>
    </div>
<?php endif; ?>