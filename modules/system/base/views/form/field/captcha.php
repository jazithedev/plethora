<?php
/**
 * @author		Krzysztof Trzos
 * @package		base
 * @subpackage	views/form/field
 * @since		1.0.0-alpha
 * @version		1.0.0-alpha
 */
?>

<?php /* @var $sLang string */ ?>
<?php /* @var $iValueNumber string */ ?>
<?php /* @var $oField \Plethora\Form\Field\Captcha */ ?>

<div class="g-recaptcha" data-sitekey="<?= $oField->getPublicKey() ?>"></div>
<input type="hidden" value="true" <?= $oField->getAttributes()->renderAttributes() ?> />