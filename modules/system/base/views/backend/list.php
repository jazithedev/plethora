<?php

use Plethora\ModelCore;
use Plethora\Helper;
use Plethora\View;

/**
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views/list
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $list array */ ?>
<?php /* @var $listColumns array */ ?>
<?php /* @var $pager Helper\Pager */ ?>
<?php /* @var $search Helper\SearchEngine */ ?>
<?php /* @var $model \Plethora\ModelCore */ ?>
<?php /* @var $options array */ ?>

<?php echo $search->render() ?>

<?php if(!empty($list)): ?>
    <div class="box">
        <div class="box-header with-border">
            <?= View::factory('base/list/results_amount')->render() ?>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="backend_list table table-striped table-hover table-condensed">
                <thead>
                <tr>
                    <th></th>
                    <?php foreach($listColumns as $column): ?>
                        <th><?php
                            if($model->hasField($column)) {
                                echo $model->getConfig()->getField($column)->getLabel();
                            } elseif($model->hasLocales() && $model->getLocales()->hasField($column)) {
                                echo $model->getLocales()->getConfig()->getField($column)->getLabel();
                            }
                            ?></th>
                    <?php endforeach ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($list as $object): ?>
                    <?php /* @var $object \Plethora\ModelCore */ ?>
                    <tr>
                        <td class="options">
                            <?php foreach($options as $option): ?>
                                <?php
                                $attributesObj = $option['attrs'];
                                /* @var $attributesObj Helper\Attributes */
                                $attributes = $attributesObj->renderAttributes([
                                    'class' => 'btn btn-xs btn-default glyphicon glyphicon-'.$option['icon'],
                                    'href'  => str_replace('{id}', $object->getId(), $option['url']),
                                    'title' => $option['title'],
                                ], TRUE, FALSE);
                                ?>
                                <a <?= $attributes ?>></a>
                            <?php endforeach ?>
                        </td>
                        <?php foreach($listColumns as $column): ?>
                            <td>
                                <?php if($object->hasLocales() && $object->getLocales()->hasField($column)): ?>
                                    <?php
                                    if(
                                        $object->getLocales() instanceof ModelCore\Locales &&
                                        $object->getLocales()->getMetadata()->hasField($column) &&
                                        $object->getLocales()->getValueForView($column) != ''
                                    ): ?>
                                        <?php $columnValue = $object->getLocales()->getValueForView($column) ?>
                                    <?php else: ?>
                                        <?php $columnValue = ' - '.__('not translated').' - ' ?>
                                    <?php endif ?>
                                <?php else: ?>
                                    <?php $columnValue = $object->getValueForView($column) ?>
                                <?php endif ?>

                                <?= $columnValue ?>
                            </td>
                        <?php endforeach ?>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <div class="box-footer">
            <?= View::factory('base/list/pages')->bind('oPager', $pager)->render() ?>
        </div>
    </div>
<?php endif ?>