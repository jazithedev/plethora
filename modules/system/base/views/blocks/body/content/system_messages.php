<?php
/**
 * @author   Krzysztof Trzos
 * @package  base
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
?>

<?php /* @var $aSystemMessages array */ ?>

<div class="system_messages">
    <?php foreach($aSystemMessages as $aMessage): ?>
        <?php list($sString, $sType) = $aMessage ?>
        <div class="alert alert-<?php echo $sType ?>">
            <?php echo $sString ?>
        </div>
    <?php endforeach ?>
</div>