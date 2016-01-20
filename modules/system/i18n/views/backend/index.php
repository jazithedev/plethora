<?php
/**
 * This View shows users basic informations about cached translations.
 *
 * @author   Krzysztof Trzos
 * @since    1.2.0-dev
 * @version  1.2.0-dev
 */

use Plethora\Helper;

?>

<?php /* @var $info array */ ?>

<?php if($info === FALSE): ?>
    <p class="alert alert-danger"><?= 'Translations are not cached. To fix it, use "Reload cache" option.' ?></p>
<?php elseif(is_array($info)): ?>
    <p class="alert alert-info"><?= __('i18n.backend.main.description') ?></p>

    <div class="box">
        <table class="table">
            <thead>
            <tr>
                <th class="col-md-3"><?= __('i18n.backend.table.label') ?></th>
                <th class="col-md-9"><?= __('i18n.backend.table.val') ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?= __('Last reload date') ?>:</td>
                <td><?= Helper\Arrays::get($info, 'date') ?></td>
            </tr>
            <tr>
                <td><?= __('Available languages') ?>:</td>
                <td><?= implode(', ', Helper\Arrays::get($info, 'langs')) ?></td>
            </tr>
            <tr>
                <td><?= __('Translations amount per lang') ?>:</td>
                <td>
                    <?php
                    $amount = Helper\Arrays::get($info, 'amount', []);
                    ?>
                    <?php foreach($amount as $lang => $amount): ?>
                        <p><b><?= $lang ?></b>: <?= $amount ?></p>
                    <?php endforeach ?>
                </td>
            </tr>
            <tr>
                <td><?= __('Translations per application part') ?>:</td>
                <td>
                    <?php $amountPerPart = Helper\Arrays::get($info, 'amount_per_part', []) ?>
                    <?php //dd($amountPerPart) ?>
                    <?php foreach($amountPerPart as $part => $data): ?>
                        <?php
                        $type = NULL;

                        switch($part) {
                            case 'fw':
                                $type = 'Plethora';
                                break;
                            case 'app':
                                $type = __('Application');
                                break;
                            case 'module':
                                $type = __('Modules');
                                break;
                        }
                        ?>
                        <p><b><?= $type ?></b></p>
                        <?php if($part === 'module'): ?>
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>name</th>
                                    <?php foreach(\Plethora\Router::getLangs() as $lang): ?>
                                        <th><?= $lang ?></th>
                                    <?php endforeach ?>
                                </tr>
                                <?php foreach($data as $module => $dataPerModule): ?>
                                    <tr>
                                        <td><?= $module ?></td>
                                        <?php foreach($dataPerModule as $lang => $counted): ?>
                                            <td style="font-weight: bold;"><?= $counted ?></td>
                                        <?php endforeach ?>
                                    </tr>
                                <?php endforeach ?>
                            </table>
                        <?php else: ?>
                            <ul>
                                <?php foreach($data as $lang => $counted): ?>
                                    <li>
                                        <?= $lang ?>: <?= $counted ?>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        <?php endif ?>
                    <?php endforeach ?>
                </td>
            </tr>
            </tbody>
    </div>
    </table>
<?php endif ?>
