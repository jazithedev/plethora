<?php
/**
 * Main View with responsibility for generating search engines form.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */

use Plethora\Form;

?>

<?php /* @var $oForm Form */ ?>

<?php
$sumitName = isset($aSubmit) ? $aSubmit['name'] : $oForm->getName().'_submit';
$submitVal = isset($aSubmit) ? $aSubmit['value'] : __('search');
$oForm->addToAttribute('class', 'form-inline');
?>

<?php if(count($oForm->getFields())): ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= __('Search engine') ?></h3>
        </div>
        <form <?php echo $oForm->renderAttributes() ?>>
            <div class="box-body">
                <?= $oForm->generateCsrf(); ?>
                <div class="search_engine_form">
                    <?php foreach($oForm->getFields() as $oField): ?>
                        <?php /* @var $oField \Plethora\Form\Field */ ?>
                        <?php echo $oField->render() ?>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="box-footer">
                <p class='submit search_engine_submit'>
                    <input class="btn btn-primary"
                           type="submit"
                           name="<?= $sumitName ?>" value="<?= $submitVal ?>" />
                    <input class="btn btn-warning"
                           type="reset"
                           name="<?= $oForm->getName().'_clear' ?>"
                           value="<?php echo __('clear') ?>" />
                </p>
            </div>
        </form>
    </div>
<?php endif; ?>