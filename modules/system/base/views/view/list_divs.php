<?php
/**
 * View file for entity list which is based on DIV's structure.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views/view
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $aList array */ ?>
<?php /* @var $sHtmlClass string */ ?>
<?php /* @var $sViewPathSingleRow string */ ?>
<?php /* @var $aFieldLabels array */ ?>
<?php /* @var $oFirstEntity \Plethora\View\ViewEntity */ ?>
<?php /* @var $sPrefix string */ ?>
<?php /* @var $sSuffix string */ ?>

<?php echo $sPrefix ?>

<?php if(!empty($aList)): ?>
    <div class="entities_list entities_list_<?php echo $sHtmlClass ?> table table-striped">
        <?php foreach($aList as $oEntity): /* @var $oEntity \Plethora\View\ViewEntity */ ?>
            <?php echo \Plethora\View::factory($sViewPathSingleRow)->bind('oEntity', $oEntity)->render() ?>
        <?php endforeach ?>
    </div>
<?php else: ?>
    <div>
        <p><?php echo __('List is empty.') ?></p>
    </div>
<?php endif ?>

<?php echo $sSuffix ?>