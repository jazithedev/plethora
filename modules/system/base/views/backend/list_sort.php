<?php
/**
 * Main view for backend "sort" actions.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $aList array */ ?>
<?php /* @var $oModel \Plethora\ModelCore */ ?>
<?php /* @var $sColumn string */ ?>


<?php if(!empty($aList)): ?>
    <div id="sorted_list">
        <?php
        echo \Plethora\View::factory('base/backend/list_sort_group')
            ->bind('aList', $aList)
            ->bind('oModel', $oModel)
            ->bind('sColumn', $sColumn)
            ->render()
        ?>
    </div>
<?php endif; ?>

<form action="/">
    <input type="hidden" name="model" value="<?php echo $oModel->getClass() ?>" id="model_name"/>
    <button class="btn btn-primary" id="sort_save_conf"><?php echo __('Save configuration') ?></button>
</form>