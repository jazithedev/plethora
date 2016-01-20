<?php
/**
 * Main view for entity list.
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
    <div class="table-responsive">
        <table class="entities_list entities_list_<?php echo $sHtmlClass ?> table table-striped">
            <thead>
            <tr>
                <?php foreach($aFieldLabels as $sLabel): ?>
                    <th><?php echo $sLabel ?></th>
                <?php endforeach ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($aList as $oEntity): /* @var $oEntity \Plethora\View\ViewEntity */ ?>
                <?php echo \Plethora\View::factory($sViewPathSingleRow)->bind('oEntity', $oEntity)->render() ?>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div>
        <p><?php echo __('List is empty.') ?></p>
    </div>
<?php endif ?>

<?php echo $sSuffix ?>