<?php
/**
 * This view is responsible for basic content of form fields.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views/form
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $oField \Plethora\Form\Field */ ?>
<?php /* @var $sContent string */ ?>

<?php
$classes = 'form-group form-field form-field-type-'.$oField->getType().' form-field-name-'.$oField->getName();
$styles  = '';

// hiding field
if(!$oField->isVisible() || $oField->getType() == 'hidden') {
    $styles .= ' display: none;';
}

// if has errors
if(count($oField->getErrors()) > 0) {
    $classes .= ' has_errors';
}
?>

<?= $oField->getPrefix() ?>

    <div class="<?= trim($classes) ?>" style="<?= trim($styles) ?>" data-fieldname="<?= $oField->getName() ?>">
        <?php if($oField->isLabelVisible()): ?>
            <div class="form-field-label">
                <label class="control-label" for="<?= $oField->getId() ?>">
                    <?php if(count($oField->getErrors()) > 0): ?>
                        <span class="error glyphicon glyphicon-flash"></span>
                    <?php endif ?>

                    <span class="value"><?= $oField->getLabel() ?></span>
                    <?php if($oField->getTip() != ''): ?>
                        <span class="form_tooltip glyphicon glyphicon-info-sign" title="" id="tooltip-<?= $oField->getName() ?>"></span>
                        <div id="tooltip-content-<?= $oField->getName() ?>" id="tooltip_content-<?= $oField->getName() ?>" style="display: none;">
                            <?= $oField->getTip() ?>
                        </div>
                    <?php endif ?>

                    <?php if($oField->isRequired()): ?>
                        <span class="required">*</span>
                    <?php endif ?>
                </label>
            </div>
        <?php endif ?>

        <?php if($oField->isMultilanguage()): ?>
            <div class="form-field-langbuttons">
                <?php foreach(\Plethora\Router::getLangs() as $sLang): ?>
                    <?php
                    $imgSrc  = \Plethora\Router::getBase().'/themes/_common/assets/system/languages/'.$sLang.'.jpg';
                    $imgAlt  = __('Language').' '.strtoupper($sLang);
                    $classes = 'multilang_button multilang_button_'.$sLang;

                    if($sLang == \Plethora\Router::getLang()) {
                        $classes .= ' multilang_checked';
                    }
                    ?>
                    <img class="<?= $classes ?>" data-lang="<?= $sLang ?>" src="<?= $imgSrc ?>" alt="<?= $imgAlt ?>" />
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <div class="form-field-content">
            <?= $sContent ?>
        </div>
    </div>

<?= $oField->getSuffix() ?>