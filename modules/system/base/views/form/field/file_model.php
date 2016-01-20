<?php
/**
 * Main view used to create form field to files upload.
 *
 * @author         Krzysztof Trzos
 * @package        base
 * @subpackage     views/form/field
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
?>

<?php /* @var $sLang string */ ?>
<?php /* @var $iValueNumber string */ ?>
<?php /* @var $oField \Plethora\Form\Field\FileModel */ ?>
<?php /* @var $oCurrentFile \Model\File */ ?>
<?php /* @var $oTmpFile \Model\File */ ?>

<?php
$iCurrentFileID = ($oCurrentFile instanceof \Model\File) ? $oCurrentFile->getId() : NULL;
$iTmpFileID     = ($oTmpFile instanceof \Model\File) ? $oTmpFile->getId() : NULL;
$oFileToShow    = $oTmpFile instanceof \Model\File ? $oTmpFile : $oCurrentFile;
?>

<?php if($oFileToShow instanceof \Model\File): ?>
    <?php
    $sWholeFileURL = \Plethora\Router::getBase().'/'.$oFileToShow->getFullPath();
    ?>
    <p>
        <a href="<?php echo $sWholeFileURL ?>" title=""><?php echo $sWholeFileURL ?></a>
    </p>
<?php endif ?>

<input value="" <?= $oField->getAttributes()->renderAttributes() ?>>

<?php if($oField->getQuantity() === 1 && !$oField->isRequired() && $oFileToShow instanceof \Model\File): ?>
    <button class="btn btn-danger btn-sm remove_file" type="button"><?php echo __('remove file') ?></button>
<?php endif ?>

<input value="exist" <?= $oField->renderFileExistanceAttrs($sLang, $iValueNumber) ?>>
<input value="<?= $iTmpFileID ?>" <?= $oField->renderTempFileAttrs($sLang, $iValueNumber) ?>>