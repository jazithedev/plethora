<?php
/**
 * Main View with responsibility for generating form.
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

<div class="box form form_name_<?= $oForm->getName() ?>">
    <?= $oForm->getPrefix() ?>
    <form <?= $oForm->renderAttributes() ?>>
        <div class="box-body">
            <?= $oForm->generateCsrf(); ?>
            <?php if(!$oForm->isValid()): ?>
                <div class="form-error alert alert-danger">
                    <p class="header">
                        <span class="glyphicon glyphicon-fire"></span>
                        <span class="content"><?= __('Error occured in the form') ?></span>
                    </p>
                    <?php if($oForm->getFormErrors() !== []): ?>
                        <ul>
                            <?php foreach($oForm->getFormErrors() as $sError): ?>
                                <li>
                                    <span class="glyphicon glyphicon-hand-right"></span><span class="error_content"><?= $sError ?></span>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                </div>
            <?php endif ?>

            <?php if($oForm->hasMultilangField()): ?>
                <div id="form-language-checker">
                    <div id="form-language-checker-container">
                        <?php foreach(\Plethora\Core::getLanguages() as $i => $lang): ?>
                            <?php $sIsActive = NULL ?>

                            <?php
                            if($i === 0 || in_array($lang, $oForm->getCheckedLanguages())) {
                                $sIsActive = 'active';
                            } else {
                                $sIsActive = 'disabled';
                            }
                            ?>
                            <div id="form-language-checker-single-num-<?= $i ?>" class="form-language-checker-single form-language-checker-single-<?= $lang ?><?= ' '.$sIsActive ?>">
                                <label for="form-language-checker-single-<?= $lang ?>" class="form-language-checker-single-label"><a href="javascript: void(0)"><?= $lang ?></a></label>
							<span class="form-language-checker-single-input">
								<input type="checkbox" id="form-language-checker-single-<?= $lang ?>" name="<?= $oForm->getName() ?>[form_language][<?= $lang ?>]" value="<?= $lang ?>"<?php if($i === 0): ?> disabled="disabled"<?php endif ?><?php if($sIsActive === 'active'): ?> checked="checked"<?php endif ?> />
							</span>
							<span class="form-language-checker-single-flag">
								<a href="javascript: void(0);"><img src="/themes/_common/assets/system/languages/<?= $lang ?>.jpg" alt="" /></a>
							</span>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

            <?php foreach($oForm->getFields() as $oField): /* @var $oField Form\Field */ ?>
                <?= $oField->render() ?>
            <?php endforeach ?>
        </div>
        <div class="box-footer">
            <input type="hidden" name="<?= $oForm->getSubmitName() ?>" value="<?= $oForm->getSubmitValue() ?>"> <?php // THIS IS USED TO SEND SUBMIT VALUE VIA jQuery AJAX serializeArray() / serialize() functions ?>
            <input class="submit btn btn-primary" type="submit" name="<?= $oForm->getSubmitName() ?>" value="<?= $oForm->getSubmitValue() ?>" />
        </div>
    </form>

    <?php /* THIS CODE MUST NOT BE INSIDE <FORM> { */ ?>
    <?php if($oForm->getFieldPatterns() !== []): ?>
        <div class="field_patterns" style="display: none;">
            <?php foreach($oForm->getFieldPatterns() as $sFieldName => $sPatternContent): ?>
                <div class="field-name-<?= $sFieldName ?>"><?= $sPatternContent ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <?php /* } THIS CODE MUST NOT BE INSIDE <FORM> */ ?>

    <?= $oForm->getSuffix() ?>
</div>