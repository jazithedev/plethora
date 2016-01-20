<?php
/**
 * View for form field single language.
 *
 * @author      Krzysztof Trzos
 * @package     base
 * @subpackage  views
 * @since       1.0.0-alpha
 * @version     1.0.0-alpha
 */
?>

<?php /* @var $sLang string */ ?>
<?php /* @var $oField \Plethora\Form\Field */ ?>
<?php /* @var $aLangValues array */ ?>


<?php
$display       = ($sLang === 'und' || in_array($sLang, $oField->getFormObject()->getCheckedLanguages())) ? 'block' : 'none';
$maxQuantity   = $oField->getQuantity();
$errorsForLang = $oField->getFormObject()->getValidator()->getErrors($oField->getName().'_'.$sLang);
?>

<div class="form-field-lang form-field-lang-<?= $sLang ?>" data-lang="<?= $sLang ?>" style="display: <?= $display ?>;">
    <?php if(!empty($errorsForLang)): ?>
        <div class="form-field-errors">
            <ul class="error">
                <?php foreach($errorsForLang as $error): ?>
                    <li class="alert alert-danger">
                        <span class="glyphicon glyphicon-chevron-right"></span> <?= $error ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>
    <div class="values">
        <?php foreach($aLangValues as $i => $oneValue): /* @var $oneValue string */ ?>
            <?php $aErrors = $oField->getFormObject()->getErrorsForField($oField->getName().'.'.$sLang.'.'.$i) ?>
            <?php if(!empty($aErrors)): ?>
                <div class="form-field-errors">
                    <ul class="error">
                        <?php foreach($aErrors as $error): ?>
                            <li class="alert alert-danger">
                                <span class="glyphicon glyphicon-hand-right"></span>
                                <span class="content"><?= $error ?></span>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>
            <?php
            echo \Plethora\View::factory('base/form/field_single_value')
                ->bind('sLang', $sLang)
                ->bind('sOneValueNumber', $i)
                ->bind('sOneValueContent', $oneValue)
                ->bind('oField', $oField)
                ->render();
            ?>
        <?php endforeach ?>
    </div>
    <?php if($maxQuantity === 0 || $maxQuantity > 1): ?>
        <div class="new_value" <?php if($maxQuantity !== 0 && $maxQuantity <= ($i + 1)): ?>style="display: none"<?php endif ?>>
            <button class="btn btn-info btn-sm" type="button" data-max="<?= $maxQuantity ?>"><?= __('add value') ?></button>
        </div>
    <?php endif ?>
</div>