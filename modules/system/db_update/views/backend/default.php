<?php
/**
 * @author         Krzysztof Trzos
 * @package        db_update
 * @subpackage     views\backend
 * @since          1.0.1, 2014-09-23
 * @version        1.2.0-dev
 */

use Plethora\Cache;
use Plethora\Form;

?>

<?php /* @var $oForm Form */ ?>

<?php $sUpdateOutput = Cache::get('output', 'dbupdate') ?>

<?= __('Click the button below to update database.') ?>
<?= $oForm->render() ?>

<?php if(!empty($sUpdateOutput)): ?>
    <h2><?= __('Output data') ?>:</h2>
    <pre><?= $sUpdateOutput ?></pre>

    <?php Cache::clearCache('output', 'dbupdate') ?>
<?php endif ?>